# Tính Năng Tìm Kiếm Tin Nhắn (Message Search Feature)

## Tổng Quan

Tính năng này cho phép người dùng tìm kiếm tin nhắn theo từ khóa trong cả chat 1-1 và chat nhóm. Kết quả hiển thị dạng dropdown tức thời với khả năng nhảy tới tin nhắn được chọn.

## 📋 Các Tệp Được Sửa Đổi

### 1. **Controllers**

- `app/Http/Controllers/ChatController.php` - Thêm method `searchMessages()` cho chat 1-1
- `app/Http/Controllers/GroupChatController.php` - Thêm method `searchMessages()` cho chat nhóm

### 2. **Routes**

- `routes/chat.php` - Thêm 2 route mới:
    - `GET /chat1-1/conversations/{conversation}/search` → `ChatController@searchMessages`
    - `GET /chat-groups/{conversation}/search` → `GroupChatController@searchMessages`

### 3. **Views**

- `resources/views/message/chat1-1.blade.php` - Thêm search UI và JavaScript
- `resources/views/message/group.blade.php` - Thêm search UI và JavaScript

## 🔍 Cách Thức Hoạt Động

### Chat 1-1 (Personal Chat)

1. **Search Input**: Trường tìm kiếm ở sidebar trái
2. **Real-time Search**: Tìm kiếm tự động khi người dùng gõ (với delay 300ms)
3. **Hiển Thị Kết Quả**: Dropdown dưới search box hiển thị:
    - Số lượng kết quả tìm thấy
    - Danh sách tin nhắn (tối đa 20 kết quả/trang)
    - Thời gian và nội dung tin nhắn

### Chat Nhóm (Group Chat)

1. **Search Input**: Trường tìm kiếm trong header phía trên
2. **Real-time Search**: Tìm kiếm tự động khi người dùng gõ (với delay 300ms)
3. **Hiển Thị Kết Quả**: Dropdown hiển thị:
    - Số lượng kết quả tìm thấy
    - Danh sách tin nhắn (tối đa 20 kết quả/trang)
    - Tên người gửi, thời gian và nội dung

## 🔧 Backend Logic

### Tìm Kiếm Tin Nhắn

```php
// Điều kiện tìm kiếm:
- Tìm kiếm trong cột 'noi_dung' (content)
- Sử dụng LIKE query: WHERE noi_dung LIKE '%keyword%'
- Loại bỏ tin nhắn đã bị thu hồi cho cả hai: WHERE kieu_xoa != 'ca_hai'
- Sắp xếp theo thời gian tạo (mới nhất trước): ORDER BY ngay_tao DESC
- Pagination: 20 kết quả trên một trang
```

### Validation

- `keyword`: Required, string, min 1 ký tự, max 255 ký tự

### Response Format

```json
{
    "keyword": "từ khóa",
    "total": 5,
    "messages": [
        {
            "id": 1,
            "sender_id": 2,
            "sender_name": "admin", // (chỉ cho nhóm)
            "content": "Nội dung tin nhắn",
            "attachments": [],
            "time": "14:30",
            "is_mine": false,
            "is_recalled": false,
            "is_deleted": false
        }
    ],
    "current_page": 1,
    "last_page": 1
}
```

## 🎨 Frontend Logic

### Search Handler

```javascript
// Trigger: Input change event
// Debounce: 300ms
// Fetch: GET request với query parameter 'keyword'
// Display: Dropdown list dưới search box
// Click: Scroll to message và highlight
```

### Features

- **Debounce**: Chỉ gửi request sau 300ms không nhập dữ liệu
- **Auto-close**: Tự đóng dropdown khi click bên ngoài
- **Scroll to Message**: Nhảy tới tin nhắn khi click kết quả
- **Highlight Effect**: Tin nhắn được chọn có hiệu ứng pulse
- **HTML Escape**: Ngăn XSS injection bằng cách escape HTML

### Styling

- Dropdown: `rounded-2xl border-[#1b3047] bg-[#0b1220]`
- Result item: `max-h-64 overflow-y-auto`
- Hover effect: `hover:bg-[#101827]`

## 📊 Database Query

### Ví Dụ Query

```sql
SELECT * FROM tin_nhan
WHERE cuoc_tro_chuyen_id = 1
  AND noi_dung LIKE '%hello%'
  AND kieu_xoa != 'ca_hai'
ORDER BY ngay_tao DESC
LIMIT 20 OFFSET 0;
```

## 🛡️ Authorization

### Chat 1-1

- User phải là thành viên của conversation (1-1)
- `abort_unless($conversation->loai === 'ca_nhan' && ...)`

### Chat Nhóm

- User phải là thành viên của group
- Sử dụng method `authorizeGroupMember()`

## 🔒 Security

1. **SQL Injection Prevention**: Sử dụng parameterized queries (Laravel Eloquent)
2. **XSS Prevention**: Escape HTML trong JavaScript
3. **Input Validation**: Validate keyword trước khi query
4. **Authorization**: Check quyền access trước khi tìm kiếm
5. **CSRF Protection**: Tự động trong Laravel (nếu cần)

## ✨ Features

### Chat 1-1

- [x] Tìm kiếm tin nhắn theo từ khóa
- [x] Hiển thị kết quả real-time
- [x] Nhảy tới tin nhắn
- [x] Hiệu ứng highlight
- [x] Loại bỏ tin nhắn bị thu hồi

### Chat Nhóm

- [x] Tìm kiếm tin nhắn theo từ khóa
- [x] Hiển thị tên người gửi
- [x] Hiển thị kết quả real-time
- [x] Nhảy tới tin nhắn
- [x] Hiệu ứng highlight
- [x] Loại bỏ tin nhắn bị thu hồi

## 🧪 Testing

### Manual Testing Steps

#### Chat 1-1

1. Mở chat 1-1 với một người dùng
2. Nhập từ khóa trong search box
3. Xác nhận dropdown hiển thị kết quả
4. Click vào một kết quả
5. Xác nhận tin nhắn được highlighted và hiển thị

#### Chat Nhóm

1. Mở một group chat
2. Nhập từ khóa trong search box
3. Xác nhận dropdown hiển thị kết quả
4. Kiểm tra tên người gửi được hiển thị
5. Click vào một kết quả
6. Xác nhận tin nhắn được highlighted

### Edge Cases

- [ ] Tìm kiếm trống (empty keyword)
- [ ] Tìm kiếm không có kết quả
- [ ] Tìm kiếm tin nhắn bị thu hồi
- [ ] Tìm kiếm tin nhắn chỉ có attachment
- [ ] Tìm kiếm với ký tự đặc biệt
- [ ] Tìm kiếm pagination

## 📝 API Endpoints

### Chat 1-1 Search

```
GET /chat1-1/conversations/{conversation}/search?keyword=<search_term>
```

**Headers:**

- `Accept: application/json`

**Response:** 200 OK

```json
{
  "keyword": "...",
  "total": 5,
  "messages": [...],
  "current_page": 1,
  "last_page": 1
}
```

### Chat Nhóm Search

```
GET /chat-groups/{conversation}/search?keyword=<search_term>
```

**Headers:**

- `Accept: application/json`

**Response:** 200 OK

```json
{
  "keyword": "...",
  "total": 10,
  "messages": [...],
  "current_page": 1,
  "last_page": 1
}
```

## 🚀 Performance Optimization

1. **Debouncing**: Giảm số lần request (300ms delay)
2. **Pagination**: Giới hạn 20 kết quả trên một trang
3. **LIKE Query**: Sử dụng LIKE '%keyword%' (có thể thêm fulltext search sau)
4. **Index**: Cân nhân xét thêm index trên cột `noi_dung` cho performance tốt hơn

## 🔄 Future Enhancements

1. **Full-text Search**: Thay thế LIKE query bằng FULLTEXT SEARCH
2. **Search Filters**: Tìm kiếm theo thời gian, người gửi
3. **Search History**: Lưu lịch sử tìm kiếm
4. **Advanced Search**: Hỗ trợ regex, wildcard patterns
5. **Search in Attachments**: Tìm kiếm tên file, type file

## 📞 Support

Nếu có vấn đề, kiểm tra:

1. Routes đã được đăng ký đúng
2. Controllers có method `searchMessages()`
3. Views có search input UI
4. JavaScript không bị minify/cache
5. Browser console không có errors
