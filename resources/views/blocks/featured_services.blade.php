{{-- Dùng ngoài trang chủ: TaskFlow → MiniCRM → các giải pháp còn lại. Trang chủ tách thành các block ảo trong public/page.blade.php. --}}
@include('blocks.product_taskflow', ['data' => []])
@include('blocks.product_minicrm', ['data' => []])
@include('blocks.other_solutions', ['data' => $data ?? []])
