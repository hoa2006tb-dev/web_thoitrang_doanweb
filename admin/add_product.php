<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    
   
    if (isset($_POST['size']) && is_array($_POST['size'])) {
        $size = implode(', ', $_POST['size']);
    } else {
        $size = 'Freesize';
    }

    $color = $_POST['color']; // Nhập nhiều màu phân cách bằng dấu phẩy, VD: "Trắng, Đen, Hồng"
    $description = $_POST['description'];
    
    // Xử lý upload ảnh
    $image = $_FILES['image']['name'];
    $target_dir = "../assets/images/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);

    // Thêm size và color vào câu lệnh INSERT
    $sql = "INSERT INTO products (product_name, price, category_id, size, color, description, image) 
            VALUES ('$name', '$price', '$category_id', '$size', '$color', '$description', '$image')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thêm sản phẩm thành công!'); window.location='../index.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm mới</title>
</head>
<body>
    <h2>Thêm Sản Phẩm Mới (Admin)</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Tên sản phẩm:</label><br>
        <input type="text" name="product_name" required><br><br>

        <label>Giá (VNĐ):</label><br>
        <input type="number" name="price" required><br><br>
        
        <label>Danh mục sản phẩm:</label><br>
        <select name="category_id" required>
            <?php
            $cats = $conn->query("SELECT * FROM categories");
            if ($cats && $cats->num_rows > 0) {
                while ($cat = $cats->fetch_assoc()) {
                    echo "<option value='" . $cat['category_id'] . "'>" . htmlspecialchars($cat['category_name']) . "</option>";
                }
            } else {
                echo "<option value=''>Chưa có danh mục nào</option>";
            }
            ?>
        </select><br><br>

     
        <label><strong>Kích thước (Size) - Chọn nhiều:</strong></label><br>
        <label><input type="checkbox" name="size[]" value="S"> S</label>
        <label><input type="checkbox" name="size[]" value="M"> M</label>
        <label><input type="checkbox" name="size[]" value="L"> L</label>
        <label><input type="checkbox" name="size[]" value="XL"> XL</label>
        <label><input type="checkbox" name="size[]" value="Freesize" checked> Freesize</label>
        <br><br>

       
        <label><strong>Màu sắc (Color):</strong></label><br>
        <input type="text" name="color" placeholder="Nhập các màu, cách nhau bởi dấu phẩy (VD: Trắng, Đen, Xanh)" style="width: 350px;"><br><br>
        
        <label>Mô tả:</label><br>
        <textarea name="description"></textarea><br><br>

        <label>Hình ảnh:</label><br>
        <input type="file" name="image" required><br><br>

        <button type="submit">Thêm Sản Phẩm</button>
    </form>
</body>
</html>