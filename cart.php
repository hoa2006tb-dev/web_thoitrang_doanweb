<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web_thoi_trang_douyin");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// 1. KIỂM TRA & XÓA GIỎ HÀNG CŨ NẾU BỊ LỖI CẤU TRÚC
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $test_item) {
        if (!is_array($test_item)) {
            unset($_SESSION['cart']); // Reset giỏ hàng bị lệch kiểu dữ liệu cũ
            break;
        }
    }
}

// 2. XỬ LÝ THÊM SẢN PHẨM VÀO GIỎ HÀNG
if (isset($_GET['action']) && $_GET['action'] == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $selected_size = isset($_POST['selected_size']) ? trim($_POST['selected_size']) : 'Freesize';
    $selected_color = isset($_POST['selected_color']) ? trim($_POST['selected_color']) : 'Mặc định';

    // Tạo mã khoá duy nhất: ID_Size_Màu (VD: 5_M_Trắng)
    $cart_key = $product_id . '_' . $selected_size . '_' . $selected_color;

    // Lấy thông tin sản phẩm từ CSDL
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu sản phẩm cùng Size và Màu đã có -> Tăng số lượng
        if (isset($_SESSION['cart'][$cart_key]) && is_array($_SESSION['cart'][$cart_key])) {
            $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
        } else {
            // Thêm sản phẩm mới vào mảng giỏ hàng
            $_SESSION['cart'][$cart_key] = [
                'id' => $product['product_id'],
                'name' => $product['product_name'],
                'price' => floatval($product['price']),
                'image' => $product['image'],
                'size' => $selected_size,
                'color' => $selected_color,
                'quantity' => $quantity
            ];
        }
    }
    
    header("Location: cart.php");
    exit();
}

// 3. XỬ LÝ TĂNG / GIẢM SỐ LƯỢNG SẢN PHẨM
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['key']) && isset($_GET['type'])) {
    $key = $_GET['key'];
    $type = $_GET['type'];

    if (isset($_SESSION['cart'][$key])) {
        if ($type == 'increase') {
            $_SESSION['cart'][$key]['quantity'] += 1;
        } elseif ($type == 'decrease') {
            $_SESSION['cart'][$key]['quantity'] -= 1;
            // Nếu giảm về 0 thì tự động xóa sản phẩm khỏi giỏ hàng
            if ($_SESSION['cart'][$key]['quantity'] <= 0) {
                unset($_SESSION['cart'][$key]);
            }
        }
    }
    header("Location: cart.php");
    exit();
}

// 4. XỬ LÝ XÓA SẢN PHẨM KHỎI GIỎ HÀNG
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['key'])) {
    $key = $_GET['key'];
    if (isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
    }
    header("Location: cart.php");
    exit();
}

// 5. TÍNH TỔNG TIỀN GIỎ HÀNG
$total_cart_money = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item)) {
            $total_cart_money += $item['price'] * $item['quantity'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng thời trang Douyin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff5f5; padding: 30px 10px; margin: 0; }
        .cart-container { max-width: 900px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { color: #2d3436; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        th { background: #ff4757; color: white; padding: 12px; font-weight: bold; }
        td { padding: 12px; border-bottom: 1px solid #f1f2f6; text-align: center; vertical-align: middle; }
        
        .product-info { text-align: left; }
        .product-name { font-weight: bold; color: #2d3436; margin-bottom: 4px; display: block; }
        .product-detail-badge { font-size: 13px; color: #636e72; background: #f1f2f6; padding: 4px 8px; border-radius: 4px; display: inline-block; margin-top: 4px; }
        
        .price { color: #ff4757; font-weight: bold; }
        .total-row { font-weight: bold; font-size: 16px; background: #fafafa; }
        
        .btn-delete { background: #ff4757; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        .btn-delete:hover { background: #e84118; }
        
        /* BỘ NÚT TĂNG GIẢM SỐ LƯỢNG */
        .quantity-control { display: inline-flex; align-items: center; border: 1px solid #dfe4ea; border-radius: 4px; overflow: hidden; }
        .btn-qty { display: inline-block; width: 28px; height: 28px; line-height: 26px; text-align: center; background: #f1f2f6; color: #2d3436; text-decoration: none; font-weight: bold; font-size: 16px; user-select: none; }
        .btn-qty:hover { background: #e4e7eb; }
        .qty-number { display: inline-block; width: 35px; text-align: center; font-weight: bold; font-size: 14px; color: #2d3436; }

        .checkout-box { margin-top: 30px; border-top: 2px dashed #ffeaa7; padding-top: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #2d3436; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 14px; }
        
        .btn-submit { background: #2ed573; color: white; padding: 12px 25px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 15px; }
        .btn-submit:hover { background: #26af5f; }
        .btn-back { margin-left: 15px; color: #0084ff; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="cart-container">
    <h2>🛍️ Giỏ hàng của bạn</h2>

    <?php if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])): ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $key => $item): 
                    if (!is_array($item)) continue;
                    $item_total = $item['price'] * $item['quantity'];
                ?>
                    <tr>
                        <td class="product-info">
                            <span class="product-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            <!-- HIỂN THỊ CHI TIẾT SIZE VÀ MÀU SẮC -->
                            <span class="product-detail-badge">
                                📏 <strong>Size:</strong> <?php echo htmlspecialchars($item['size']); ?> | 
                                🎨 <strong>Màu:</strong> <?php echo htmlspecialchars($item['color']); ?>
                            </span>
                        </td>
                        <td class="price"><?php echo number_format($item['price']); ?> VNĐ</td>
                        <td>
                            <!-- BỘ CÔNG TRỪ SỐ LƯỢNG -->
                            <div class="quantity-control">
                                <a href="cart.php?action=update&type=decrease&key=<?php echo urlencode($key); ?>" class="btn-qty">-</a>
                                <span class="qty-number"><?php echo $item['quantity']; ?></span>
                                <a href="cart.php?action=update&type=increase&key=<?php echo urlencode($key); ?>" class="btn-qty">+</a>
                            </div>
                        </td>
                        <td class="price"><?php echo number_format($item_total); ?> VNĐ</td>
                        <td>
                            <a href="cart.php?action=delete&key=<?php echo urlencode($key); ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" 
                               class="btn-delete">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <tr class="total-row">
                    <td colspan="3" style="text-align: right; padding-right: 20px;"><strong>Tổng tiền:</strong></td>
                    <td colspan="2" style="color: #ff4757; font-size: 18px; text-align: left; padding-left: 10px;">
                        <strong><?php echo number_format($total_cart_money); ?> VNĐ</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- KHUNG THÔNG TIN GIAO HÀNG -->
        <div class="checkout-box">
            <h3>📝 Thông tin giao hàng</h3>
            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label>Họ và tên người nhận:</label>
                    <input type="text" name="fullname" placeholder="Ví dụ: Lê Thị Hoa" required>
                </div>

                <div class="form-group">
                    <label>Số điện thoại:</label>
                    <input type="tel" name="phone" placeholder="Ví dụ: 0987654321" required>
                </div>

                <div class="form-group">
                    <label>Địa chỉ nhận hàng:</label>
                    <textarea name="address" rows="3" placeholder="Nhập địa chỉ chi tiết..." required></textarea>
                </div>

                <button type="submit" class="btn-submit">✅ Xác nhận đặt hàng</button>
                <a href="index.php" class="btn-back">Tiếp tục xem sản phẩm</a>
            </form>
        </div>

    <?php else: ?>
        <p style="text-align: center; padding: 30px 0;"> 
            Giỏ hàng của bạn đang trống! 
        </p>
        <div style="text-align: center;">
             <a href="index.php" class="btn-back"> 
                Tiếp tục xem sản phẩm 
            </a> 
        </div> 
    <?php endif; ?> 
</div> 
</body> 
</html>