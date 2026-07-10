# HAPPY ECOTECH — Hướng dẫn quản trị (Admin)

Truy cập: `https://<domain>/admin` — đăng nhập bằng tài khoản được cấp.

## 1. Vai trò
| Role | Phạm vi |
|---|---|
| SuperAdmin | Toàn quyền, gồm quản lý user/role và cài đặt |
| Admin | Toàn bộ nội dung + cài đặt, không xoá cứng, không sửa role |
| Editor | Dịch vụ, bài viết, trang, đội ngũ, media, menu, danh mục, thẻ + đọc/xuất lead |
| Author | Chỉ bài viết của chính mình + media (không publish) |
| Viewer | Chỉ xem báo cáo/nội dung/lead |

## 2. Cấu trúc menu (2 cấp)
- **Nội dung**: Trang, Bài viết, Danh mục, Thẻ, Dịch vụ, Đội ngũ.
- **Thư viện**: Thư viện Media.
- **Giao diện & Menu**: Menu, Cài đặt website.
- **Liên hệ**: Liên hệ / Lead.
- **Hệ thống**: Người dùng, Vai trò, Nhật ký.

## 3. Tác vụ thường gặp

**Trang chủ / block** — *Nội dung → Trang → Trang chủ*. Mỗi trang có nhiều block (Hero, Giá trị cốt lõi, Dịch vụ nổi bật, Số liệu, CTA, Tải ứng dụng, Tin tức...). Bật/tắt và sắp thứ tự trong tab *Các block nội dung*. Block "Số liệu" đang **tắt** và cần **xác minh số liệu** trước khi bật (số cũ là placeholder của template).

**Dịch vụ** — *Nội dung → Dịch vụ*. Đặt `Trạng thái = Đã đăng` + bật *Nổi bật* để hiện ở trang chủ (tối đa 4 theo thứ tự). Thêm tính năng/lợi ích trong tab con.

**Bài viết** — *Nội dung → Bài viết*. Nhập tiêu đề (slug tự sinh), nội dung (rich-text được sanitize), ảnh đại diện, danh mục, thẻ, SEO. Chọn `Đã đăng` + `Thời gian đăng` để hiển thị công khai.

**Đội ngũ** — *Nội dung → Đội ngũ*. `Đã đăng` để hiện ở `/doi-ngu`. Ảnh chọn từ Media Library. Trường trống/không có social sẽ không hiển thị.

**Media Library** — *Thư viện → Thư viện Media*. Upload tự điền mime/kích thước/dung lượng. Không xoá được media đang được tham chiếu (hệ thống cảnh báo).

**Menu** — *Giao diện & Menu → Menu*. Sửa menu `header` và `footer_quick`; mỗi mục chọn loại liên kết (route/trang/dịch vụ/bài viết/URL), thứ tự, mở tab mới.

**Cài đặt website** — *Giao diện & Menu → Cài đặt website*. Các tab: Chung (tên/logo/favicon), Liên hệ (điện thoại/email/địa chỉ/map), Mạng xã hội, SEO, Footer, Tải ứng dụng, Email/SMTP (chỉ hiển thị trạng thái, không nhập mật khẩu).

**Liên hệ / Lead** — *Liên hệ*. Xem chi tiết, đổi trạng thái, gán người phụ trách, thêm ghi chú nội bộ, **Xuất CSV** (cần quyền). Lead không xoá cứng mặc định (đánh dấu spam/lưu trữ).

**Người dùng & Vai trò** — *Hệ thống*. Tạo user, gán role; role gán quyền chi tiết. Không tự xoá tài khoản của chính mình.

**Nhật ký** — *Hệ thống → Nhật ký*. Chỉ đọc: đăng nhập/đăng xuất/đăng nhập thất bại, tạo/sửa/publish/archive nội dung, đổi cài đặt, xuất lead.

## 4. Cần chủ website xác minh
- Số liệu thống kê (khách hàng/dự án...) — hiện **tắt**, là placeholder.
- Logo đối tác, ảnh & thông tin đội ngũ thật (ảnh cũ là stock của template).
- Nội dung *Chính sách bảo mật* / *Điều khoản sử dụng* (đang là bản nháp placeholder, cần nội dung pháp lý chính thức trước khi publish).
- Liên kết mạng xã hội thật (đang tắt, chưa có URL).
