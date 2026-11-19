<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactSubmissionRequest;
use App\Models\ContactSubmission;
use App\Models\Service;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('public.contact', [
            'services' => Service::query()->published()->ordered()->get(),
            'seo' => he_seo([
                'title' => 'Liên hệ',
                'og_image' => seo_url('/assets/img/v2/og-about-contact.webp'),
            ]),
        ]);
    }

    public function submit(ContactSubmissionRequest $request)
    {
        // Honeypot đã điền => coi như bot: giả thành công, không lưu, không báo lỗi.
        if ($request->attributes->get('contact.replay') === true || filled($request->input('website'))) {
            return $this->submissionAccepted($request);
        }

        $data = $request->validated();
        $service = isset($data['service_id']) ? Service::published()->find($data['service_id']) : null;
        $attributes = [
            'name' => $request->string('name'),
            'phone' => $request->string('phone'),
            'email' => $request->input('email'),
            'company' => $request->input('company'),
            'service_id' => $service?->id,
            'service_interest' => $service?->name ?? ($data['solution'] ?? null),
            'source' => $data['source'] ?? 'contact-form',
            'message' => $data['message'] ?? null,
            'consent' => $request->boolean('consent'),
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ];
        $key = $data['idempotency_key'] ?? null;
        $submission = $key === null
            ? ContactSubmission::create($attributes)
            : ContactSubmission::withTrashed()->firstOrCreate(['idempotency_key' => strtolower($key)], $attributes);
        if ($submission->wasRecentlyCreated) {
            $this->notify($submission);
        }

        return $this->submissionAccepted($request);
    }

    private function submissionAccepted(ContactSubmissionRequest $request)
    {
        if ($request->wantsSubmissionJson()) {
            return response()->json(['ok' => true, 'message' => 'Cảm ơn bạn. Happy Ecotech đã nhận được thông tin và sẽ sớm liên hệ.']);
        }

        return redirect()->route('contact')
            ->with('contact_success', true)
            ->withFragment('contact-form');
    }

    /**
     * Gửi email thông báo lead (best-effort). Lỗi SMTP không được làm hỏng việc lưu DB.
     */
    private function notify(ContactSubmission $submission): void
    {
        $to = config('happyecotech.contact_notify_to') ?: setting('contact_email');

        if (blank($to)) {
            return;
        }

        try {
            $body = "Lead mới từ website Happy Ecotech\n\n"
                ."Họ tên: {$submission->name}\n"
                ."Điện thoại: {$submission->phone}\n"
                ."Email: {$submission->email}\n"
                ."Công ty: {$submission->company}\n"
                .'Dịch vụ quan tâm: '.($submission->service_interest ?? '—')."\n"
                ."Nội dung:\n{$submission->message}\n";

            Mail::raw($body, function ($mail) use ($to) {
                $mail->to($to)->subject('Lead mới từ website Happy Ecotech');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
