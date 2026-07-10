# HAPPY ECOTECH — Kiến trúc CMS

> Stack thực tế đã triển khai (không phải mô tả chung chung).

## 1. Tổng quan stack

| Lớp | Công nghệ | Ghi chú |
|---|---|---|
| Ngôn ngữ | PHP 8.3+ (dev: 8.5) | `composer.json` yêu cầu `^8.3` |
| Framework | **Laravel 13** | MVC + Blade, session auth |
| Admin/CMS | **Filament v4** (`/admin`) | Livewire, panel builder |
| RBAC | **spatie/laravel-permission v6** | permission ở backend (policy) |
| Sanitize HTML | **mews/purifier** | rich-text chống XSS |
| CSDL | **PostgreSQL 16** | thay SQL Server (quyết định chủ dự án — xem AUDIT §5) |
| Frontend công khai | **Blade + Bootstrap 5.0.0-beta2** | tái sử dụng template gốc, không viết lại React |
| Build asset | Vite | `public/build` |
| Media | filesystem (`storage/app/public`) qua Storage abstraction | DB chỉ lưu metadata/URL |

## 2. Bố cục thư mục chính

```
app/
  Models/                 17 model + Concerns/ (HasBlameable, GeneratesSlug, Publishable, Orderable)
  Policies/               BasePolicy + 13 policy (map permission "{ability}_{resource}")
  Support/Rbac.php        nguồn sự thật tên permission + ma trận role
  Support/helpers.php     setting(), media_url(), he_seo()
  Filament/
    Resources/            12 resource (Services, Posts, Pages, ... ) + RelationManagers
    Pages/ManageSettings  trang cài đặt (tabs)
    Widgets/              StatsOverview, LatestPosts, LatestLeads
  Http/Controllers/       Home, Service, Team, Post, Contact, Sitemap
  Http/Requests/          ContactSubmissionRequest (validate + honeypot rule)
  Observers/AuditObserver ghi audit cho thao tác của user đăng nhập
database/
  migrations/             20 migration nghiệp vụ (jsonb, soft delete, index)
  seeders/                7 seeder idempotent (firstOrCreate)
resources/views/
  layouts/public.blade    layout công khai (SEO, header, footer)
  partials/               header, footer, seo, page-hero, post-card
  blocks/                 11 block partial (hero, core_values, featured_services, ...)
  public/                 page, services/, team/, posts/, contact, sitemap
  errors/404.blade
routes/web.php            route công khai (đặt tên khớp seeder menu)
```

## 3. Phân quyền (RBAC)

- Nguồn sự thật: [`app/Support/Rbac.php`](../app/Support/Rbac.php). Permission dạng `{ability}_{resource}` (97 permission).
- Role: `SuperAdmin` (toàn quyền qua `Gate::before`), `Admin`, `Editor`, `Author` (chỉ sửa bài của mình — enforce trong `PostPolicy`), `Viewer`.
- Backend luôn kiểm tra qua policy — Filament chỉ ẩn menu tương ứng, không thay thế authorization.

## 4. Nội dung động

- **Trang & block**: `pages` + `page_blocks` (`block_type`, `data` jsonb, `sort_order`, `is_enabled`). Render qua `resources/views/blocks/{type}.blade.php` bằng `@includeIf`. Block `featured_services`/`latest_news` query dữ liệu nguồn, không copy.
- **Header/Footer/Menu**: bảng `menus` + `menu_items` (tối đa 2 cấp) + `site_settings` + `social_links`, nạp cho mọi trang qua `View::composer('layouts.public', ...)` trong `AppServiceProvider`.
- **Cache**: `SiteSetting::map()` cache scalar (không cache model), tự flush khi lưu.

## 5. Bảo mật

- Session auth + CSRF mặc định Laravel; password hash bcrypt (`BCRYPT_ROUNDS`).
- Rate limit form liên hệ: `throttle:contact` (5/phút, 50/ngày theo IP) + honeypot `website`.
- Rich-text sanitize (Purifier) khi lưu Service/Post và khi render block richtext.
- `robots.txt` chặn `/admin`; audit log không ghi secret/password/token.
- SMTP secret đọc từ `.env`, không lưu DB, không trả về frontend.

## 6. Điểm mở rộng

- Storage abstraction → đổi sang S3 bằng cách đổi `FILESYSTEM_DISK`.
- Thêm block mới: thêm hằng trong `PageBlock::TYPES` + tạo `blocks/<type>.blade.php`.
- Thêm resource admin: `php artisan make:filament-resource X --generate` rồi tinh chỉnh theo mẫu `ServiceResource`.
