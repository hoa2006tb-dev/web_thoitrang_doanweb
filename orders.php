<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "web_thoi_trang_douyin");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh sách đơn hàng
$sql = "SELECT * FROM orders ORDER BY order_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Đơn Hàng - Web Thời Trang Douyin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff5f5; padding: 20px; }
        .container { max-width: 950px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #ff6b81; color: white; }
        .btn-view { background: #70a1ff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <h2>📋 Danh Sách Đơn Hàng Đã Đặt</h2>
    <a href="index.php" style="color: #ff4757; font-weight: bold;">← Quay lại Trang Chủ</a>

    <table>
        <tr>
            <th>Mã Đơn</th>
            <th>Tên Khách Hàng</th>
            <th>Số Điện Thoại</th>
            <th>Tổng Tiền</th>
            <th>Ngày Đặt</th>
            <th>Chi Tiết</th>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['order_id']; ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><strong style="color: #ff4757;"><?php echo number_format($row['total_price']); ?> VNĐ</strong></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <a href="order_detail.php?id=<?php echo $row['order_id']; ?>" class="btn-view">🔍 Xem Chi Tiết</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Chưa có đơn hàng nào được đặt.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>