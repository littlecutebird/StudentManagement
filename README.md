# StudentManagement
A website support upload and download homework, manage student profiles and chat with other users. Create with PHP and MySQL.

# Yêu cầu:
Lập trình bằng ngôn ngữ PHP (yêu cầu không sử dụng framework có sẵn),
sử dụng DB MySQL để xây dựng website quản lý thông tin sinh viên, tài liệu
của 1 lớp học.

Yêu cầu ứng dụng:
- Giao diện website rõ ràng, sạch đẹp (có sử dụng HTML, CSS để định
dạng và thiết kế website) (1đ)
- Đăng ký tài khoản và tạo project trên github để quản lý code (0.5đ)
- Deploy ứng dụng lên server public (0.5đ)

Yêu cầu chức năng:
- Giáo viên có thể thêm, sửa, xóa các thông tin của sinh viên. Thông tin có
các trường cơ bản gồm: tên đăng nhập, mật khẩu, họ tên, email, số điện
thoại (1đ)
- Sinh viên sau khi đăng nhập được phép thay đổi các thông tin của mình
trừ tên đăng nhập và họ tên (1đ).
- Một người dùng (giáo viên hoặc sinh viên) bất kỳ đc phép xem danh
sách các người dùng trên website và xem thông tin chi tiết của một
người dùng khác. Tại trang xem thông tin chi tiết của một người dùng có
mục để lại tin nhắn cho người dùng đó, có thể sửa/xóa tin nhắn đã gửi
(2đ).
- Chức năng giao bài, trả bài:
  - Giáo viên có thể upload file bài tập lên. Các sinh viên có thể xem
danh sách bài tập và tải file bài tập về (1đ).
  - Sinh viên có thể upload bài làm tương ứng với bài tập được giao. Chỉ giáo viên mới nhìn thấy danh sách bài làm này (1đ).
- Tạo chức năng cho phép giáo viên tổ chức 1 trò chơi giải đố như sau:
  - Giáo viên tạo challenge, trong đó cần thực hiện: upload lên 1 file
txt có nội dung là 1 bài thơ, văn,…, tên file được viết dưới định
dạng không dấu và các từ cách nhau bởi 1 khoảng trắng. Sau đó
nhập gợi ý về challenge và submit. (Đáp án chính là tên file mà
giáo viên upload lên. Không lưu đáp án ra file, DB,…) (1đ)
  - Sinh viên xem gợi ý và nhập đáp án. Khi sinh viên nhập đúng thì
trả về nội dung bài thơ, văn,… lưu trong file đáp án (1đ).

# How I deploy this website to public
- I use 000webhost. 
- After upload file to web host, I need to change database name, username, host, password in db_config.php because the database and username in 000webhost is different from my local database. They are something like id123456_databaseName and I have no clue to change those names.
- Then I import my local database to web host by phpmyadmin. The database file is studentmanagement.sql
- Now everything should be ok. Just enjoy the website.
