<?php
session_start();

if (file_exists('../config/db.php')) {
    include '../config/db.php';
} elseif (file_exists('../db.php')) {
    include '../db.php';
} else {
    $conn = new mysqli("localhost", "root", "", "web_thoi_trang_douyin");
}

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý gửi phản hồi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reply') {
    $contact_id = intval($_POST['contact_id']);
    $reply = mysqli_real_escape_string($conn, $_POST['reply_content']);
    
    $conn->query("UPDATE contacts SET reply = '$reply' WHERE contact_id = $contact_id");
    header("Location: contacts.php");
    exit();
}

// Xử lý xóa tin nhắn
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $contact_id = intval($_GET['id']);
    $conn->query("DELETE FROM contacts WHERE contact_id = $contact_id");
    header("Location: contacts.php");
    exit();
}

$result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tin nhắn & Trả lời khách hàng - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .admin-container { max-width: 1100px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #2d3436; margin-top: 0; display: flex; align-items: center; justify-content: space-between; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #2d3436; color: white; padding: 12px; text-align: left; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #dfe4ea; font-size: 14px; vertical-align: top; }
        tr:hover { background: #f8f9fa; }
        
        .badge-time { font-size: 12px; color: #636e72; display: block; margin-top: 4px; }
        .reply-box { margin-top: 10px; background: #eef5ff; padding: 10px; border-radius: 6px; border-left: 3px solid #0084ff; }
        .reply-box textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; font-size: 13px; margin-bottom: 6px; }
        
        .btn-reply { background: #0084ff; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px; }
        .btn-reply:hover { background: #006bce; }
        .btn-delete { background: #ff4757; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; display: inline-block; }
        .btn-delete:hover { background: #e84118; }
        .btn-back { background: #70a1ff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 14px; }
        
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .status-done { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>

<div class="admin-container">
    <h2>
        <span>📩 Quản lý & Trả lời tin nhắn</span>
        <a href="index.php" class="btn-back">⬅ Quay lại Admin</a>
    </h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">STT</th>
                    <th style="width: 18%;">Khách hàng</th>
                    <th style="width: 15%;">SĐT / Email</th>
                    <th style="width: 47%;">Nội dung & Phản hồi</th>
                    <th style="width: 15%;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $stt = 1;
                while ($row = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><strong><?php echo $stt++; ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                            <span class="badge-time">🕒 <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                        <td>
                            <div><strong>Lời nhắn:</strong> <?php echo nl2br(htmlspecialchars($row['message'])); ?></div>
                            
                            <!-- HIỂN THỊ PHẢN HỒI HOẶC KHUNG NHẬP TRẢ LỜI -->
                            <div class="reply-box">
                                <?php if (!empty($row['reply'])): ?>
                                    <span class="status-badge status-done">✓ Đã trả lời:</span>
                                    <p style="margin: 5px 0 0 0; color: #1e3799;"><?php echo nl2br(htmlspecialchars($row['reply'])); ?></p>
                                <?php else: ?>
                                    <span class="status-badge status-pending">⏳ Chưa phản hồi</span>
                                <?php endif; ?>

                                <!-- FORM NHẬP CÂU TRẢ LỜI -->
                                <form action="contacts.php" method="POST" style="margin-top: 8px;">
                                    <input type="hidden" name="action" value="reply">
                                    <input type="hidden" name="contact_id" value="<?php echo $row['contact_id']; ?>">
                                    <textarea name="reply_content" rows="2" placeholder="Nhập câu trả lời cho khách..."><?php echo htmlspecialchars($row['reply'] ?? ''); ?></textarea>
                                    <button type="submit" class="btn-reply">💬 <?php echo !empty($row['reply']) ? 'Cập nhật phản hồi' : 'Gửi phản hồi'; ?></button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <a href="contacts.php?action=delete&id=<?php echo $row['contact_id']; ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa tin nhắn này?');" 
                               class="btn-delete">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; padding: 30px; color: #636e72;">Chưa có tin nhắn liên hệ nào từ khách hàng!</p>
    <?php endif; ?>
</div>

</body>
</html>