@extends('layouts.public', ['seo' => he_seo(['title' => 'Không tìm thấy trang', 'robots' => 'noindex,follow'])])

@section('content')
    <section class="section">
        <div class="container-page max-w-2xl text-center">
            <p class="text-display text-brand-600" aria-hidden="true">404</p>
            <h1 class="mt-4 text-h2">Không tìm thấy trang bạn cần</h1>
            <p class="mt-4 text-lead text-ink-700">Đường dẫn có thể đã thay đổi hoặc không còn tồn tại. Bạn có thể quay về trang chủ hoặc xem các giải pháp của chúng tôi.</p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg">Về trang chủ</a>
                <a href="{{ route('services.index') }}" class="btn btn-outline btn-lg">Xem giải pháp</a>
            </div>
        </div>
    </section>
@endsection
