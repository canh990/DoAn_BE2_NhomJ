# TODO - Read receipts (Đã gửi / Đã nhận / Đã xem)

- [x] Back-end: thêm endpoint `mark-read` cho 1-1 và nhóm, cập nhật `thanh_vien_nhom.doc_den_luc`.
- [x] Back-end: mở rộng `formatMessage()` để trả `receipt_status` cho từng message.
- [x] Front-end (1-1): gọi `mark-read` khi load và khi scroll gần bottom; hiển thị trạng thái receipt.
- [x] Front-end (nhóm): gọi `mark-read` khi load và khi scroll gần bottom; hiển thị trạng thái receipt.

- [ ] Test: mở chat, gửi tin, ở phía đối phương nhấn mở chat để thấy chuyển trạng thái.
