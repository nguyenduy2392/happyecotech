<?php

/*
 * Nhãn ngành (eyebrow), tiêu đề theo lợi ích và gạch đầu dòng mặc định theo slug, dùng khi CMS chưa có
 * category_label / tagline / highlights. Mọi ý đều bám mô tả seed hoặc repo sản phẩm; không số liệu/khách hàng.
 * Giải pháp chưa xuất bản (datahub, omc) chỉ có tên + dòng giới thiệu chung, không lộ nội dung nháp.
 */

return [
    'taskflow' => [
        'group' => 'ops', 'category' => 'Quản lý công việc',
        'headline' => 'TaskFlow: quản lý công việc, dự án và chấm công cho đội nhóm',
        'summary' => 'Giao việc, theo dõi dự án, chấm công và trò chuyện trên điện thoại.',
    ],
    'minicrm' => [
        'group' => 'ops', 'category' => 'Quản lý khách hàng',
        'headline' => 'MiniCRM: nắm khách hàng, hợp đồng và pipeline bán hàng',
        'summary' => 'Hồ sơ khách hàng, pipeline, tương tác và hợp đồng trong một hệ thống.',
    ],
    'smartlabel' => [
        'group' => 'trace', 'category' => 'Truy xuất nguồn gốc',
        'headline' => 'Cho người mua tự kiểm tra nguồn gốc và chất lượng sản phẩm',
        'summary' => 'Tem điện tử mã QR xác thực nguồn gốc, kiểm định và chất lượng.',
        'points' => [
            'Mỗi sản phẩm hoặc lô hàng gắn một mã QR duy nhất',
            'Người mua quét mã để xem thông tin nguồn gốc, kiểm định',
            'Điện tử hóa việc đánh dấu và quản lý chất lượng',
        ],
        'cta' => 'Tìm hiểu SmartLabel',
    ],
    'farm2home' => [
        'group' => 'trace', 'category' => 'Nông sản',
        'headline' => 'Đưa nông sản từ người trồng đến thẳng người tiêu dùng',
        'summary' => 'Kết nối trực tiếp giữa nông dân và người tiêu dùng.',
        'points' => [
            'Kết nối trực tiếp nông dân với người tiêu dùng',
        ],
        'cta' => 'Tìm hiểu Farm2Home',
    ],
    'datahub' => [
        'group' => 'egov', 'category' => 'Hành chính số', 'name' => 'DataHub',
        'headline' => 'DataHub Văn thư số AI cho cấp xã, phường',
        'summary' => 'Văn thư số ứng dụng AI cho cấp xã, phường.',
        'blurb' => 'DataHub Văn thư số AI nằm trong hệ sinh thái hành chính số của Happy Ecotech dành cho cấp xã, phường.',
        'cta' => 'Liên hệ tư vấn DataHub',
    ],
    'omc' => [
        'group' => 'egov', 'category' => 'Hành chính số', 'name' => 'OMC',
        'headline' => 'OMC trong hệ sinh thái hành chính số cấp xã, phường',
        'summary' => 'Giải pháp hành chính số cho cấp xã, phường.',
        'blurb' => 'OMC là một phần của hệ sinh thái hành chính số Happy Ecotech dành cho cấp xã, phường.',
        'cta' => 'Liên hệ tư vấn OMC',
    ],
    '_groups' => [
        'ops' => ['title' => 'Quản lý công việc và khách hàng', 'icon' => 'workflow'],
        'trace' => ['title' => 'Truy xuất nguồn gốc và nông sản', 'icon' => 'qr'],
        'egov' => ['title' => 'Hành chính số cấp xã, phường', 'icon' => 'building'],
    ],
];
