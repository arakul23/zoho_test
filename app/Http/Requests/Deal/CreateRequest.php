<?php

declare(strict_types=1);

namespace App\Http\Requests\Deal;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account.name' => 'required|string|max:100',
            'account.website' => 'nullable|url',
            'account.phone' => 'required|string',
            'deal.name' => 'required|string|max:100',
            'deal.stage' => 'required|string',
            'deal.closing_date' => 'required|date',
        ];
    }
}
