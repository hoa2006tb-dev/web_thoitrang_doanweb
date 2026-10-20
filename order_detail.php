<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "web_thoi_trang_douyin");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin đơn hàng
$order_sql = "SELECT * FROM orders WHERE order_id = $order_id";
$order_res = $conn->query($order_sql);
$order = $order_res->fetch_assoc();

// Lấy thông tin sản phẩm trong đơn hàng
$details_sql = "SELECT od.*, p.product_name, p.image, p.size, p.color 
                FROM order_details od 
                JOIN products p ON od.product_id = p.product_id 
                WHERE od.order_id = $order_id";
$details_res = $conn->query($details_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Đơn Hàng #<?php echo $order_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
        .box-info { background: #f1f2f6; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #ff6b81; color: white; }
    </style>
</head>
<body>

<div class="container">
    <?php if ($order): ?>
        <h2>🛒 Chi Tiết Đơn Hàng #<?php echo $order['order_id']; ?></h2>
        
        <div class="box-info">
            <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Ngày đặt:</strong> <?php echo $order['created_at']; ?></p>
        </div>

        <h3>Món Hàng Đã Đặt:</h3>
        <table>
            <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Kích thước</th>
                <th>Màu sắc</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
            <?php while ($item = $details_res->fetch_assoc()): ?>
            <tr>
                <td><img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" width="50" style="border-radius: 4px;"></td>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo htmlspecialchars($item['size'] ?? 'Freesize'); ?></td>
                <td><?php echo htmlspecialchars($item['color'] ?? 'Mặc định'); ?></td>
                <td><?php echo number_format($item['price']); ?> VNĐ</td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['price'] * $item['quantity']); ?> VNĐ</td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="6" style="text-align: right;"><strong>Tổng Tiền Đơn Hàng:</strong></td>
                <td><strong style="color: #ff4757; font-size: 16px;"><?php echo number_format($order['total_price']); ?> VNĐ</strong></td>
            </tr>
        </table>

        <br>
        <a href="orders.php" style="color: #ff4757; font-weight: bold; text-decoration: none;">← Quay lại danh sách đơn hàng</a>
    <?php else: ?>
        <p>Đơn hàng không tồn tại!</p>
        <a href="orders.php">Quay lại</a>
    <?php endif; ?>
</div>

</body>
</html>