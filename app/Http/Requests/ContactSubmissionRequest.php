<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactSubmissionRequest extends FormRequest
{
    public static function normalizeIdempotencyKey(mixed $key): ?string
    {
        return is_string($key) && preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/i', $key)
            ? strtolower($key)
            : null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['idempotency_key' => self::normalizeIdempotencyKey($this->input('idempotency_key'))]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function wantsSubmissionJson(): bool
    {
        return $this->expectsJson() || $this->ajax();
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->wantsSubmissionJson()) {
            throw new HttpResponseException(response()->json([
                'ok' => false,
                'errors' => $validator->errors()->messages(),
            ], 422));
        }

        parent::failedValidation($validator);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->attributes->get('contact.replay') === true) {
            return [];
        }
        if (filled($this->input('website'))) {
            return ['website' => ['required', 'string', 'max:255']];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/\A(?:0|\+84)[ .]*[1-9](?:[ .]*[0-9]){8,9}\z/'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'solution' => ['nullable', 'string', 'max:255', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/'],
            'source' => ['nullable', 'in:contact-form,lead-form'],
            'idempotency_key' => ['nullable', 'uuid:4'],
            'message' => ['required_unless:source,lead-form', 'nullable', 'string', 'max:5000'],
            'consent' => $this->input('source') === 'lead-form' ? ['required', 'accepted'] : ['sometimes', 'accepted'],
            // Honeypot: bot thường điền, người dùng thật để trống.
            'website' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'họ tên',
            'phone' => 'số điện thoại',
            'email' => 'email',
            'company' => 'công ty',
            'service_id' => 'dịch vụ quan tâm',
            'message' => 'nội dung',
            'consent' => 'đồng ý chính sách',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'email' => 'Email không hợp lệ.',
            'consent.accepted' => 'Bạn cần đồng ý với chính sách dữ liệu.',
            'consent.required' => 'Bạn cần đồng ý với chính sách dữ liệu.',
            'phone.regex' => 'Nhập số điện thoại Việt Nam bắt đầu bằng 0 hoặc +84.',
            'message.required_unless' => 'Vui lòng nhập nội dung.',
        ];
    }
}
