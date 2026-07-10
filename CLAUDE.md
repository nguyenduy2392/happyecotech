# CLAUDE.md — Happy Ecotech Website & CMS

> Hướng dẫn cho Claude/dev khi làm việc trong repo này. Mô tả **stack thực tế đã triển khai**, không phải kế hoạch.
> Website: https://happyecotech.com — Ngôn ngữ sản phẩm: Tiếng Việt.

## 1. Đây là gì

Website doanh nghiệp + CMS quản trị nội dung. Ban đầu là **landing page tĩnh** (Bootstrap); đã được xây dựng lại thành ứng dụng full-stack:

| Lớp | Công nghệ |
|---|---|
| Backend | **Laravel 13** (PHP 8.3+) |
| Admin/CMS | **Filament v4** tại `/admin` (Livewire) |
| RBAC | **spatie/laravel-permission v6** (policy backend) |
| Sanitize HTML | **mews/purifier** |
| CSDL | **PostgreSQL 16** |
| Public site | **Blade SSR + Bootstrap 5.0.0-beta2** (tái sử dụng template gốc, KHÔNG dùng React) |
| Build asset | Vite → `public/build` |
| Media | filesystem `storage/app/public` qua Storage abstraction (DB chỉ lưu metadata) |

**Quyết định kiến trúc quan trọng:** tài liệu gốc yêu cầu Microsoft SQL Server, nhưng chủ dự án đã chốt dùng **PostgreSQL**. Đây là nguồn sự thật hiện tại — không quay lại SQL Server nếu không có quyết định mới.

Tài liệu chi tiết: [`docs/`](docs/) — AUDIT, CMS_ARCHITECTURE, DATABASE_SCHEMA, ADMIN_GUIDE, DEPLOY.

## 2. Môi trường & lệnh

Máy dev (macOS): PHP/Composer/PostgreSQL cài qua Homebrew; `psql` là keg-only (thêm `/opt/homebrew/opt/postgresql@16/bin` vào PATH). Kết nối local dùng `trust` auth nên `DB_PASSWORD` sai vẫn kết nối được ở local.

```bash
# Cài đặt lần đầu
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed && php artisan storage:link

# Chạy
php artisan serve                 # public :8000, admin /admin

# Quality gates (chạy trước khi báo hoàn thành)
vendor/bin/pint                   # lint/format (--test để chỉ kiểm tra)
php artisan test                  # PHPUnit (dùng DB pgsql happyecotech_test)
npm run build                     # production asset
php artisan migrate:fresh --seed  # migrate + seed idempotent trên DB sạch
php artisan optimize              # cache config/route/view (clear: optimize:clear)
php artisan filament:cache-components   # compile/validate toàn bộ resource Filament
```

## 3. Đăng nhập admin

- SuperAdmin seed từ `HE_ADMIN_EMAIL`/`HE_ADMIN_PASSWORD`. **Seed dùng `firstOrCreate` → chỉ set mật khẩu khi TẠO mới**, không ghi đè nếu tài khoản đã tồn tại (bảo vệ mật khẩu admin đã đổi).
- Đổi mật khẩu / tạo lại admin: **`php artisan he:superadmin`** (luôn đặt lại mật khẩu từ `.env`, hoặc `--email=` `--password=`). Đây là cách đúng khi đổi `HE_ADMIN_PASSWORD` trong `.env`.

## 4. Bố cục & quy ước code

Xem cấu trúc đầy đủ trong [`docs/HAPPY_ECOTECH_CMS_ARCHITECTURE.md`](docs/HAPPY_ECOTECH_CMS_ARCHITECTURE.md).

- **Model** ở `app/Models` dùng trait dùng chung ở `app/Models/Concerns`: `HasBlameable` (auto created_by/updated_by), `GeneratesSlug` (slug duy nhất, override `slugSource()`), `Publishable` (`scopePublished`), `Orderable` (`scopeOrdered`). Nội dung dùng soft delete.
- **RBAC** — nguồn sự thật: [`app/Support/Rbac.php`](app/Support/Rbac.php). Permission dạng `{ability}_{resource}` (vd `update_post`, `publish_service`, `export_contact`, `manage_settings`). Role: SuperAdmin (toàn quyền qua `Gate::before` trong `AppServiceProvider`), Admin, Editor, Author (chỉ sửa bài của mình — enforce ở `PostPolicy`), Viewer. Mọi policy kế thừa `app/Policies/BasePolicy.php`. **Luôn enforce ở backend, không chỉ ẩn menu.**
- **Filament v4** (khác v3, dễ nhầm):
  - Cấu trúc: `app/Filament/Resources/{Plural}/{X}Resource.php` + `Schemas/{X}Form.php` + `Tables/{X}sTable.php` + `Pages/`.
  - Form: `public static function configure(Schema $schema): Schema` → `$schema->components([...])`. Layout `Section/Grid/Tabs` ở `Filament\Schemas\Components`. `Get/Set` ở `Filament\Schemas\Components\Utilities`.
  - Table: dùng **`->recordActions([...])`** và **`->toolbarActions([...])`** (KHÔNG phải `actions()`/`bulkActions()`). Mọi action ở namespace `Filament\Actions\*`.
  - `navigationIcon` dùng enum `Filament\Support\Icons\Heroicon`. Nav group: `Nội dung`, `Thư viện`, `Giao diện & Menu`, `Liên hệ`, `Hệ thống`.
  - Chọn media trong form: helper `App\Filament\Support\Forms::media('icon_media_id','icon','Icon')`.
  - Thêm resource mới: `php artisan make:filament-resource X --generate` rồi tinh chỉnh theo mẫu `ServiceResource` (form có Section, status Select từ `X::STATUSES`, badge, filter, SoftDelete). Nhớ đặt policy + permission tương ứng trong `Rbac`.
- **Trang & block**: `pages` + `page_blocks` (`block_type`, `data` jsonb). Thêm loại block: thêm hằng vào `PageBlock::TYPES` + tạo `resources/views/blocks/<type>.blade.php`. Block `featured_services`/`latest_news` query dữ liệu nguồn, không copy.
- **Header/Footer/Menu/Settings**: nạp cho public qua `View::composer('layouts.public', ...)` trong `AppServiceProvider`. Đọc setting: helper `setting('key', $default)` (media trả về `MediaAsset`). `SiteSetting::map()` **chỉ cache scalar** (không cache Eloquent model — nếu không sẽ lỗi unserialize giữa các tiến trình).
- **Sanitize**: nội dung rich-text của Service/Post được Purifier clean khi lưu; block `richtext` clean khi render.
- **Route public**: `routes/web.php` — tên route (`home`, `services.index`, `posts.show`...) phải khớp với `MenuSeeder`. Catch-all `/{page:slug}` đặt CUỐI và loại trừ `admin|livewire|storage|assets|api`.

## 5. Kiểm thử

- `phpunit.xml` trỏ DB test = `pgsql`/`happyecotech_test` (KHÔNG dùng sqlite vì schema có `jsonb`). Tạo DB test: `createdb -O happyecotech happyecotech_test`.
- Dùng `RefreshDatabase`; data-provider dùng attribute `#[DataProvider(...)]` (PHPUnit 12).
- Test hiện có: `AdminSmokeTest`, `AdminResourcesTest` (12 resource render), `PublicSiteTest` (published-only, contact form, honeypot, sitemap), `RbacAndDataTest` (slug uniqueness, phân quyền, không lộ secret).

## 6. Nguyên tắc bắt buộc

1. **Không mất dữ liệu / không gián đoạn production**; backup DB + media trước khi migrate.
2. Migration/seed **idempotent** (`firstOrCreate`), không thao tác destructive trên production.
3. Không commit secret; secret đọc từ `.env`/env store. `.env.example` không chứa secret thật.
4. **Không hard-code** nội dung admin cần chỉnh; validation quan trọng ở backend.
5. **Không bịa** số liệu marketing / thông tin doanh nghiệp — dữ liệu chưa xác minh phải tắt hoặc gắn cờ.
6. Không dùng `href="#"` / link rỗng cho phần tử điều hướng; link ngoài kèm `rel="noopener noreferrer"`.
7. Chạy pint + test + build + migrate xanh trước khi báo hoàn thành.

## 7. Cần chủ website xác minh (đang gắn cờ / tắt)

- Số liệu thống kê trang chủ (placeholder template) — block `stats` đang tắt.
- Logo đối tác, ảnh & thông tin đội ngũ thật (ảnh cũ là stock).
- Nội dung Chính sách bảo mật / Điều khoản (đang là bản nháp placeholder).
- URL mạng xã hội thật (social links đang tắt).

## 8. Nguồn sự thật (khi mâu thuẫn)

1. Yêu cầu mới nhất của chủ dự án → 2. File này (CLAUDE.md) → 3. `docs/` cập nhật gần nhất → 4. Migration/schema thực tế → 5. Code đang chạy → 6. Nội dung website cũ (`resources/legacy/index.html`).
Nếu code và tài liệu mâu thuẫn: xác minh runtime, rồi cập nhật tài liệu trong cùng thay đổi.
