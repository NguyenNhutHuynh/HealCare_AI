# Healcare

Website PHP thuần về tư vấn dinh dưỡng, được tổ chức theo mô hình MVC.

## Cấu trúc

```text
app/
  Controllers/   Điều phối trang và API chat
  Core/          Autoload và helper
  Repositories/  Đọc dữ liệu nội bộ
  Services/      Tích hợp OpenAI
  Views/         Layout và từng trang
assets/          CSS và JavaScript
assets/images/   Ảnh minh họa hero, thực phẩm và món ăn
data/            Cơ sở dữ liệu nội bộ dạng PHP
index.php        Front controller
```

## Chạy dự án

```powershell
cd E:\laragon\www\Healcare
php -S localhost:8000
```

Mở `http://localhost:8000`.

## Bật AI thật

AI chạy qua OpenAI Responses API. Đặt API key ở biến môi trường, không ghi trực tiếp vào mã nguồn:

```powershell
setx OPENAI_API_KEY "sk-your-key"
setx OPENAI_MODEL "gpt-5-mini"
```

Sau khi chạy `setx`, hãy mở lại Laragon/terminal rồi khởi động lại PHP. Nếu chưa có key, trang chat vẫn dùng câu trả lời dự phòng từ dữ liệu nội bộ.

## Liên hệ bác sĩ thực tế

- Mở `index.php?page=contact` để xem các cơ sở y tế công khai, nút gọi cấp cứu 115 và gửi yêu cầu liên hệ.
- Form liên hệ chỉ gửi email khi đã cấu hình `HEALCARE_CONTACT_EMAIL` và máy chủ PHP có mail/SMTP. Nếu chưa cấu hình, hệ thống vẫn ghi nhận yêu cầu trên phiên làm việc nhưng chưa thể chuyển đến bệnh viện.

```powershell
setx HEALCARE_CONTACT_EMAIL "tiepnnhan@example.com"
```

Thông tin cơ sở y tế cần được kiểm tra lại trên website chính thức trước khi liên hệ. Healcare chỉ hỗ trợ tra cứu và giáo dục dinh dưỡng, không thay thế chẩn đoán hoặc điều trị của bác sĩ.

## Hồ sơ và gợi ý món ăn

- Vào `index.php?page=profile` để nhập tuổi, bệnh đang theo dõi, dị ứng và thói quen ăn uống.
- Vào `index.php?page=recommendations` để xem món đã lọc, cách nấu từng bước và mở video YouTube.
- Nút `Nhờ AI lập thực đơn hôm nay` sẽ dùng hồ sơ hiện tại để gọi AI; nếu chưa có API key, danh sách món nội bộ vẫn hoạt động.

## Khu vực quản trị và CRM

- Mở `index.php?page=admin` để vào trang quản trị riêng.
- CRM lưu các yêu cầu từ trang liên hệ vào `data/storage/crm_contacts.php`.
- Admin có dashboard, bộ lọc liên hệ, trạng thái `Mới`, `Đang xử lý`, `Hoàn tất`, `Lưu trữ`, mức ưu tiên và ghi chú nội bộ.
- Kho nội dung hiển thị số lượng bệnh, thực phẩm, công thức và cơ sở y tế. Nội dung gốc vẫn nằm trong thư mục `data/` để dễ chuyển sang MySQL về sau.

Tài khoản mặc định cho môi trường phát triển là `admin` / `Healcare@2026`. Trước khi triển khai thật, hãy cấu hình tài khoản riêng:

```powershell
setx HEALCARE_ADMIN_USER "admin"
setx HEALCARE_ADMIN_PASSWORD "mat-khau-manh-cua-ban"
```

Không chia sẻ tài khoản quản trị và không sử dụng mật khẩu mặc định trên máy chủ công khai.
