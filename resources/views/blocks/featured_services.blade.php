{{-- Dùng ngoài trang chủ: TaskFlow (chủ lực) → MiniCRM → giải pháp khác. Trang chủ tách thành 3 block ảo trong public/page.blade.php. --}}
@include('blocks.product_taskflow', ['data' => []])
@include('blocks.product_minicrm', ['data' => []])
@include('blocks.other_solutions', ['data' => $data ?? []])
