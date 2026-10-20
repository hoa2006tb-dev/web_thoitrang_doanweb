<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kết nối database từ thư mục admin ra ngoài root
include '../config/db.php';

// Truy vấn lấy toàn bộ sản phẩm từ CSDL
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        ORDER BY p.product_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Sản Phẩm (Admin)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h2 { color: #333; }
        .btn-add { background: #2ed573; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #333; color: white; }
        img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .btn-edit { background: #ffa500; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-size: 13px; }
        .btn-delete { background: #ff4757; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
    </style>
</head>
<body>

    <h2>🛍️ Trang Quản Lý Sản Phẩm (Admin)</h2>
    
    <!-- Nút thêm sản phẩm mới -->
    <a href="add_product.php" class="btn-add">➕ Thêm Sản Phẩm Mới</a>
    <a href="../index.php" style="margin-left: 15px; text-decoration: none; color: #555;">🌐 Xem trang chủ Store</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá (VNĐ)</th>
                <th>Size</th>
                <th>Màu sắc</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                        // Lấy ảnh đầu tiên để làm ảnh đại diện
                        $images = !empty($row['image']) ? explode(',', $row['image']) : ['default.jpg'];
                        $first_img = trim($images[0]);
                    ?>
                    <tr>
                        <td><strong>#<?php echo $row['product_id']; ?></strong></td>
                        <td>
                            <img src="../assets/images/<?php echo htmlspecialchars($first_img); ?>" alt="Ảnh">
                        </td>
                        <td style="text-align: left;"><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
                        <td style="color: #d63031; font-weight: bold;"><?php echo number_format($row['price']); ?> đ</td>
                        <td><?php echo htmlspecialchars($row['size']); ?></td>
                        <td><?php echo htmlspecialchars($row['color']); ?></td>
                        <td>
                            <!-- Nút Sửa gắn đúng ID -->
                            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn-edit">✏️ Sửa</a>
                            
                            <!-- Nút Xóa gắn đúng ID -->
                            <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm: <?php echo htmlspecialchars($row['product_name']); ?> không?');" 
                               class="btn-delete">🗑️ Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="padding: 20px;">Chưa có sản phẩm nào trong CSDL! Bấm nút "Thêm Sản Phẩm Mới" phía trên để tạo.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>