# Kho dữ liệu Healcare

Các file PHP trong thư mục này đang đóng vai trò cơ sở dữ liệu nội bộ:

- `diseases.php`: bệnh và thực phẩm nên ăn/hạn chế.
- `disease_details.php`: nguyên nhân thường gặp và cách hạn chế/phòng ngừa theo từng bệnh.
- `foods.php`: thư viện thực phẩm.
- `recipes.php`: công thức món ăn.
- `disease_recipes.php`: các món bổ sung được gắn riêng cho bệnh tim mạch, gút, gan nhiễm mỡ, thiếu máu và bệnh thận mạn; các món có ghi chú cá nhân hóa khi cần.
- `recipe_images.php` và `food_images.php`: ánh xạ tên nội dung với ảnh minh họa riêng trong `assets/images/`.
- Mỗi công thức có `ingredients`, `steps`, `suitable_for`, `image` và `youtube_query` để tạo gợi ý cá nhân hóa và link video.

Mỗi file trả về một mảng PHP và được nạp bằng `require`. Khi dự án lớn hơn, có thể chuyển các mảng này sang MySQL mà không cần thay đổi giao diện.

`storage/crm_contacts.php` là dữ liệu vận hành của CRM. File này chứa yêu cầu liên hệ do người dùng gửi, trạng thái xử lý và ghi chú nội bộ; cần giới hạn quyền truy cập khi triển khai lên máy chủ thật và sao lưu định kỳ.
