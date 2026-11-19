<?php

namespace App\Support;

final class EcosystemCopy
{
    public const LABELS = [
        'taskflow' => ['category_label' => 'Quản lý công việc', 'tagline' => 'Giao việc rõ ràng và theo dõi tiến độ đến khi hoàn tất'],
        'minicrm' => ['category_label' => 'Quản lý khách hàng', 'tagline' => 'Quản lý hồ sơ khách hàng và lịch chăm sóc gọn nhẹ'],
        'smartlabel' => ['category_label' => 'Truy xuất nguồn gốc', 'tagline' => 'Cho người mua xem nguồn gốc và thông tin kiểm định qua tem QR'],
        'farm2home' => ['category_label' => 'Nông sản', 'tagline' => 'Kết nối trực tiếp nông dân với người tiêu dùng'],
        'datahub' => ['category_label' => 'Hành chính số', 'tagline' => 'Đưa công tác văn thư cấp xã, phường lên môi trường số'],
        'omc' => ['category_label' => 'Hành chính số', 'tagline' => 'Định hướng phục vụ nhu cầu hành chính số cấp xã, phường'],
    ];

    public const OVERVIEW = [
        'heading' => 'Công cụ số cho từng nhu cầu vận hành',
        'intro' => 'Khám phá các công cụ quản lý công việc, khách hàng và những giải pháp dành cho nông sản, truy xuất nguồn gốc, hành chính số.',
        'items' => [
            ['key' => 'taskflow', 'name' => 'TaskFlow', 'category' => 'Quản lý công việc', 'summary' => 'Giao đầu việc, đặt ngày kết thúc và theo dõi trạng thái theo dự án.', 'anchor_or_url' => '#taskflow'],
            ['key' => 'minicrm', 'name' => 'MiniCRM', 'category' => 'Quản lý khách hàng', 'summary' => 'Theo dõi hồ sơ, giai đoạn bán hàng và lịch chăm sóc tiếp theo.', 'anchor_or_url' => '#minicrm'],
            ['key' => 'smartlabel', 'name' => 'SmartLabel', 'category' => 'Truy xuất nguồn gốc', 'summary' => 'Dùng tem QR để người mua xem nguồn gốc và thông tin kiểm định.', 'anchor_or_url' => '#smartlabel'],
            ['key' => 'farm2home', 'name' => 'Farm2Home', 'category' => 'Nông sản', 'summary' => 'Kết nối trực tiếp nông dân với người tiêu dùng.', 'anchor_or_url' => '#farm2home'],
            ['key' => 'datahub', 'name' => 'DataHub Văn thư số AI', 'category' => 'Hành chính số', 'summary' => 'Giải pháp định hướng văn thư số cho đơn vị hành chính cấp xã, phường.', 'anchor_or_url' => '#datahub'],
            ['key' => 'omc', 'name' => 'OMC', 'category' => 'Hành chính số', 'summary' => 'Giải pháp thuộc nhóm hành chính số cấp xã, phường.', 'anchor_or_url' => '#omc'],
        ],
    ];

    public const SOLUTIONS = [
        'heading' => 'Giải pháp cho truy xuất, nông sản và hành chính số',
        'items' => [
            [
                'key' => 'smartlabel', 'name' => 'SmartLabel', 'category' => 'Truy xuất nguồn gốc',
                'title' => 'Tra cứu nguồn gốc sản phẩm ngay trên tem QR',
                'description' => 'SmartLabel hỗ trợ điện tử hóa việc đánh dấu và quản lý thông tin kiểm định sản phẩm. Người mua quét mã QR để xem thông tin nguồn gốc và chất lượng.',
                'highlights' => ['Tem điện tử có mã QR', 'Tra cứu nguồn gốc sản phẩm', 'Xem thông tin kiểm định'],
                'image' => 'service-smartlabel.webp', 'cta_label' => 'Tìm hiểu SmartLabel', 'url' => '/dich-vu/smartlabel',
            ],
            [
                'key' => 'farm2home', 'name' => 'Farm2Home', 'category' => 'Nông sản',
                'title' => 'Kết nối người trồng với người mua nông sản',
                'description' => 'Farm2Home hướng tới kết nối trực tiếp nông dân với người tiêu dùng. Giải pháp tập trung vào việc giúp người mua tiếp cận người sản xuất nông sản.',
                'highlights' => ['Kết nối trực tiếp', 'Dành cho người sản xuất và người mua nông sản'],
                'image' => 'service-farm2home.webp', 'cta_label' => 'Tìm hiểu Farm2Home', 'url' => '/dich-vu/farm2home',
            ],
            [
                'key' => 'datahub', 'name' => 'DataHub Văn thư số AI', 'category' => 'Hành chính số',
                'title' => 'Văn thư số cho đơn vị hành chính cấp xã, phường',
                'description' => 'DataHub Văn thư số AI hướng tới nhu cầu văn thư số tại các đơn vị cấp xã, phường. Đơn vị quan tâm có thể trao đổi với Happy Ecotech về nhu cầu triển khai.',
                'highlights' => ['Văn thư số', 'Dành cho đơn vị cấp xã, phường'],
                'image' => 'service-datahub.webp', 'cta_label' => 'Trao đổi về DataHub', 'url' => '/lien-he?solution=datahub',
            ],
            [
                'key' => 'omc', 'name' => 'OMC', 'category' => 'Hành chính số',
                'title' => 'Trao đổi về nhu cầu hành chính số với OMC',
                'description' => 'OMC thuộc nhóm giải pháp hành chính số của Happy Ecotech cho cấp xã, phường. Liên hệ để trao đổi về nhu cầu của đơn vị và phạm vi phù hợp.',
                'highlights' => ['Hành chính số cấp xã, phường'],
                'image' => 'service-omc.webp', 'cta_label' => 'Trao đổi về OMC', 'url' => '/lien-he?solution=omc',
            ],
        ],
    ];
}
