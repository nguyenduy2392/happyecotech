# HAPPY ECOTECH — Audit hiện trạng (Phase 0)

> Ngày audit: 2026-07-10. Người thực hiện: Claude (Tech Lead + Full-stack + QA).
> Mục tiêu audit: xác định chính xác stack thực tế trước khi triển khai CMS, tránh làm hỏng nội dung/URL đang chạy.

## 1. Stack thực tế đã xác định (bằng chứng)

| Hạng mục | Thực tế | Bằng chứng |
|---|---|---|
| Frontend framework | **Không có** — HTML tĩnh thuần | `index.html` (568 dòng), không có JSX/Vue/build |
| CSS | Bootstrap **5.0.0-beta2** + template UIdeck | `assets/css/bootstrap-5.0.0-beta2.min.css`, `assets/css/main.css` |
| JS | Vanilla JS + tiny-slider + wow.js + count-up | `assets/js/main.js`, không bundler |
| Backend | **Không có** | Không `package.json`, `*.csproj`, `composer.json`, `requirements.txt`, `go.mod` |
| Database | **Không có** | — |
| ORM / data access | **Không có** | — |
| Auth | **Không có** | — |
| API | **Không có** | — |
| Routing | Anchor trong 1 trang (`#home`, `#about`, `#service`) | `index.html`, `assets/js/main.js` (`.page-scroll` smooth scroll) |
| Build system | **Không có** | — |
| Test | **Không có** | — |
| Deploy | Docker image `nginx:alpine` phục vụ file tĩnh | `Dockerfile`, `docker-compose.prod.yml`, `.github/workflows/deploy.yml` |
| CI/CD | GitHub Actions: build image trên tag `v*` → Docker Hub → SSH `docker-compose up` tại `/opt/eco` | `.github/workflows/deploy.yml` |

**Kết luận:** repo là website **tĩnh 100%**. Không tồn tại backend/DB/ORM/auth để "mở rộng". Việc xây CMS là **dựng mới toàn bộ một stack full-stack**, phần tái sử dụng được duy nhất là **HTML/CSS Bootstrap của trang public**.

## 2. Nội dung thật cần seed (không phải placeholder)

- **Công ty / tagline:** "HappyEcotech — Minh bạch từ gốc – Hạnh phúc từ công nghệ".
- **Hero:** "Minh bạch từ gốc – Hạnh phúc từ công nghệ" + mô tả hệ sinh thái.
- **Tầm nhìn:** "Trở thành hệ sinh thái công nghệ minh bạch hàng đầu Đông Nam Á."
- **Sứ mệnh:** "Minh bạch dữ liệu – Số hóa kiểm định – Kết nối doanh nghiệp và người tiêu dùng với công nghệ an toàn."
- **5 giá trị cốt lõi:** Minh bạch, Chính trực, Công nghệ vì con người, Trách nhiệm xã hội, Bền vững (mỗi giá trị có đoạn mô tả trong `index.html`).
- **4 dịch vụ:** SmartLabel, TaskFlow, MiniCRM, Farm2Home (kèm mô tả + icon trong `assets/img/project-icon/`).
- **Liên hệ:** ĐT `0936685886`, email `hung.duy@happyecotech.com`, địa chỉ `198 Trường Chinh, Hải Phòng`.
- **App TaskFlow (link thật):**
  - App Store: `https://apps.apple.com/us/app/taskflow/id6758305963`
  - Google Play: `https://play.google.com/store/apps/details?id=com.happyecotech.taskflow`
- **Google Maps embed** (198 Trường Chinh) — có trong footer.
- **Zalo verification:** meta `zalo-platform-site-verification` = `Fz-kBBpPAIrbyBfscFKTR1hmd6RsXaanDJ0o`.

## 3. Placeholder của template — KHÔNG được seed như dữ liệu thật (cần chủ website xác minh)

- **Số liệu counter:** `3642` khách hàng hài lòng, `5436` dự án, `642K` thiết kế sống động, `42` thiết kế sáng tạo — số của template UIdeck, **bịa**.
- **4 logo "đối tác":** `pagebulb`, `uideck`, `graygrids`, `lineicons` — logo của nhà làm template, không phải khách hàng thật.
- **Ảnh đội ngũ:** `team-1..4.jpg` — ảnh stock của template (không có tên/chức danh thật).
- **Testimonials:** `testimonial-1..3.png` — stock.

→ Sẽ đưa vào block quản trị nhưng **để trống hoặc gắn cờ "cần xác minh"**, không hiển thị số bịa trên production khi chưa được duyệt.

## 4. Bug cụ thể trong trang hiện tại

| # | Bug | Vị trí |
|---|---|---|
| 1 | Nav "Đội ngũ", "Liên hệ" → `href="#0"` (link chết) | `index.html:81-84` |
| 2 | CTA "Liên hệ hợp tác" → `javascript:void(0)` | `index.html:114` |
| 3 | "Xem tất cả dịch vụ" → `javascript:void(0)` | `index.html:330` |
| 4 | "LIÊN HỆ VỚI CHÚNG TÔI" → `javascript:void(0)` | `index.html:422` |
| 5 | Footer social → **`href="jvascript:void(0)"`** (lỗi gõ `jvascript`) | `index.html:490-499` |
| 6 | Footer logo → `assets/img/logo/logo.svg` **không tồn tại** (chỉ có `logo-full.png`) → ảnh vỡ | `index.html:484` |
| 7 | Footer quick-links + service-links → toàn `javascript:void(0)` | `index.html:509-525` |
| 8 | Card dịch vụ không click được, không có trang chi tiết | `index.html:277-327` |
| 9 | `<html lang="">` rỗng; `<meta description>` rỗng | `index.html:2,8` |
| 10 | Không có trang Đội ngũ / Liên hệ / Tin tức / 404 | toàn site |

## 5. Quyết định kiến trúc (đã chốt với chủ dự án)

- **CSDL:** **PostgreSQL** (thay cho SQL Server trong tài liệu gốc — chủ dự án đã quyết chính thức; nhẹ, miễn phí, chạy native ARM/CI/Docker, đủ cho CMS brochure). Ghi chú: `CLAUDE.md §19` yêu cầu quyết định chính thức của chủ dự án để đổi khỏi SQL Server — đã có.
- **Stack:** **Laravel 12 + Filament v3 (admin) + PostgreSQL**. Blade render lại giao diện Bootstrap hiện có (không viết lại thành React → giữ nguyên UI, ít churn).
- **RBAC:** `spatie/laravel-permission`.
- **Auth admin:** Laravel session + Filament (bcrypt/argon qua Hash), CSRF mặc định của Laravel.
- **Media:** filesystem (`storage/app/public`) qua Storage abstraction → dễ chuyển S3 sau; DB chỉ lưu metadata/URL.
- **Vị trí app:** Laravel tại **repo root**; asset cũ chuyển vào `public/assets/`; `index.html` → Blade view; giữ nguyên đường dẫn `/assets/...` để không vỡ URL asset đã index.

## 6. Môi trường build/verify (thực tế máy dev)

- macOS 15.6, **arm64**. Node 22 + npm có sẵn. Homebrew có sẵn.
- **PHP/Composer/PostgreSQL chưa cài** → cài native qua Homebrew (bottled ARM, nhanh) để chạy được migrate/seed/test/build đúng yêu cầu quality gate.
- Docker có cài nhưng **daemon đang tắt** → dùng native cho dev; Docker để đóng gói deploy.

## 7. Nguyên tắc bảo vệ hiện trạng khi triển khai

- Giữ site tĩnh cũ hoạt động tới khi CMS sẵn sàng (cutover có kiểm soát; không chuyển frontend sang CMS trước khi DB/API sẵn sàng — theo `CLAUDE.md §16`).
- Giữ đường dẫn `/assets/...` và URL asset đang được dùng.
- Không seed số liệu/logos/team bịa như dữ liệu thật.
- Migration idempotent; seed idempotent; không thao tác destructive.
