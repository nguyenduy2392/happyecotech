<?php

namespace Database\Seeders;

final class LandingCopy
{
    public const HOME = [
        'faq' => [
            'heading' => 'Câu hỏi thường gặp',
            'subheading' => 'Tìm hiểu TaskFlow, MiniCRM và các giải pháp khác của Happy Ecotech.',
            'items' => [
                [
                    'question' => 'TaskFlow là gì và dành cho ai?',
                    'answer' => 'TaskFlow giúp đội nhóm quản lý công việc theo dự án. Nhóm có thể giao đầu việc, đặt ngày kết thúc và theo dõi trạng thái.',
                ],
                [
                    'question' => 'TaskFlow có ứng dụng trên điện thoại không?',
                    'answer' => 'Có. Bạn có thể tải TaskFlow trên iOS qua App Store hoặc trên Android qua Google Play.',
                ],
                [
                    'question' => 'Ai có thể xem và cập nhật công việc trong TaskFlow?',
                    'answer' => 'Quyền xem chi tiết công việc được kiểm tra theo cấu hình vai trò. Người thực hiện có thể cập nhật trạng thái của công việc được giao.',
                ],
                [
                    'question' => 'MiniCRM giúp đội kinh doanh theo dõi những gì?',
                    'answer' => 'MiniCRM lưu hồ sơ khách hàng, giai đoạn bán hàng và lịch sử tương tác. Khi ghi nhận một lần trao đổi, bạn có thể lưu ngày hẹn và bước xử lý tiếp theo.',
                ],
                [
                    'question' => 'SmartLabel hỗ trợ truy xuất nguồn gốc như thế nào?',
                    'answer' => 'SmartLabel dùng tem QR để người mua xem thông tin nguồn gốc và kiểm định của sản phẩm.',
                ],
            ],
        ],
    ];

    public const LEGACY_HOME = [
        'faq' => [
            'heading' => 'Câu hỏi thường gặp',
            'subheading' => 'Những điều doanh nghiệp, hợp tác xã và đơn vị hành chính hay hỏi trước khi bắt đầu.',
            'items' => [
                [
                    'question' => 'Tem điện tử và truy xuất nguồn gốc dành cho những ai?',
                    'answer' => 'Dành cho doanh nghiệp vừa và nhỏ, hợp tác xã và hộ sản xuất cần cho người mua kiểm tra nguồn gốc, chất lượng sản phẩm bằng mã QR. SmartLabel là giải pháp của Happy Ecotech cho nhu cầu này.',
                ],
                [
                    'question' => 'Doanh nghiệp nhỏ hoặc hợp tác xã có triển khai được không?',
                    'answer' => 'Được. Giải pháp được thiết kế để triển khai gọn, bắt đầu từ vài sản phẩm rồi mở rộng dần. Thời gian và chi phí phụ thuộc quy mô, vì vậy chúng tôi tư vấn lộ trình cụ thể sau khi hiểu nhu cầu của bạn.',
                ],
                [
                    'question' => 'Chi phí được tính như thế nào?',
                    'answer' => 'Chi phí phụ thuộc số lượng sản phẩm, quy mô vận hành và phạm vi tích hợp. Để lại số điện thoại, đội ngũ sẽ liên hệ trao đổi và gửi báo giá phù hợp.',
                ],
                [
                    'question' => 'TaskFlow có ứng dụng trên điện thoại không?',
                    'answer' => 'Có. TaskFlow có ứng dụng trên App Store và Google Play, bạn có thể cài đặt trực tiếp từ mục Ứng dụng ở trang này.',
                ],
                [
                    'question' => 'Happy Ecotech có giải pháp cho hành chính số cấp xã, phường không?',
                    'answer' => 'Có. Hệ sinh thái hành chính số gồm DataHub Văn thư số AI và OMC dành cho cấp xã, phường. Hãy liên hệ để được tư vấn và trải nghiệm demo theo nhu cầu của đơn vị.',
                ],
            ],
        ],
    ];

    public const SERVICES = [
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
        ],
    ];
}
