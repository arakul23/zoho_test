<template>
    <div class="zoho-form">
        <h2 class="zoho-form__title">Create Zoho CRM Deal</h2>

        <div class="zoho-form__tabs" role="tablist">
            <button
                type="button"
                role="tab"
                :aria-selected="activeTab === 'account'"
                :class="[
                    'zoho-form__tab',
                    { 'zoho-form__tab--active': activeTab === 'account' },
                ]"
                @click="activeTab = 'account'"
            >
                1. Account
            </button>
            <button
                type="button"
                role="tab"
                :aria-selected="activeTab === 'deal'"
                :class="[
                    'zoho-form__tab',
                    { 'zoho-form__tab--active': activeTab === 'deal' },
                ]"
                @click="activeTab = 'deal'"
            >
                2. Deal
            </button>
        </div>

        <form @submit.prevent="handleSubmit" novalidate>
            <section
                v-show="activeTab === 'account'"
                class="zoho-form__section"
            >
                <div class="zoho-form__field">
                    <label for="account_name">Account name *</label>
                    <input
                        id="account_name"
                        v-model.trim="account.name"
                        type="text"
                        :class="{ 'is-invalid': errors.account_name }"
                        @blur="validateField('account_name')"
                    />
                    <span v-if="errors.account_name" class="zoho-form__error">{{
                        errors.account_name
                    }}</span>
                </div>

                <div class="zoho-form__field">
                    <label for="account_website">Website</label>
                    <input
                        id="account_website"
                        v-model.trim="account.website"
                        type="text"
                        placeholder="https://example.com"
                        :class="{ 'is-invalid': errors.account_website }"
                        @blur="validateField('account_website')"
                    />
                    <span
                        v-if="errors.account_website"
                        class="zoho-form__error"
                        >{{ errors.account_website }}</span
                    >
                </div>

                <div class="zoho-form__field">
                    <label for="account_phone">Phone *</label>
                    <input
                        id="account_phone"
                        v-model.trim="account.phone"
                        type="tel"
                        placeholder="+380 XX XXX XX XX"
                        :class="{ 'is-invalid': errors.account_phone }"
                        @blur="validateField('account_phone')"
                    />
                    <span
                        v-if="errors.account_phone"
                        class="zoho-form__error"
                        >{{ errors.account_phone }}</span
                    >
                </div>
            </section>

            <section v-show="activeTab === 'deal'" class="zoho-form__section">
                <div class="zoho-form__field">
                    <label for="deal_name">Deal name *</label>
                    <input
                        id="deal_name"
                        v-model.trim="deal.name"
                        type="text"
                        :class="{ 'is-invalid': errors.deal_name }"
                        @blur="validateField('deal_name')"
                    />
                    <span v-if="errors.deal_name" class="zoho-form__error">{{
                        errors.deal_name
                    }}</span>
                </div>

                <div class="zoho-form__field">
                    <label for="deal_stage">Deal stage *</label>
                    <select
                        id="deal_stage"
                        v-model="deal.stage"
                        :class="{ 'is-invalid': errors.deal_stage }"
                        @change="validateField('deal_stage')"
                    >
                        <option value="" disabled>Select a stage</option>
                        <option
                            v-for="stage in dealStages"
                            :key="stage"
                            :value="stage"
                        >
                            {{ stage }}
                        </option>
                    </select>
                    <span v-if="errors.deal_stage" class="zoho-form__error">{{
                        errors.deal_stage
                    }}</span>
                </div>

                <div class="zoho-form__field">
                    <label for="closing_date">Closing date *</label>
                    <input
                        id="closing_date"
                        v-model="deal.closingDate"
                        type="date"
                        :class="{ 'is-invalid': errors.closing_date }"
                        @blur="validateField('closing_date')"
                    />
                    <span v-if="errors.closing_date" class="zoho-form__error">{{
                        errors.closing_date
                    }}</span>
                </div>
            </section>

            <div class="zoho-form__submit-row">
                <button
                    type="submit"
                    class="zoho-form__btn zoho-form__btn--primary"
                    :disabled="submitting"
                >
                    {{ submitting ? 'Creating…' : 'Create Account & Deal' }}
                </button>
            </div>

            <p
                v-if="successMessage"
                class="zoho-form__alert zoho-form__alert--success"
            >
                {{ successMessage }}
            </p>
            <p
                v-if="submitError"
                class="zoho-form__alert zoho-form__alert--error"
            >
                {{ submitError }}
            </p>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

const dealStages = [
    'Qualification',
    'Needs Analysis',
    'Value Proposition',
    'Proposal/Price Quote',
    'Negotiation/Review',
    'Closed Won',
    'Closed Lost',
];

const activeTab = ref('account');
const submitting = ref(false);
const successMessage = ref('');
const submitError = ref('');

const account = reactive({
    name: '',
    website: '',
    phone:  '',
});

const deal = reactive({
    name: '',
    stage: '',
    closingDate: '',
});

const errors = reactive({
    account_name: '',
    account_website: '',
    account_phone: '',
    deal_name: '',
    deal_stage: '',
    closing_date: '',
});

const urlPattern = /^https?:\/\/[\w-]+(\.[\w-]+)+[/#?]?.*$/i;
const phonePattern = /^[+()\d][\d\s()+-]{5,20}$/;

function validateField(field) {
    switch (field) {
        case 'account_name':
            errors.account_name = account.name
                ? ''
                : 'Account name is required.';
            break;
        case 'account_website':
            errors.account_website =
                account.website && !urlPattern.test(account.website)
                    ? 'Enter a valid URL, e.g. https://example.com'
                    : '';
            break;
        case 'account_phone':
            errors.account_phone = !account.phone
                ? 'Phone is required.'
                : !phonePattern.test(account.phone)
                  ? 'Enter a valid phone number.'
                  : '';
            break;
        case 'deal_name':
            errors.deal_name = deal.name ? '' : 'Deal name is required.';
            break;
        case 'deal_stage':
            errors.deal_stage = deal.stage ? '' : 'Deal stage is required.';
            break;
        case 'closing_date':
            errors.closing_date = deal.closingDate
                ? ''
                : 'Closing date is required.';
            break;
    }
}

function validateAll() {
    [
        'account_name',
        'account_website',
        'account_phone',
        'deal_name',
        'deal_stage',
        'closing_date',
    ].forEach(validateField);

    return Object.values(errors).every((e) => !e);
}

async function handleSubmit() {
    successMessage.value = '';
    submitError.value = '';

    if (!validateAll()) {
        if (
            errors.account_name ||
            errors.account_website ||
            errors.account_phone
        ) {
            activeTab.value = 'account';
        } else {
            activeTab.value = 'deal';
        }

        return;
    }

    submitting.value = true;

    try {
        const payload = {
            account: {
                name: account.name,
                website: account.website || null,
                phone: account.phone,
            },
            deal: {
                name: deal.name,
                stage: deal.stage,
                closing_date: deal.closingDate,
            },
        };

        const response = await fetch('/api/zoho/deals', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message ||
                    'Failed to create the records in Zoho CRM. Please try again.',
            );
        }

        successMessage.value = `Created successfully. Account ID: ${data.account_id}, Deal ID: ${data.deal_id}.`;
        resetForm();
    } catch (e) {
        submitError.value =
            e.message ||
            'Failed to create the records in Zoho CRM. Please try again.';
    } finally {
        submitting.value = false;
    }
}

function resetForm() {
    account.name = '';
    account.website = '';
    account.phone = '';
    deal.name = '';
    deal.stage = '';
    deal.closingDate = '';
    Object.keys(errors).forEach((k) => (errors[k] = ''));
}
</script>

<style scoped>
.zoho-form {
    max-width: 480px;
    margin: 0 auto;
    padding: 24px;
    font-family:
        system-ui,
        -apple-system,
        sans-serif;
}

.zoho-form__title {
    font-size: 1.25rem;
    margin-bottom: 16px;
}

.zoho-form__tabs {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}

.zoho-form__tab {
    flex: 1;
    padding: 10px 12px;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-size: 0.95rem;
    color: #666;
}

.zoho-form__tab--active {
    color: #d1481f;
    border-bottom-color: #d1481f;
    font-weight: 600;
}

.zoho-form__section {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.zoho-form__field {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.zoho-form__field label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #333;
}

.zoho-form__field input,
.zoho-form__field select {
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 0.95rem;
}

.zoho-form__field input.is-invalid,
.zoho-form__field select.is-invalid {
    border-color: #d32f2f;
}

.zoho-form__error {
    font-size: 0.8rem;
    color: #d32f2f;
}

.zoho-form__nav {
    display: flex;
    justify-content: flex-end;
    margin-top: 4px;
}

.zoho-form__submit-row {
    margin-top: 24px;
}

.zoho-form__btn {
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 0.95rem;
    cursor: pointer;
    border: 1px solid transparent;
}

.zoho-form__btn--primary {
    background: #d1481f;
    color: #fff;
    width: 100%;
}

.zoho-form__btn--primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.zoho-form__btn--secondary {
    background: #f2f2f2;
    color: #333;
    border-color: #ddd;
}

.zoho-form__alert {
    margin-top: 14px;
    padding: 10px 12px;
    border-radius: 6px;
    font-size: 0.9rem;
}

.zoho-form__alert--success {
    background: #e6f4ea;
    color: #1e7e34;
}

.zoho-form__alert--error {
    background: #fdecea;
    color: #d32f2f;
}
</style>
