<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối CSDL
include 'config/db.php';

// Tính tổng số lượng sản phẩm trong giỏ hàng
$cart_count = 0;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item) && isset($item['quantity'])) {
            $cart_count += (int)$item['quantity'];
        }
    }
}


// Lấy danh sách sản phẩm
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Flower Fashion Store</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #fdf6f0; margin: 0; padding: 20px; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; max-width: 1000px; margin: 0 auto 20px auto; }
        h1 { color: #ff6b81; margin: 0; }
        .grid { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .card { background: #fff; border-radius: 12px; padding: 15px; width: 220px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        .card img { width: 100%; height: 250px; object-fit: cover; border-radius: 8px; }
        .price { color: #e84118; font-weight: bold; font-size: 1.1em; }
        .btn { display: inline-block; padding: 8px 15px; background: #ff6b81; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .btn-cart { text-decoration: none; font-size: 16px; font-weight: bold; color: #ff4757; background: #ffeaa7; padding: 10px 18px; border-radius: 20px; border: 1px solid #ffd32a; }
    </style>
</head>
<body>

    <!-- THANH TIÊU ĐỀ VÀ BIỂU TƯỢNG GIỎ HÀNG -->
    <div class="header-bar">
        <h1>✨ FLOWER FASHION STORE ✨</h1>
        <a href="cart.php" class="btn-cart">
            🛒 Giỏ hàng (<span style="color: red;"><?php echo $cart_count; ?></span>)
        </a>
    </div>

    <!-- DANH SÁCH SẢN PHẨM -->
    <div class="grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                    <p class="price"><?php echo number_format($row['price']); ?> VNĐ</p>
                    <a href="detail.php?id=<?php echo $row['product_id']; ?>" class="btn">Xem chi tiết</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Chưa có sản phẩm nào!</p>
        <?php endif; ?>
    </div>

</body>
</html>