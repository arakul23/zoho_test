<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class ZohoAuthController extends Controller
{
    /**
     * Шаг 1: Перенаправление админа на страницу авторизации Zoho
     */
    public function redirect()
    {
        $query = http_build_query([
            'client_id'     => config('services.zoho.client_id'),
            'response_type' => 'code',
            'scope'         => 'ZohoCRM.modules.ALL', // нужные права
            'redirect_uri'  => route('zoho.callback'),
            'access_type'   => 'offline', // ОБЯЗАТЕЛЬНО для получения refresh_token
            'prompt'        => 'consent', // Принудительный запрос прав
        ]);

        return redirect("https://accounts.zoho.eu/oauth/v2/auth?{$query}");
    }

    /**
     * Шаг 2: Обработка возврата от Zoho и сохранение refresh_token в Redis
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');

        if (! $code) {
            return response()->json(['error' => 'Authorization code not provided'], 400);
        }

        // Запрос на получение первой пары токенов
        $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'redirect_uri'  => route('zoho.callback'),
            'code'          => $code,
        ]);

        $data = $response->json();

        if (empty($data['refresh_token'])) {
            return response()->json([
                'error' => 'Failed to obtain refresh token',
                'details' => $data
            ], 400);
        }

        // Сохраняем refresh_token в Redis БЕЗ TTL (бессрочно)
        Redis::connection('zoho')->set('zoho:refresh_token', $data['refresh_token']);

        // Сразу сохраняем и полученный access_token
        $ttl = max(($data['expires_in'] ?? 3600) - 300, 60);
        Redis::connection('zoho')->setex('zoho:access_token', $ttl, $data['access_token']);

        return response()->json(['status' => 'Zoho authorization successfully initialized!']);
    }
}
