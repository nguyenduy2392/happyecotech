<?php

/*
 * Dữ liệu mặc định CHỈ dùng khi trang chủ chưa có block tương ứng trong CMS.
 * Khi admin thêm block `faq`, dữ liệu trong CMS luôn thắng. Không có số liệu/khách hàng/chứng chỉ.
 */

return [
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
