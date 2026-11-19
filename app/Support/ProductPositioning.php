<?php

namespace App\Support;

final class ProductPositioning
{
    public const HOME_TITLE = 'TaskFlow và MiniCRM | Happy Ecotech';

    public const HOME_DESCRIPTION = 'TaskFlow giúp đội nhóm giao việc và theo dõi tiến độ. MiniCRM giúp đội kinh doanh quản lý khách hàng, giai đoạn bán hàng và lịch chăm sóc.';

    public const ORGANIZATION_DESCRIPTION = 'Happy Ecotech phát triển TaskFlow để quản lý công việc, MiniCRM để chăm sóc khách hàng và các giải pháp số cho những nhu cầu vận hành khác.';

    public const ORDER = ['taskflow' => 1, 'minicrm' => 2, 'smartlabel' => 3, 'farm2home' => 4];

    public const HERO = [
        'h1' => 'Quản lý công việc với TaskFlow để đội nhóm giao việc rõ ràng và theo dõi tiến độ',
        'heading' => 'Quản lý công việc với TaskFlow để đội nhóm giao việc rõ ràng và theo dõi tiến độ',
        'subheading' => 'Giao việc cho từng người, đặt hạn xử lý và xem trạng thái trong TaskFlow. Dùng MiniCRM để lưu hồ sơ và theo dõi lịch chăm sóc khách hàng.',
        'cta_text' => 'Khám phá TaskFlow', 'cta_url' => '/dich-vu/taskflow',
        'secondary_cta_text' => 'Tìm hiểu MiniCRM', 'secondary_cta_url' => '/dich-vu/minicrm',
        'art' => 'hero-taskflow', 'image_alt' => 'Màn hình TaskFlow minh họa danh sách và chi tiết công việc.',
    ];

    public const GROUPS = [
        ['title' => 'Giao việc và chăm sóc khách hàng', 'icon' => 'workflow', 'art' => 'group-operations', 'description' => 'TaskFlow quản lý công việc. MiniCRM quản lý khách hàng và lịch chăm sóc.', 'slugs' => ['taskflow', 'minicrm']],
        ['title' => 'Truy xuất, nông sản và hành chính số', 'icon' => 'layers', 'art' => 'group-traceability', 'description' => 'Các giải pháp cho truy xuất nguồn gốc, kết nối nông sản và hành chính số.', 'slugs' => ['smartlabel', 'farm2home', 'datahub', 'omc']],
    ];

    // TaskFlow: DashboardTasks, TaskDetail, ProjectDetail và Comments.
    // MiniCRM: README, customer-list, customer-pipeline-modal và customer-detail.
    public const PRODUCTS = [
        'taskflow' => [
            'h1' => 'TaskFlow giúp đội nhóm quản lý công việc từ lúc giao đến khi bàn giao',
            'seo_title' => 'TaskFlow – Quản lý công việc và dự án cho đội nhóm',
            'seo_description' => 'Dùng TaskFlow để giao việc, đặt hạn xử lý, theo dõi trạng thái và trao đổi ngay trên đầu việc. Xem danh sách việc được giao theo khoảng ngày.',
            'short_description' => 'TaskFlow giúp đội nhóm giao việc theo dự án, đặt hạn xử lý, cập nhật trạng thái và trao đổi ngay trên từng đầu việc.',
            'benefits' => [
                ['title' => 'Biết ai đang xử lý', 'text' => 'Xem người thực hiện, hạn xử lý và trạng thái trên từng đầu việc.'],
                ['title' => 'Tìm nhanh việc cần làm', 'text' => 'Lọc danh sách theo nhóm việc được giao và khoảng ngày.'],
                ['title' => 'Giữ trao đổi cùng đầu việc', 'text' => 'Dùng bình luận và danh sách kiểm tra trong chi tiết công việc.'],
            ],
            'steps' => [
                ['title' => 'Tạo đầu việc trong dự án', 'text' => 'Nhập nội dung, chọn người thực hiện và đặt ngày kết thúc.'],
                ['title' => 'Thực hiện và trao đổi', 'text' => 'Cập nhật trạng thái và trao đổi qua bình luận ngay trên đầu việc.'],
                ['title' => 'Rà soát danh sách công việc', 'text' => 'Lọc theo khoảng ngày để rà soát việc chưa xong hoặc sắp đến hạn.'],
            ],
            'faq' => [
                ['question' => 'TaskFlow có ứng dụng trên điện thoại không?', 'answer' => 'Có. TaskFlow có ứng dụng trên App Store và Google Play. Bạn có thể mở liên kết tải ứng dụng ở đầu trang.'],
                ['question' => 'Có thể xem các đầu việc sắp đến hạn không?', 'answer' => 'Có. Danh sách công việc có bộ lọc việc sắp đến hạn và bộ chọn khoảng ngày.'],
            ],
        ],
        'minicrm' => [
            'h1' => 'MiniCRM giúp đội kinh doanh quản lý khách hàng và theo dõi lần chăm sóc tiếp theo',
            'seo_title' => 'MiniCRM – Quản lý khách hàng và lịch chăm sóc',
            'seo_description' => 'MiniCRM tập trung hồ sơ khách hàng, giai đoạn bán hàng và nhật ký tương tác. Đội kinh doanh có thể ghi ngày hẹn và bước chăm sóc tiếp theo.',
            'short_description' => 'MiniCRM tập trung hồ sơ khách hàng, giai đoạn bán hàng và lịch chăm sóc để đội kinh doanh theo dõi từng mối quan hệ.',
            'benefits' => [
                ['title' => 'Xem hồ sơ cùng lịch sử chăm sóc', 'text' => 'Mở một hồ sơ để xem thông tin và các lần trao đổi với khách hàng.'],
                ['title' => 'Theo dõi giai đoạn bán hàng', 'text' => 'Biết khách hàng đang ở bước nào và xem lịch sử chuyển giai đoạn.'],
                ['title' => 'Ghi rõ lần chăm sóc tiếp', 'text' => 'Lưu ngày hẹn và bước xử lý kế tiếp ngay trong bản ghi tương tác.'],
            ],
            'steps' => [
                ['title' => 'Tạo hồ sơ khách hàng', 'text' => 'Thêm thông tin khách hàng và phân công người phụ trách.'],
                ['title' => 'Ghi lại mỗi lần tương tác', 'text' => 'Nhập tóm tắt trao đổi, ngày hẹn chăm sóc tiếp và việc cần làm.'],
                ['title' => 'Cập nhật giai đoạn bán hàng', 'text' => 'Chuyển giai đoạn khi tình trạng thay đổi và tra lại lịch sử khi cần.'],
            ],
            'faq' => [
                ['question' => 'MiniCRM có lưu ngày hẹn chăm sóc tiếp không?', 'answer' => 'Có. Bản ghi tương tác có trường ngày hẹn chăm sóc tiếp và bước xử lý kế tiếp.'],
                ['question' => 'Có thể xem lại khách hàng đã chuyển qua những giai đoạn nào không?', 'answer' => 'Có. Lịch sử chuyển giai đoạn ghi nhận giai đoạn trước, giai đoạn sau và thời điểm thay đổi.'],
            ],
        ],
    ];

    public const FEATURES = [
        'taskflow' => [
            ['title' => 'Giao việc và đặt hạn xử lý', 'description' => 'Chọn người thực hiện, ngày kết thúc và trạng thái.'],
            ['title' => 'Lọc việc theo khoảng ngày', 'description' => 'Xem công việc của mình, việc đã giao và việc sắp đến hạn.'],
            ['title' => 'Bình luận và danh sách kiểm tra', 'description' => 'Trao đổi và theo dõi các việc nhỏ trong chi tiết công việc.'],
        ],
        'minicrm' => [
            ['title' => 'Danh sách và hồ sơ khách hàng', 'description' => 'Tìm hồ sơ và xem dữ liệu theo phạm vi được phân công.'],
            ['title' => 'Giai đoạn bán hàng và lịch sử', 'description' => 'Cập nhật giai đoạn và xem lại những lần chuyển trạng thái.'],
            ['title' => 'Nhật ký tương tác và ngày hẹn', 'description' => 'Ghi nội dung trao đổi, ngày hẹn chăm sóc tiếp và bước xử lý kế.'],
        ],
    ];
}
