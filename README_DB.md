Hướng dẫn sử dụng project GianHangXanh (đã đồng bộ với gianhangxanh.sql)

1. Import database:
   - Tạo database `gianhangxanh` và import file `gianhangxanh.sql` (đã cung cấp).
2. Cấu hình .env:
   DB_DATABASE=gianhangxanh
   DB_USERNAME=your_mysql_user
   DB_PASSWORD=your_password
3. Không cần chạy migrate/seed (database có sẵn). Nếu bạn chạy seed, có thể ghi đè dữ liệu.
4. Tạo storage link nếu cần:
   php artisan storage:link
5. Khởi động server:
   php artisan serve

Ảnh sản phẩm đã được thêm vào public/storage/products (noicom.jpg, dao.jpg, ...)
