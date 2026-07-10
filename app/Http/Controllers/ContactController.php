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
                'description' => 'Liên hệ với Happy Ecotech.',
            ]),
        ]);
    }

    public function submit(ContactSubmissionRequest $request)
    {
        // Honeypot đã điền => coi như bot: giả thành công, không lưu, không báo lỗi.
        if (filled($request->input('website'))) {
            return redirect()->route('contact')->with('contact_success', true)->withFragment('contact-form');
        }

        $submission = ContactSubmission::create([
            'name' => $request->string('name'),
            'phone' => $request->string('phone'),
            'email' => $request->input('email'),
            'company' => $request->input('company'),
            'service_id' => $request->input('service_id'),
            'message' => $request->string('message'),
            'consent' => $request->boolean('consent'),
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $this->notify($submission);

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
                .'Dịch vụ quan tâm: '.(optional($submission->service)->name ?? '—')."\n"
                ."Nội dung:\n{$submission->message}\n";

            Mail::raw($body, function ($mail) use ($to) {
                $mail->to($to)->subject('Lead mới từ website Happy Ecotech');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
