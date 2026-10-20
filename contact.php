<?php
session_start();

if (file_exists('config/db.php')) {
    include 'config/db.php';
} elseif (file_exists('db.php')) {
    include 'db.php';
} else {
    $conn = new mysqli("localhost", "root", "", "web_thoi_trang_douyin");
}

$message_sent = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_msg'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contacts (name, contact_info, message) VALUES ('$name', '$contact', '$message')";
    if ($conn->query($sql)) {
        $message_sent = true;
    }
}

// Xử lý tra cứu phản hồi từ shop
$search_result = null;
if (isset($_GET['search_contact']) && !empty($_GET['search_contact'])) {
    $search_key = mysqli_real_escape_string($conn, $_GET['search_contact']);
    $search_result = $conn->query("SELECT * FROM contacts WHERE contact_info LIKE '%$search_key%' ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên hệ & Phản hồi - Flower Fashion Store</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff5f5; padding: 30px 10px; margin: 0; }
        .contact-container { max-width: 850px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h1 { color: #2d3436; text-align: center; font-size: 26px; margin-bottom: 5px; }
        .sub-title { text-align: center; color: #636e72; margin-bottom: 25px; font-size: 14px; }
        
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        @media (max-width: 600px) { .contact-grid { grid-template-columns: 1fr; } }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #2d3436; font-size: 14px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #dfe4ea; border-radius: 6px; box-sizing: border-box; font-size: 14px; }

        .btn-submit { background: #ff4757; color: white; border: none; padding: 12px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; font-size: 15px; }
        .btn-submit:hover { background: #e84118; }
        .alert-success { background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; }

        /* KHUNG TRA CỨU */
        .search-box { margin-top: 30px; padding-top: 20px; border-top: 2px dashed #ffeaa7; }
        .reply-item { background: #fafafa; border: 1px solid #dfe4ea; padding: 12px; border-radius: 8px; margin-top: 10px; }
        .shop-reply { background: #eef5ff; border-left: 3px solid #0084ff; padding: 8px; margin-top: 8px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="contact-container">
    <h1>✨ FLOWER FASHION STORE ✨</h1>
    <p class="sub-title">Liên hệ với chúng tôi để được hỗ trợ nhanh nhất!</p>

    <?php if ($message_sent): ?>
        <div class="alert-success">✅ Đã gửi tin nhắn thành công! Shop sẽ phản hồi bạn sớm nhất.</div>
    <?php endif; ?>

    <div class="contact-grid">
        <div>
            <h3>📍 Thông tin shop</h3>
            <p><strong>Địa chỉ:</strong> 123 Đường Thời Trang, Q. Cầu Giấy, Hà Nội</p>
            <p><strong>Hotline:</strong> 0987 654 321</p>
            <p><strong>Email:</strong> support@flowerfashion.com</p>
        </div>

        <div>
            <h3>✉️ Gửi tin nhắn</h3>
            <form action="contact.php" method="POST">
                <input type="hidden" name="send_msg" value="1">
                <div class="form-group">
                    <label>Họ và tên:</label>
                    <input type="text" name="name" required placeholder="Nhập tên của bạn">
                </div>
                <div class="form-group">
                    <label>SĐT hoặc Email:</label>
                    <input type="text" name="contact" required placeholder="Nhập SĐT/Email để nhận câu trả lời">
                </div>
                <div class="form-group">
                    <label>Lời nhắn:</label>
                    <textarea name="message" rows="3" required placeholder="Nội dung cần hỗ trợ..."></textarea>
                </div>
                <button type="submit" class="btn-submit">Gửi tin nhắn</button>
            </form>
        </div>
    </div>

    <!-- KHUNG TRA CỨU CÂU TRẢ LỜI CỦA SHOP -->
    <div class="search-box">
        <h3>🔍 Xem câu trả lời từ Shop</h3>
        <form action="contact.php" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search_contact" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 6px;" placeholder="Nhập Số điện thoại/Email của bạn để xem phản hồi..." value="<?php echo htmlspecialchars($_GET['search_contact'] ?? ''); ?>" required>
            <button type="submit" class="btn-submit" style="width: auto;">Tra cứu</button>
        </form>

        <?php if ($search_result): ?>
            <div style="margin-top: 15px;">
                <?php if ($search_result->num_rows > 0): ?>
                    <?php while ($item = $search_result->fetch_assoc()): ?>
                        <div class="reply-item">
                            <p style="margin: 0;"><strong>Câu hỏi:</strong> <?php echo htmlspecialchars($item['message']); ?></p>
                            <?php if (!empty($item['reply'])): ?>
                                <div class="shop-reply">
                                    <strong style="color: #0084ff;">💬 Shop trả lời:</strong>
                                    <p style="margin: 4px 0 0 0; color: #2d3436;"><?php echo nl2br(htmlspecialchars($item['reply'])); ?></p>
                                </div>
                            <?php else: ?>
                                <p style="color: #e67e22; font-size: 13px; margin: 5px 0 0 0;">⏳ Shop đang xử lý, vui lòng quay lại sau nhé!</p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #7f8c8d; margin-top: 10px;">Không tìm thấy tin nhắn nào gắn với SĐT/Email này.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" style="color: #ff4757; text-decoration: none; font-weight: bold;">⬅ Quay lại trang chủ</a>
    </div>
</div>

</body>
</html>