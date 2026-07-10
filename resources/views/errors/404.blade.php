@extends('layouts.public')

@section('content')
    <section class="error-section pt-150 pb-150">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 text-center">
                    <h1 class="error-code">404</h1>
                    <h2 class="mb-15">Không tìm thấy trang</h2>
                    <p class="mb-30">Trang bạn tìm không tồn tại hoặc đã được di chuyển.</p>
                    <a href="{{ route('home') }}" class="main-btn btn-hover">Về trang chủ</a>
                </div>
            </div>
        </div>
    </section>
@endsection
