# PROMPT GIAO CLAUDE: HOÀN THIỆN WEBSITE & XÂY DỰNG CMS HAPPY ECOTECH

> Website hiện tại: https://happyecotech.com/  
> Mục tiêu: hoàn thiện toàn bộ liên kết và nội dung website công khai, đồng thời xây dựng trang quản trị nội dung toàn diện theo hướng WordPress nhưng phù hợp với kiến trúc dự án hiện có.  
> Cơ sở dữ liệu bắt buộc: **Microsoft SQL Server**.

---

## 1. Vai trò và nguyên tắc thực hiện

Bạn đang làm việc trực tiếp trong repository của dự án Happy Ecotech với vai trò **Tech Lead + Full-stack Developer + QA**.

Hãy thực hiện trọn vẹn công việc, không chỉ viết kế hoạch hoặc tạo mockup. Trước khi sửa mã nguồn, bắt buộc khảo sát repository để xác định chính xác:

- Framework frontend và backend đang dùng.
- Cơ chế routing hiện tại.
- Cấu trúc component, layout, asset và style.
- Cơ chế xác thực hiện có, nếu có.
- Cách kết nối database và ORM/data-access hiện tại.
- Cách build, deploy và cấu hình production.
- Dữ liệu hoặc chức năng nào đang hoạt động để tránh làm hỏng.

### Nguyên tắc bắt buộc

1. **Không viết lại toàn bộ dự án từ đầu** nếu chưa chứng minh kiến trúc hiện tại không thể mở rộng.
2. Tái sử dụng giao diện, màu sắc, logo, component và phong cách hiện có.
3. Không phá vỡ URL đang được index hoặc nội dung đang hoạt động; nếu thay đổi URL phải có redirect phù hợp.
4. Không hard-code nội dung mà quản trị viên cần chỉnh sửa.
5. Không lưu secret, password hoặc chuỗi kết nối thật vào source code.
6. Không xóa dữ liệu production và không chạy thao tác destructive nếu chưa có backup.
7. Dùng migration có thể chạy lặp an toàn; mọi schema mới phải được version-control.
8. Sau khi khảo sát, hãy **tiếp tục triển khai ngay**, không dừng lại để chờ xác nhận trừ khi phát hiện nguy cơ mất dữ liệu hoặc cần credential production.
9. Hoàn thành build, test và tài liệu triển khai trước khi kết thúc.

---

## 2. Hiện trạng khảo sát từ website công khai

Khảo sát website ngày 10/07/2026 cho thấy:

- Website đang là landing page giới thiệu công ty và hệ sinh thái dịch vụ.
- Menu đầu trang gồm: **Trang chủ, Giới thiệu, Dịch vụ, Đội ngũ, Liên hệ**.
- Hai mục **Đội ngũ** và **Liên hệ** hiện chưa có hyperlink hoạt động.
- Khối Dịch vụ đang hiển thị bốn sản phẩm:
  - SmartLabel
  - TaskFlow
  - MiniCRM
  - Farm2Home
- Các thẻ dịch vụ chưa thể hiện rõ trạng thái có thể click và chưa dẫn tới trang chi tiết.
- Nút **“Xem tất cả dịch vụ”** chưa có hyperlink hoạt động.
- Footer hiển thị nhóm Link, Dịch vụ và Liên hệ nhưng phần lớn mục chưa có liên kết thực tế.
- Khu vực social ở footer có dấu hiệu đang để icon/link rỗng.
- Website có các CTA “Liên hệ hợp tác” và “LIÊN HỆ VỚI CHÚNG TÔI”; cần kiểm tra và thống nhất đường dẫn.
- Nội dung, logo, icon, menu, footer và các khối landing page đang có dấu hiệu phụ thuộc vào mã nguồn tĩnh.
- Các số liệu “Khách hàng hài lòng”, “Dự án hoàn thành”, “Thiết kế sống động”, “Thiết kế sáng tạo” cần chuyển thành dữ liệu quản trị được và đánh dấu để chủ website xác minh trước khi công bố chính thức.

Không chỉ sửa đúng các lỗi nêu trên. Hãy rà soát toàn bộ header, footer, CTA, card và liên kết nội bộ để loại bỏ mọi `href="#"`, link rỗng, click handler không điều hướng, URL sai hoặc phần tử nhìn giống nút nhưng không thể thao tác bằng bàn phím.

---

## 3. Kết quả mong muốn

Sau khi hoàn thành, hệ thống phải gồm hai phần:

### 3.1. Website công khai

Một website doanh nghiệp hoàn chỉnh, responsive, có các trang:

| Trang | URL đề xuất | Yêu cầu chính |
|---|---|---|
| Trang chủ | `/` | Landing page, các block lấy dữ liệu từ CMS |
| Giới thiệu | `/gioi-thieu` | Tầm nhìn, sứ mệnh, giá trị cốt lõi, nội dung công ty |
| Dịch vụ | `/dich-vu` | Danh sách toàn bộ dịch vụ, tìm hiểu chi tiết |
| Chi tiết dịch vụ | `/dich-vu/{slug}` | Nội dung, ảnh/icon, lợi ích, tính năng, CTA |
| Đội ngũ | `/doi-ngu` | Danh sách thành viên, chức danh, tiểu sử, ảnh |
| Liên hệ | `/lien-he` | Thông tin liên hệ, bản đồ tùy chọn, form liên hệ |
| Tin tức | `/tin-tuc` | Danh sách bài viết, phân trang, danh mục |
| Chi tiết tin | `/tin-tuc/{slug}` | Bài viết, ảnh đại diện, SEO, bài liên quan |
| 404 | `/404` hoặc routing tương đương | Giao diện thống nhất, có đường quay lại |

Nếu framework hiện tại có cơ chế routing khác, có thể điều chỉnh nhưng phải giữ URL tiếng Việt không dấu, dễ đọc và thống nhất.

### 3.2. Trang quản trị

Trang quản trị bảo mật tại `/admin` hoặc route phù hợp, cho phép quản trị toàn bộ nội dung website theo trải nghiệm tương tự WordPress nhưng không sao chép WordPress một cách máy móc.

Admin phải có:

- Dashboard.
- Quản lý nội dung trang chủ và các block.
- Quản lý trang tĩnh.
- Quản lý bài viết/tin tức.
- Quản lý danh mục và thẻ bài viết.
- Quản lý dịch vụ.
- Quản lý đội ngũ.
- Thư viện media: logo, icon, ảnh, file.
- Quản lý menu header/footer.
- Quản lý form liên hệ và khách hàng tiềm năng.
- Cài đặt website.
- Quản lý người dùng, vai trò và phân quyền.
- Nhật ký thao tác quan trọng.

Menu admin chỉ dùng tối đa **hai cấp**, rõ ràng, không gom tất cả chức năng vào một menu “Ứng dụng” chung.

---

## 4. Hoàn thiện header và hệ thống liên kết

### 4.1. Header desktop và mobile

Gắn link thực tế:

- Trang chủ → `/`
- Giới thiệu → `/gioi-thieu`
- Dịch vụ → `/dich-vu`
- Đội ngũ → `/doi-ngu`
- Liên hệ → `/lien-he`

Yêu cầu:

- Dùng phần tử `<a>` hoặc component Link chuẩn của framework, không dùng `<div onClick>` thay link.
- Có trạng thái active theo route.
- Logo click về trang chủ.
- Mobile menu mở/đóng đúng, khóa focus phù hợp và đóng sau khi điều hướng.
- Điều hướng bằng bàn phím được.
- Có `aria-current="page"` cho mục đang active nếu phù hợp.
- Không tạo full-page reload nếu dự án là SPA/SSR framework có router.

### 4.2. Các CTA

- “Liên hệ hợp tác” → `/lien-he#contact-form`.
- “LIÊN HỆ VỚI CHÚNG TÔI” → `/lien-he#contact-form`.
- Nút “Xem tất cả dịch vụ” → `/dich-vu`.
- Nút hoặc link “Xem chi tiết” trên mỗi dịch vụ → `/dich-vu/{slug}`.
- Card dịch vụ có thể click toàn bộ nhưng vẫn phải hỗ trợ keyboard và không tạo nested interactive element sai chuẩn.

---

## 5. Hoàn thiện khối Dịch vụ

### 5.1. Trang chủ

Khối dịch vụ trên trang chủ không được hard-code. Lấy từ database với quy tắc:

- Chỉ hiển thị dịch vụ có `Status = Published` và `IsFeatured = true`.
- Mặc định hiển thị tối đa 4 dịch vụ theo `SortOrder`.
- Nếu không đủ 4, hiển thị số lượng hiện có và giữ layout cân đối.
- Mỗi card gồm:
  - Logo hoặc icon.
  - Tên dịch vụ.
  - Mô tả ngắn.
  - Link chi tiết.
- Có hover/focus state rõ ràng.
- Ảnh/icon có `alt` hợp lý.
- Có fallback khi ảnh lỗi hoặc chưa upload.

### 5.2. Trang danh sách dịch vụ

- Hero tiêu đề và mô tả có thể chỉnh trong admin.
- Danh sách tất cả dịch vụ đang publish.
- Sắp xếp theo `SortOrder`, sau đó theo thời gian tạo.
- Có thiết kế responsive 1/2/3/4 cột tùy viewport.
- Có CTA liên hệ tư vấn.
- Không cần filter phức tạp nếu số lượng dịch vụ hiện ít, nhưng kiến trúc phải cho phép mở rộng.

### 5.3. Trang chi tiết dịch vụ

Tối thiểu hỗ trợ các trường:

- Tên dịch vụ.
- Slug.
- Mô tả ngắn.
- Nội dung chi tiết.
- Logo/icon.
- Ảnh cover.
- Gallery tùy chọn.
- Danh sách tính năng/lợi ích.
- URL website sản phẩm hoặc URL tải ứng dụng nếu có.
- CTA text và CTA URL.
- Trạng thái publish.
- Dịch vụ nổi bật.
- Thứ tự hiển thị.
- SEO title, SEO description, OG image, canonical URL.

Seed bốn dịch vụ hiện có từ website vào SQL Server để giao diện không mất nội dung sau khi chuyển sang CMS.

---

## 6. Xây dựng trang Đội ngũ

### Website công khai

- Tạo trang `/doi-ngu`.
- Hiển thị thành viên theo nhóm/chức danh hoặc thứ tự thiết lập.
- Mỗi thành viên có:
  - Họ tên.
  - Chức danh.
  - Ảnh đại diện.
  - Tiểu sử ngắn.
  - Tiểu sử chi tiết tùy chọn.
  - Email, điện thoại hoặc social link tùy chọn.
  - `IsFeatured`, `Status`, `SortOrder`.
- Không hiển thị trường trống hoặc link social rỗng.
- Ảnh có tỉ lệ thống nhất, lazy-load và alt text.

### Admin

- CRUD thành viên.
- Upload/chọn ảnh từ media library.
- Kéo thả hoặc nhập thứ tự hiển thị.
- Bật/tắt hiển thị.
- Preview trước khi publish nếu kiến trúc cho phép.

---

## 7. Xây dựng trang Liên hệ và quản lý lead

### Website công khai

Trang `/lien-he` gồm:

- Tên công ty.
- Địa chỉ.
- Số điện thoại click gọi được bằng `tel:`.
- Email click gửi được bằng `mailto:`.
- Thời gian làm việc tùy chọn.
- Link Google Maps hoặc iframe map chỉ khi admin cấu hình.
- Form liên hệ có id `contact-form`.

Form tối thiểu:

- Họ tên.
- Số điện thoại.
- Email.
- Công ty tùy chọn.
- Dịch vụ quan tâm tùy chọn.
- Nội dung.
- Checkbox đồng ý chính sách dữ liệu nếu có chính sách.

Yêu cầu form:

- Validate cả client và server.
- Chống spam bằng honeypot và rate limit; có thể bổ sung CAPTCHA qua cấu hình, không hard-code provider.
- Hiển thị trạng thái loading/success/error rõ ràng.
- Không mất dữ liệu đã nhập nếu API lỗi.
- Lưu tất cả submission hợp lệ vào SQL Server.
- Có thể gửi email thông báo qua cấu hình SMTP; nếu chưa có SMTP thì việc lưu database vẫn phải hoạt động.
- Không log nội dung nhạy cảm không cần thiết.

### Admin quản lý liên hệ

- Danh sách lead với tìm kiếm, lọc theo trạng thái và thời gian.
- Trạng thái: `New`, `InProgress`, `Resolved`, `Spam`, `Archived`.
- Xem chi tiết.
- Ghi chú nội bộ.
- Gán người phụ trách nếu hệ thống có nhiều user.
- Export CSV có kiểm soát quyền.
- Đánh dấu spam hoặc lưu trữ, không xóa cứng mặc định.

---

## 8. Hoàn thiện footer

Footer phải được lấy từ CMS và hiển thị thống nhất trên mọi trang.

### Cấu trúc đề xuất

1. **Nhận diện công ty**
   - Logo.
   - Mô tả ngắn.
   - Social links.

2. **Liên kết nhanh**
   - Trang chủ.
   - Giới thiệu.
   - Dịch vụ.
   - Đội ngũ.
   - Tin tức.
   - Liên hệ.

3. **Dịch vụ**
   - Lấy động từ danh sách dịch vụ publish.
   - Chỉ hiển thị tối đa số lượng do admin cấu hình.

4. **Liên hệ**
   - Điện thoại.
   - Email.
   - Địa chỉ.

5. **Footer bottom**
   - Copyright tự động theo năm hiện tại.
   - Chính sách bảo mật.
   - Điều khoản sử dụng nếu có.

### Quy tắc

- Menu footer lấy từ bảng menu/menu item hoặc cấu hình CMS, không hard-code.
- Social link rỗng thì không render icon.
- Link ngoài dùng `target="_blank"` kèm `rel="noopener noreferrer"`.
- Link điện thoại/email dùng đúng protocol.
- Admin có thể chỉnh logo footer khác logo header.
- Admin có thể chỉnh mô tả, copyright và bật/tắt từng nhóm.

---

## 9. CMS quản trị nội dung theo hướng WordPress

### 9.1. Dashboard

Hiển thị tối thiểu:

- Số bài viết đang publish/draft.
- Số dịch vụ đang publish.
- Số thành viên đang hiển thị.
- Số liên hệ mới.
- Bài viết chỉnh sửa gần đây.
- Lead mới gần đây.
- Shortcut tạo bài viết, dịch vụ và thành viên.

### 9.2. Quản lý trang và block nội dung

Không xây một visual page builder quá phức tạp trong phiên bản đầu. Xây cơ chế **structured block editor** an toàn:

- Mỗi trang có nhiều section/block.
- Block có `BlockType`, `Title`, `DataJson`, `SortOrder`, `IsEnabled`.
- `DataJson` phải được validate bằng schema theo từng loại block.
- Không cho chèn JavaScript tùy ý.
- Nội dung rich text phải được sanitize chống XSS.
- Cho phép reorder block.
- Cho phép bật/tắt block.
- Có preview.

Các block trang chủ tối thiểu:

- Hero.
- Logo/đối tác.
- Giới thiệu/tầm nhìn/sứ mệnh.
- Giá trị cốt lõi.
- Dịch vụ nổi bật.
- Số liệu nổi bật.
- Lý do lựa chọn.
- CTA liên hệ.
- Tải ứng dụng TaskFlow.
- Tin tức mới nhất.

Các block phải sử dụng dữ liệu có cấu trúc; với block dịch vụ và tin tức, dùng query từ bảng tương ứng thay vì copy nội dung.

### 9.3. Bài viết/Tin tức

Hỗ trợ:

- Tạo, sửa, xóa mềm bài viết.
- Tiêu đề, slug, excerpt, nội dung rich text.
- Ảnh đại diện.
- Danh mục.
- Thẻ.
- Tác giả.
- Trạng thái `Draft`, `Published`, `Archived`.
- Ngày publish; hỗ trợ hẹn giờ nếu framework/job infrastructure phù hợp.
- Bài nổi bật.
- SEO title, description, canonical, OG image.
- Preview bản nháp.
- Tìm kiếm, lọc, phân trang trong admin.
- Trang danh sách bài viết công khai có phân trang.
- Bài liên quan theo danh mục hoặc thẻ.

### 9.4. Media Library

Quản lý:

- Logo.
- Icon.
- Ảnh bài viết.
- Ảnh dịch vụ.
- Ảnh đội ngũ.
- QR code/App Store/Google Play image.
- File tài liệu nếu cần.

Yêu cầu:

- Lưu file trên filesystem hoặc object storage; **SQL Server chỉ lưu metadata và URL**, không lưu toàn bộ file binary vào DB trừ khi dự án hiện tại đã có yêu cầu đặc biệt.
- Có abstraction để sau này chuyển sang S3-compatible storage.
- Validate MIME type thực tế, extension, kích thước file và giới hạn dung lượng.
- Tạo tên file an toàn, tránh path traversal và ghi đè.
- Lưu width, height, size, MIME, alt text, caption.
- Tạo thumbnail hoặc phiên bản ảnh tối ưu nếu stack hỗ trợ.
- Không xóa file đang được sử dụng; cảnh báo dependency.

### 9.5. Menu Manager

- Quản lý menu Header và Footer.
- Menu item gồm label, loại link, URL/page/service/post liên kết, thứ tự, parent tối đa một cấp, mở tab mới, trạng thái.
- Không cho tạo menu sâu quá hai cấp.
- Validate URL nội bộ/ngoài.
- Preview menu trước khi lưu.

### 9.6. Cài đặt website

Nhóm cài đặt:

- General: tên website, tagline, logo, favicon.
- Contact: tên công ty, điện thoại, email, địa chỉ, map URL, giờ làm việc.
- Social: Facebook, LinkedIn, YouTube, Zalo hoặc các mạng cần thiết.
- SEO mặc định: title template, description, OG image, robots.
- Footer: mô tả, copyright, số lượng dịch vụ hiển thị.
- App download: App Store URL, Google Play URL, QR images.
- Email/SMTP: đọc từ environment hoặc secret store; admin chỉ hiển thị trạng thái cấu hình, không trả password về frontend.

### 9.7. Người dùng, vai trò và phân quyền

Tối thiểu có các role:

- `SuperAdmin`: toàn quyền.
- `Admin`: quản trị nội dung và cấu hình, hạn chế thao tác hệ thống nhạy cảm.
- `Editor`: bài viết, trang, dịch vụ, đội ngũ và media.
- `Author`: quản lý bài viết của chính mình.
- `Viewer` tùy chọn: chỉ xem báo cáo/lead.

Dùng permission chi tiết ở backend, không chỉ ẩn menu frontend.

### 9.8. Audit log

Ghi nhận các thao tác quan trọng:

- Đăng nhập thành công/thất bại bất thường.
- Tạo/sửa/publish/archive nội dung.
- Thay đổi menu/cài đặt.
- Thay đổi user/role.
- Export lead.

Không ghi password, token, connection string hoặc nội dung secret vào log.

---

## 10. Thiết kế dữ liệu Microsoft SQL Server

Hãy điều chỉnh tên bảng theo convention của dự án, nhưng tối thiểu cần các entity sau:

### 10.1. Hệ thống và xác thực

- `Users`
- `Roles`
- `Permissions`
- `UserRoles`
- `RolePermissions`
- `RefreshTokens` hoặc session store nếu kiến trúc dùng token/session.
- `AuditLogs`

### 10.2. Nội dung

- `Pages`
- `PageBlocks`
- `Posts`
- `PostCategories`
- `PostCategoryMappings`
- `Tags`
- `PostTagMappings`
- `Services`
- `ServiceFeatures`
- `ServiceMediaMappings`
- `TeamMembers`
- `MediaAssets`

### 10.3. Menu và cấu hình

- `Menus`
- `MenuItems`
- `SiteSettings`
- `SocialLinks`

### 10.4. Liên hệ

- `ContactSubmissions`
- `ContactNotes`

### 10.5. Trường dữ liệu chung

Các bảng nội dung nên có:

- `Id` dùng loại phù hợp với convention hiện tại.
- `CreatedAt`, `CreatedBy`.
- `UpdatedAt`, `UpdatedBy`.
- `DeletedAt`, `DeletedBy` nếu dùng soft delete.
- `RowVersion` kiểu `rowversion` để optimistic concurrency nếu phù hợp.

### 10.6. Quy tắc SQL Server

- Dùng migration chính thức của ORM/framework.
- Tạo unique index cho slug theo loại nội dung.
- Tạo index cho `Status`, `PublishedAt`, `SortOrder`, `CreatedAt` và các foreign key thường query.
- Dùng Unicode `nvarchar` cho tiếng Việt.
- Dùng UTC trong database; chuyển timezone tại presentation layer.
- Dùng `datetime2`, không dùng `datetime` cũ nếu không có lý do tương thích.
- Dùng `decimal` với precision rõ ràng nếu có số liệu số học.
- Không ghép chuỗi SQL thủ công; dùng parameterized query/ORM.
- `DataJson` dùng `nvarchar(max)` và validate JSON ở application; có thể dùng `ISJSON` constraint nếu phù hợp.
- Thiết lập cascade delete thận trọng; ưu tiên restrict/soft delete với dữ liệu nội dung và lead.
- Tạo seed idempotent cho nội dung hiện tại.

### 10.7. Kết nối database

- Chuỗi kết nối lấy từ environment variable hoặc secret store.
- Cập nhật `.env.example`/config mẫu không có secret thật.
- Có health check database.
- Có retry có giới hạn khi startup nếu framework hỗ trợ.
- Không tự động chạy destructive migration trên production.
- Viết hướng dẫn backup SQL Server trước khi deploy schema mới.

---

## 11. API và backend

Thiết kế API theo convention hiện tại. Tối thiểu cần:

### Public API

- Lấy site settings công khai.
- Lấy menu công khai.
- Lấy page/block đã publish.
- Danh sách/chi tiết dịch vụ publish.
- Danh sách/chi tiết thành viên publish.
- Danh sách/chi tiết bài viết publish.
- Gửi form liên hệ.

### Admin API

- Authentication/logout/refresh/me.
- CRUD pages và blocks.
- CRUD posts/categories/tags.
- CRUD services/features/media.
- CRUD team members.
- CRUD media assets.
- CRUD menu/menu items.
- Site settings.
- Contact submissions/notes/status/export.
- User/role/permission management.
- Audit log read-only theo quyền.

### Yêu cầu API

- Pagination, sort, filter và search chuẩn hóa.
- DTO/schema validation ở server.
- Error response nhất quán.
- Không trả stack trace production.
- Không over-posting; chỉ nhận field cho phép.
- Authorization ở từng endpoint.
- Rate limit login, contact form và upload.
- OpenAPI/Swagger nếu backend hiện tại hỗ trợ.

---

## 12. Bảo mật bắt buộc

- Hash password bằng thuật toán chuẩn của framework như Argon2id hoặc bcrypt với cost phù hợp.
- Cookie auth phải dùng `HttpOnly`, `Secure`, `SameSite` phù hợp và CSRF protection.
- JWT phải có expiry ngắn, refresh token rotation và revoke nếu dùng JWT.
- Chống brute-force login bằng rate limiting/lockout có kiểm soát.
- Sanitize rich-text HTML.
- Encode output đúng ngữ cảnh.
- Chống SQL injection bằng ORM/parameterized query.
- Validate và giới hạn upload.
- Không cho upload SVG không kiểm soát; nếu cần SVG phải sanitize.
- Security headers: CSP phù hợp, HSTS ở production, X-Content-Type-Options, Referrer-Policy, frame-ancestors.
- Không cho role thấp tự nâng quyền.
- Không tạo tài khoản admin mặc định với password dễ đoán trong production.
- Seed admin đầu tiên bằng biến môi trường hoặc lệnh CLI một lần.
- Không expose endpoint admin/content draft cho người chưa đăng nhập.

---

## 13. SEO, hiệu năng và accessibility

### SEO

- Title, meta description theo từng trang.
- Canonical URL.
- Open Graph/Twitter metadata.
- Sitemap XML cho page, service và post đã publish.
- `robots.txt` phù hợp; chặn index `/admin`.
- Structured data `Organization`, `WebSite`, `BreadcrumbList`, `Article` và `Service` khi phù hợp.
- Slug ổn định, redirect khi đổi slug nếu triển khai được an toàn.

### Hiệu năng

- Responsive image, lazy-loading dưới fold.
- Không lazy-load logo/LCP image quan trọng một cách gây chậm.
- Cache public settings/menu/content hợp lý và invalidate khi admin cập nhật.
- Tránh N+1 query.
- Phân trang server-side.
- Minify/build production theo stack.
- Giữ giao diện nhanh trên mobile.

### Accessibility

- Semantic HTML.
- Heading hierarchy đúng.
- Focus state rõ.
- Keyboard navigation.
- Label và error message cho form.
- Alt text cho ảnh nội dung.
- Màu sắc đủ tương phản.
- Không dùng màu đơn thuần để biểu thị trạng thái.

---

## 14. UI/UX admin

- Giữ giao diện sạch, hiện đại, dễ dùng với người không chuyên kỹ thuật.
- Sidebar tối đa hai cấp.
- Có breadcrumb.
- List page có search/filter/pagination.
- Form chia section hợp lý.
- Có dirty-state warning khi rời trang chưa lưu.
- Có confirm cho thao tác archive/delete.
- Có toast nhưng lỗi quan trọng phải hiện gần field hoặc khu vực liên quan.
- Upload media có progress và preview.
- Trạng thái Draft/Published/Archived có badge rõ ràng.
- Hỗ trợ desktop và tablet; mobile admin ở mức sử dụng được.
- Không copy giao diện WordPress; chỉ học cách tổ chức nội dung và quy trình quản trị.

---

## 15. Migration dữ liệu hiện tại

Tạo seed/migration idempotent để đưa nội dung tĩnh hiện tại vào database:

- Thông tin công ty và tagline.
- Tầm nhìn và sứ mệnh.
- Năm giá trị cốt lõi hiện có.
- Bốn dịch vụ SmartLabel, TaskFlow, MiniCRM, Farm2Home.
- Thông tin liên hệ hiện tại:
  - Điện thoại: `0936685886`
  - Email: `hung.duy@happyecotech.com`
  - Địa chỉ: `198 Trường Chinh, Hải Phòng`
- Link App Store/Google Play hiện tại nếu đang tồn tại trong source.
- Logo/icon/image hiện tại chuyển vào media metadata mà không làm mất asset.
- Menu header/footer.

Các số liệu thống kê hiện có phải đưa vào block quản trị, nhưng thêm ghi chú trong admin hoặc tài liệu rằng cần xác minh trước khi tiếp tục dùng trên production.

---

## 16. Kiểm thử bắt buộc

### 16.1. Automated test

Tùy stack, bổ sung unit/integration/E2E cho các luồng quan trọng:

1. Public chỉ lấy nội dung Published.
2. Slug không trùng.
3. RBAC chặn đúng quyền.
4. CRUD service/post/team/page.
5. Upload từ chối file sai loại/quá dung lượng.
6. Contact form validate, rate limit và lưu database.
7. Menu render đúng thứ tự.
8. Site settings không trả secret.
9. Soft delete không xuất hiện ở public.
10. Seed chạy lại không tạo dữ liệu trùng.

### 16.2. Manual/E2E checklist

- Tất cả menu header hoạt động trên desktop/mobile.
- Logo về trang chủ.
- Tất cả link footer hoạt động.
- Card dịch vụ và nút “Xem tất cả dịch vụ” hoạt động.
- CTA liên hệ cuộn đúng tới form.
- Admin đăng nhập/đăng xuất.
- Tạo draft → preview → publish bài viết.
- Tạo dịch vụ và bật nổi bật → xuất hiện trang chủ.
- Sửa logo/icon/footer → website cập nhật.
- Tạo thành viên → xuất hiện trang Đội ngũ.
- Gửi form liên hệ → admin nhận lead.
- Test 404, link hỏng và ảnh lỗi.
- Test viewport tối thiểu 375px, 768px, 1440px.
- Test Chrome, Safari và Edge bản hiện hành.

### 16.3. Quality gate

Trước khi kết thúc phải chạy thành công các lệnh tương ứng của dự án:

- Install dependencies.
- Lint.
- Type-check.
- Unit/integration test.
- Production build.
- Migration trên database test sạch.
- Seed trên database test sạch.

Không báo “hoàn thành” nếu build hoặc migration còn lỗi.

---

## 17. Triển khai production an toàn

Tạo tài liệu `docs/DEPLOY_HAPPY_ECOTECH_CMS.md` gồm:

1. Điều kiện môi trường.
2. Biến môi trường cần thiết.
3. Cách tạo database/login SQL Server với quyền tối thiểu.
4. Cách backup database hiện tại.
5. Cách chạy migration.
6. Cách seed nội dung.
7. Cách tạo SuperAdmin đầu tiên.
8. Cách build frontend/backend.
9. Cách restart service/container.
10. Health check và smoke test.
11. Kế hoạch rollback ứng dụng và database.
12. Vị trí lưu media và phương án backup media.

Nếu hệ thống đang chạy production:

- Không chỉnh trực tiếp dữ liệu production khi chưa backup.
- Ưu tiên deploy staging trước.
- Migration phải backward-compatible trong bước đầu nếu có thể.
- Không thay toàn bộ trang bằng CMS khi API/database chưa sẵn sàng.
- Có rollback rõ ràng.

---

## 18. Tài liệu phải tạo trong repository

Tối thiểu tạo/cập nhật:

- `docs/HAPPY_ECOTECH_CURRENT_AUDIT.md`
- `docs/HAPPY_ECOTECH_CMS_ARCHITECTURE.md`
- `docs/HAPPY_ECOTECH_DATABASE_SCHEMA.md`
- `docs/HAPPY_ECOTECH_ADMIN_GUIDE.md`
- `docs/DEPLOY_HAPPY_ECOTECH_CMS.md`
- `.env.example` hoặc file config mẫu tương ứng.
- Migration và seed script.

Audit phải ghi rõ stack thực tế, route hiện tại, điểm yếu, quyết định kiến trúc và lý do. Không dùng mô tả chung chung.

---

## 19. Trình tự thực hiện

### Phase 0 — Audit và bảo vệ hiện trạng

- Đọc repository.
- Chạy ứng dụng local.
- Chụp/ghi nhận giao diện và route hiện tại.
- Kiểm tra link hỏng.
- Xác định stack và database hiện tại.
- Tạo nhánh/backup phù hợp.
- Viết audit ngắn, sau đó tiếp tục triển khai.

### Phase 1 — SQL Server và backend CMS

- Cấu hình SQL Server.
- Tạo entity/schema/migration/index.
- Auth + RBAC.
- API public/admin.
- Seed dữ liệu hiện tại.
- Test backend.

### Phase 2 — Admin

- Layout và navigation admin.
- Dashboard.
- CRUD page/block, service, team, post, media, menu, settings.
- Contact lead.
- User/role.
- Audit log.

### Phase 3 — Website công khai

- Routing đầy đủ.
- Header/footer động.
- Dịch vụ/list/detail.
- Đội ngũ.
- Liên hệ/form.
- Tin tức.
- SEO, sitemap, 404.

### Phase 4 — QA, tối ưu và deploy docs

- Automated test.
- E2E/manual checklist.
- Responsive/accessibility/security review.
- Build production.
- Tài liệu deploy/rollback/admin guide.

---

## 20. Tiêu chí nghiệm thu — Definition of Done

Công việc chỉ được coi là hoàn thành khi:

- [ ] Menu Đội ngũ và Liên hệ có link thực tế và trang tương ứng.
- [ ] Header và mobile menu không có link rỗng.
- [ ] Footer hoàn chỉnh, tất cả link hợp lệ và lấy từ CMS.
- [ ] Bốn card dịch vụ click được và có trang chi tiết.
- [ ] Nút “Xem tất cả dịch vụ” dẫn tới trang danh sách.
- [ ] CTA liên hệ dẫn tới form hoạt động.
- [ ] Admin đăng nhập an toàn.
- [ ] Admin quản lý được logo, icon, media và favicon.
- [ ] Admin quản lý được nội dung trang và block trang chủ.
- [ ] Admin quản lý được dịch vụ.
- [ ] Admin quản lý được đội ngũ.
- [ ] Admin đăng bài/tin, danh mục, thẻ và SEO.
- [ ] Admin quản lý menu header/footer.
- [ ] Admin quản lý thông tin website và footer.
- [ ] Contact form lưu lead vào SQL Server.
- [ ] Có RBAC và backend authorization.
- [ ] Có migration, seed và backup/rollback guide.
- [ ] Dữ liệu tĩnh hiện tại đã được seed, không mất nội dung.
- [ ] Website responsive, keyboard-accessible và không có lỗi console nghiêm trọng.
- [ ] Lint, type-check, test và production build đều pass.
- [ ] Tài liệu kiến trúc, database, admin và deploy đã hoàn thiện.

---

## 21. Báo cáo kết quả cuối cùng của Claude

Khi hoàn thành, trả lời theo cấu trúc:

1. **Tóm tắt kiến trúc thực tế đã phát hiện**.
2. **Những hạng mục đã triển khai**.
3. **Danh sách migration/schema đã tạo**.
4. **Danh sách route public và admin**.
5. **Các file chính đã thay đổi**.
6. **Kết quả lint/test/build** với lệnh và trạng thái.
7. **Cách chạy local**.
8. **Cách deploy production**.
9. **Các biến môi trường cần cấu hình** nhưng không được in secret.
10. **Rủi ro hoặc việc cần chủ website xác nhận**, đặc biệt là số liệu thống kê và nội dung pháp lý.

Không được kết thúc bằng báo cáo chung chung kiểu “đã hoàn thiện giao diện”. Phải nêu rõ phần nào đã chạy, phần nào đã test và bằng chứng kỹ thuật tương ứng.
