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
    'taskflow' => [
        'h1' => 'TaskFlow: quản lý dự án và công việc cho đội nhóm',
        'benefits' => [
            ['title' => 'Giao việc rõ ràng', 'text' => 'Mỗi đầu việc có người phụ trách, hạn xử lý và trạng thái minh bạch.'],
            ['title' => 'Theo dõi tiến độ', 'text' => 'Nắm tình hình dự án mà không phải hỏi từng người.'],
            ['title' => 'Giảm sai sót', 'text' => 'Quy trình làm việc thống nhất giúp hạn chế bỏ sót và trùng lặp.'],
        ],
        'steps' => [
            ['title' => 'Tạo dự án', 'text' => 'Khai báo dự án và thành viên tham gia.'],
            ['title' => 'Giao và nhận việc', 'text' => 'Chia nhỏ công việc, giao cho từng người, đặt hạn xử lý.'],
            ['title' => 'Theo dõi và hoàn tất', 'text' => 'Cập nhật trạng thái trên điện thoại, xem tiến độ tổng thể.'],
        ],
    ],
    'minicrm' => [
        'h1' => 'MiniCRM: quản lý khách hàng gọn nhẹ cho doanh nghiệp nhỏ',
        'benefits' => [
            ['title' => 'Thông tin khách hàng tập trung', 'text' => 'Một nơi lưu hồ sơ và lịch sử trao đổi, không thất lạc khi đổi người phụ trách.'],
            ['title' => 'Chăm sóc đúng lúc', 'text' => 'Theo dõi từng khách hàng để không bỏ lỡ cơ hội.'],
            ['title' => 'Dễ dùng', 'text' => 'Gọn nhẹ, phù hợp đội ngũ nhỏ chưa có quy trình CRM phức tạp.'],
        ],
        'steps' => [
            ['title' => 'Nhập danh sách khách hàng', 'text' => 'Đưa thông tin khách hàng hiện có vào hệ thống.'],
            ['title' => 'Ghi nhận trao đổi', 'text' => 'Lưu lại từng lần liên hệ và việc cần làm tiếp theo.'],
            ['title' => 'Theo dõi và chăm sóc', 'text' => 'Xem danh sách khách cần liên hệ lại và kết quả chăm sóc.'],
        ],
    ],
    'farm2home' => [
        'h1' => 'Farm2Home: kết nối trực tiếp nông dân với người tiêu dùng',
        'benefits' => [
            ['title' => 'Đi thẳng từ nông trại', 'text' => 'Rút ngắn khâu trung gian giữa người sản xuất và người mua.'],
            ['title' => 'Biết rõ nguồn gốc', 'text' => 'Người mua nắm được sản phẩm đến từ đâu.'],
            ['title' => 'Mở thêm kênh bán', 'text' => 'Nông dân và hợp tác xã có thêm kênh tiếp cận khách hàng.'],
        ],
        'steps' => [
            ['title' => 'Nông dân đăng sản phẩm', 'text' => 'Cập nhật sản phẩm và thông tin nguồn gốc.'],
            ['title' => 'Người mua lựa chọn', 'text' => 'Xem sản phẩm và đặt mua trực tiếp.'],
            ['title' => 'Giao nhận', 'text' => 'Sản phẩm đến tay người tiêu dùng.'],
        ],
    ],
];
