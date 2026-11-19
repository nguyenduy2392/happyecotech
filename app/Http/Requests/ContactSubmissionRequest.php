<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if (filled($this->input('website'))) {
            return ['website' => ['required', 'string', 'max:255']];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'solution' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'in:contact-form,lead-form'],
            'message' => ['required_unless:source,lead-form', 'nullable', 'string', 'max:5000'],
            'consent' => ['nullable', 'accepted'],
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
            'message.required_unless' => 'Vui lòng nhập nội dung.',
        ];
    }
}
