<?php

/*
 * Nội dung trang đích theo giải pháp (H1 theo intent, lợi ích, cách hoạt động, FAQ).
 * Chỉ là mặc định theo slug khi CMS chưa có dữ liệu riêng: nếu model Service có thuộc tính
 * `h1`, `benefits`, `steps`, `faq` thì view ưu tiên dữ liệu CMS. Không chứa số liệu/khách hàng/chứng chỉ.
 */

return [
    'smartlabel' => [
        'h1' => 'SmartLabel: tem điện tử xác thực nguồn gốc và chất lượng sản phẩm',
        'benefits' => [
            ['title' => 'Người mua tự kiểm tra', 'text' => 'Quét mã QR là xem được nguồn gốc, thông tin kiểm định và chất lượng sản phẩm.'],
            ['title' => 'Quản lý kiểm định tập trung', 'text' => 'Điện tử hóa việc đánh dấu và quản lý chất lượng, thay cho sổ sách và tem giấy rời rạc.'],
            ['title' => 'Tăng niềm tin thương hiệu', 'text' => 'Thông tin minh bạch giúp sản phẩm của bạn khác biệt trước hàng không rõ nguồn gốc.'],
        ],
        'steps' => [
            ['title' => 'Khai báo sản phẩm', 'text' => 'Nhập thông tin sản phẩm, lô hàng và kết quả kiểm định.'],
            ['title' => 'Gắn tem điện tử', 'text' => 'Mỗi sản phẩm hoặc lô hàng gắn một mã QR duy nhất.'],
            ['title' => 'Người mua quét mã', 'text' => 'Xem thông tin xác thực ngay trên điện thoại.'],
        ],
    ],
    // TaskFlow: chỉ giữ lợi ích khớp mô tả seed (tối ưu quy trình, giảm sai sót, nâng cao hiệu suất). Chưa có quy trình đã xác nhận.
    'taskflow' => [
        'h1' => 'TaskFlow: quản lý dự án và công việc cho đội nhóm',
        'benefits' => [
            ['title' => 'Tối ưu quy trình làm việc', 'text' => 'Quản lý dự án và công việc trên một nền tảng thống nhất.'],
            ['title' => 'Giảm thiểu sai sót', 'text' => 'Quy trình rõ ràng giúp hạn chế bỏ sót và trùng lặp.'],
            ['title' => 'Nâng cao hiệu suất', 'text' => 'Đội nhóm theo dõi được công việc và tiến độ của mình.'],
        ],
    ],
    // MiniCRM / Farm2Home: chỉ giữ H1 theo mô tả seed. Các bước/lợi ích CHƯA được chủ sản phẩm xác nhận nên ẩn.
    'minicrm' => ['h1' => 'MiniCRM: quản lý khách hàng và mối quan hệ khách hàng'],
    'farm2home' => ['h1' => 'Farm2Home: kết nối trực tiếp nông dân với người tiêu dùng'],
];
