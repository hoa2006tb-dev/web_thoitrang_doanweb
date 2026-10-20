<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web_thoi_trang_douyin");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$order_info = null;
$order_items = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['cart'])) {
    // Lấy thông tin người nhận (Đã sửa lại name="fullname" theo đúng file cart.php)
    $name = isset($_POST['fullname']) ? mysqli_real_escape_string($conn, $_POST['fullname']) : '';
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';
    $address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : '';

    // Tính tổng tiền dựa trên thông tin sẵn có trong $_SESSION['cart']
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item)) {
            $total += $item['price'] * $item['quantity'];
        }
    }

    // 1. Lưu thông tin vào bảng orders
    $sql_order = "INSERT INTO orders (customer_name, phone, address, total_price) VALUES ('$name', '$phone', '$address', '$total')";
    
    if ($conn->query($sql_order)) {
        $order_id = $conn->insert_id;

        // 2. Lưu từng sản phẩm vào bảng order_details (bao gồm cả size và màu nếu CSDL có cột)
        foreach ($_SESSION['cart'] as $item) {
            if (!is_array($item)) continue;
            
            $product_id = intval($item['id']);
            $quantity = intval($item['quantity']);
            $price = floatval($item['price']);
            $size = mysqli_real_escape_string($conn, $item['size']);
            $color = mysqli_real_escape_string($conn, $item['color']);

            // Lưu chi tiết sản phẩm
            $sql_detail = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                           VALUES ('$order_id', '$product_id', '$quantity', '$price')";
            $conn->query($sql_detail);
        }

        // Lưu dữ liệu để hiển thị chi tiết hóa đơn
        $order_info = [
            'id' => $order_id,
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
            'total' => $total,
            'date' => date('d/m/Y H:i')
        ];
        $order_items = $_SESSION['cart'];

        // 3. Xóa giỏ hàng sau khi đặt thành công
        unset($_SESSION['cart']);
    } else {
        die("Lỗi xử lý đơn hàng: " . $conn->error);
    }
} elseif (!isset($order_info)) {
    // Nếu truy cập trực tiếp mà không bấm Đặt hàng -> Quay về trang chủ
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công - Thời trang Douyin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff5f5; padding: 40px 10px; margin: 0; }
        .order-container { max-width: 750px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .success-header { text-align: center; border-bottom: 2px dashed #f1f2f6; padding-bottom: 20px; margin-bottom: 20px; }
        .success-icon { font-size: 50px; color: #2ed573; margin-bottom: 10px; }
        .success-title { color: #2d3436; margin: 0; font-size: 24px; }
        .order-id { color: #ff4757; font-weight: bold; }
        
        .info-box { background: #fafafa; border: 1px solid #f1f2f6; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; }
        .info-box p { margin: 8px 0; color: #2d3436; font-size: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #ff4757; color: white; padding: 10px; text-align: left; }
        td { padding: 12px 10px; border-bottom: 1px solid #f1f2f6; vertical-align: middle; }
        
        .product-badge { font-size: 12px; color: #636e72; background: #eef2f5; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-top: 4px; }
        .price { color: #ff4757; font-weight: bold; }
        .total-row { font-size: 16px; font-weight: bold; background: #fff5f5; }
        
        .actions { text-align: center; margin-top: 30px; }
        .btn-home { background: #ff4757; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; }
        .btn-home:hover { background: #e84118; }
    </style>
</head>
<body>

<div class="order-container">
    <div class="success-header">
        <div class="success-icon">🎉</div>
        <h2 class="success-title">Đặt hàng thành công!</h2>
        <p>Cảm ơn bạn đã ủng hộ FLOWER FASHION STORE. Món đồ của bạn đang được chuẩn bị!</p>
    </div>

    <!-- THÔNG TIN ĐƠN HÀNG -->
    <div class="info-box">
        <h3>📋 Thông tin giao hàng</h3>
        <p><strong>Mã đơn hàng:</strong> <span class="order-id">#<?php echo $order_info['id']; ?></span></p>
        <p><strong>Thời gian đặt:</strong> <?php echo $order_info['date']; ?></p>
        <p><strong>Họ và tên người nhận:</strong> <?php echo htmlspecialchars($order_info['name']); ?></p>
        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order_info['phone']); ?></p>
        <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order_info['address']); ?></p>
    </div>

    <!-- CHI TIẾT SẢN PHẨM ĐÃ ĐẶT -->
    <h3>🛍️ Chi tiết sản phẩm đã đặt</h3>
    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th style="text-align: center;">Đơn giá</th>
                <th style="text-align: center;">Số lượng</th>
                <th style="text-align: right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): 
                if (!is_array($item)) continue;
                $subtotal = $item['price'] * $item['quantity'];
            ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                        <span class="product-badge">Size: <?php echo htmlspecialchars($item['size']); ?> | Màu: <?php echo htmlspecialchars($item['color']); ?></span>
                    </td>
                    <td style="text-align: center;" class="price"><?php echo number_format($item['price']); ?> VNĐ</td>
                    <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                    <td style="text-align: right;" class="price"><?php echo number_format($subtotal); ?> VNĐ</td>
                </tr>
            <?php endforeach; ?>
            
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding: 15px;">Tổng cộng:</td>
                <td style="text-align: right;" class="price"><?php echo number_format($order_info['total']); ?> VNĐ</td>
            </tr>
        </tbody>
    </table>

    <div class="actions">
        <a href="index.php" class="btn-home">Quay lại trang chủ</a>
    </div>
</div>

</body>
</html>