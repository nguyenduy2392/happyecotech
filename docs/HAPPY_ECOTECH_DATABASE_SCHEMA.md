# HAPPY ECOTECH — Lược đồ CSDL (PostgreSQL)

> 20 migration nghiệp vụ + bảng hệ thống Laravel + bảng spatie. UTC trong DB, `timestamptz`/`timestamp`, `jsonb` cho dữ liệu JSON, unique index cho slug.

## Hệ thống & xác thực
- `users` (+ `is_active`, `phone`, `avatar_media_id`)
- `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` (spatie)
- `audit_logs` — `event`, `auditable_type/id` (morph), `properties` jsonb, `ip_address`, `user_agent`, `created_at` (chỉ đọc)

## Nội dung
| Bảng | Điểm chính |
|---|---|
| `media_assets` | disk, path, mime, size, width/height, alt, caption, folder; soft delete |
| `services` | slug unique, status, is_featured, sort_order, published_at, seo_*, FK icon/cover/og media; soft delete |
| `service_features` | FK service (cascade), title, description, icon, sort_order |
| `service_media` | pivot service ↔ media (gallery) |
| `team_members` | position, group, socials jsonb, status, is_featured, sort_order; soft delete |
| `posts` | slug unique, excerpt, content, status, is_featured, published_at, author_id, seo_*; soft delete |
| `post_categories` | slug unique, sort_order; soft delete |
| `tags` | slug unique |
| `category_post`, `post_tag` | pivot (unique cặp khoá) |
| `pages` | slug unique, status, is_system, seo_*; soft delete |
| `page_blocks` | FK page (cascade), block_type, `data` jsonb, sort_order, is_enabled |

## Menu & cấu hình
- `menus` (location unique), `menu_items` (self-FK `parent_id`, link_type, url/route_name/reference_id, target_blank, sort_order, is_enabled)
- `site_settings` (key unique, group, value, type: string/text/boolean/integer/json/media)
- `social_links` (platform, url, icon, sort_order, is_enabled)

## Liên hệ
- `contact_submissions` — name/phone/email/company, FK service, message, status (new/in_progress/resolved/spam/archived), assigned_to, ip, consent; soft delete
- `contact_notes` — FK submission (cascade), FK user, body

## Quy tắc đã áp dụng
- **Trường chung** bảng nội dung: `created_by`/`updated_by` (nullOnDelete), `created_at`/`updated_at`, `deleted_at` (soft delete). Tự gán qua trait `HasBlameable`.
- **Index**: unique slug theo loại nội dung; index cho `status`, `published_at`, `sort_order`, `created_at`, và các FK thường query.
- **FK**: media dùng `nullOnDelete`; pivot/feature dùng `cascadeOnDelete`; lead/nội dung ưu tiên soft delete (không xoá cứng mặc định).
- **JSON**: `data`/`properties`/`socials` kiểu `jsonb`, validate/cast ở application layer.
- **Migration & seed idempotent**: `php artisan migrate:fresh --seed` chạy sạch; seed dùng `firstOrCreate` (chạy lại không tạo trùng — đã kiểm thử).

## Sinh lại nhanh
```bash
php artisan migrate:fresh --seed
```
