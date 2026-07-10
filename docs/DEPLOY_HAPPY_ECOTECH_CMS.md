# HAPPY ECOTECH — Triển khai production (Deploy & Rollback)

> Stack: Laravel 13 + Filament v4 + PostgreSQL 16. An toàn dữ liệu là ưu tiên #1 — **luôn backup trước khi migrate**.

## 1. Điều kiện môi trường
- PHP 8.3+ với extension: `pdo_pgsql pgsql mbstring intl gd bcmath zip openssl tokenizer xml opcache`.
- Composer 2, Node 20+ & npm (build asset), PostgreSQL 16 (server hoặc container).
- Web server phục vụ `public/` (nginx + php-fpm, hoặc Docker image kèm theo).

## 2. Biến môi trường cần cấu hình (KHÔNG in secret ra log/commit)
`APP_KEY` (sinh mới), `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`,
`DB_CONNECTION=pgsql`, `DB_HOST/PORT/DATABASE/USERNAME/DB_PASSWORD`,
`MAIL_*` (nếu gửi mail), `CONTACT_NOTIFY_TO` (tuỳ chọn),
`HE_ADMIN_NAME/EMAIL/PASSWORD` (chỉ để seed SuperAdmin lần đầu). Xem `.env.example`.

## 3. Tạo database / login SQL với quyền tối thiểu
```sql
CREATE ROLE happyecotech LOGIN PASSWORD '<mật khẩu mạnh>';
CREATE DATABASE happyecotech OWNER happyecotech ENCODING 'UTF8';
-- (tuỳ chọn) hạn chế quyền: chỉ CONNECT + quyền trên schema public của DB này.
```

## 4. Backup database hiện tại (BẮT BUỘC trước migrate)
```bash
pg_dump -h <host> -U happyecotech -d happyecotech -Fc -f backup_$(date +%F_%H%M).dump
# Backup media:
tar czf media_$(date +%F).tgz storage/app/public
```

## 5. Cài đặt & migrate
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate            # nếu chưa có APP_KEY
php artisan migrate --force         # KHÔNG dùng migrate:fresh trên production
php artisan storage:link
```
> Migration không chứa lệnh destructive; chỉ tạo bảng/index mới → backward-compatible ở bước đầu.

## 6. Seed nội dung (idempotent — an toàn chạy lại)
```bash
php artisan db:seed --force
```
Seed dùng `firstOrCreate` nên không tạo bản ghi trùng và không ghi đè chỉnh sửa của admin.

## 7. Tạo / đặt lại SuperAdmin
Seed **chỉ tạo mới** admin từ `HE_ADMIN_EMAIL`/`HE_ADMIN_PASSWORD` (dùng `firstOrCreate` — **không** ghi đè mật khẩu nếu tài khoản đã tồn tại). Để tạo mới hoặc **đặt lại mật khẩu** admin, dùng lệnh CLI (luôn set lại mật khẩu):
```bash
php artisan he:superadmin                              # lấy từ .env
php artisan he:superadmin --email=... --password=...   # hoặc chỉ định trực tiếp
```
**Đăng nhập và đổi mật khẩu ngay**, sau đó có thể bỏ `HE_ADMIN_PASSWORD` khỏi môi trường.

## 8. Build frontend & cache production
```bash
npm ci && npm run build
php artisan optimize                # config + route + view + event cache
php artisan filament:cache-components
```

## 9. Restart service / container
- Native: reload php-fpm + nginx (`systemctl reload php8.3-fpm nginx`).
- Docker: `docker compose up -d --build` (xem `docker-compose.yml`). App nghe cổng 8080.

## 10. Health check & smoke test
```bash
php artisan about                                   # kiểm tra DB connection
curl -I https://<domain>/                           # 200
curl -I https://<domain>/admin/login                # 200
curl -I https://<domain>/dich-vu                    # 200
curl -s https://<domain>/sitemap.xml | head         # XML
```

## 11. Rollback
- **Ứng dụng**: deploy lại tag/commit trước; `php artisan optimize:clear && php artisan optimize`.
- **Database**: restore từ dump —
  ```bash
  pg_restore -h <host> -U happyecotech -d happyecotech --clean --if-exists backup_....dump
  ```
  (chỉ thực hiện khi đã xác nhận; ưu tiên staging trước.)
- **Media**: giải nén lại `media_*.tgz` vào `storage/app/public`.

## 12. Vị trí media & backup media
- Lưu tại `storage/app/public` (symlink `public/storage`). Trong Docker: volume `storage_public`.
- Backup định kỳ bằng `tar` như mục 4, hoặc chuyển sang S3 (`FILESYSTEM_DISK=s3`) rồi bật versioning của bucket.

---

### Ghi chú CI/CD
Workflow cũ (`.github/workflows/deploy.yml`) build image **tĩnh nginx** cho landing page. Khi cắt sang CMS cần cập nhật để:
1. Build image từ `Dockerfile` mới (Laravel), push registry.
2. Trên server: `docker compose pull && up -d`, rồi chạy `php artisan migrate --force && php artisan optimize` trong container app.
3. Giữ service tĩnh cũ tới khi CMS smoke-test đạt (staging-first), rồi chuyển DNS/router.

> **Chưa build image trong môi trường dev** (Docker daemon tắt) — hãy build & kiểm thử ở CI/staging trước production.
