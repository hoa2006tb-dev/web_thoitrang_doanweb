<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL từ thư mục admin ra ngoài root
include '../config/db.php'; 

// 1. Lấy ID sản phẩm cần sửa từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM products WHERE product_id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location='index.php';</script>";
    exit();
}

$product = $result->fetch_assoc();

// Tách các Size cũ đã lưu trong CSDL thành mảng để tự động tích chọn Checkbox
$current_sizes = !empty($product['size']) ? array_map('trim', explode(',', $product['size'])) : [];

// 2. Xử lý khi Admin bấm "💾 Lưu Thay Đổi"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['product_name']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    
    // Gộp danh sách Size chọn thành chuỗi "S, M, L"
    if (isset($_POST['size']) && is_array($_POST['size'])) {
        $size = $conn->real_escape_string(implode(', ', $_POST['size']));
    } else {
        $size = 'Freesize';
    }

    $color = $conn->real_escape_string($_POST['color']);
    $description = $conn->real_escape_string($_POST['description']);
    
    // Mặc định giữ lại ảnh cũ
    $images_string = $product['image']; 

    // Nếu Admin chọn thêm/thay ảnh mới
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $target_dir = "../assets/images/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $uploaded_images = [];
        $total_files = count($_FILES['images']['name']);
        
        for ($i = 0; $i < $total_files; $i++) {
            $image_name = $_FILES['images']['name'][$i];
            $tmp_name = $_FILES['images']['tmp_name'][$i];
            
            if (!empty($image_name)) {
                $new_image_name = time() . '_' . basename($image_name);
                if (move_uploaded_file($tmp_name, $target_dir . $new_image_name)) {
                    $uploaded_images[] = $new_image_name;
                }
            }
        }

        if (!empty($uploaded_images)) {
            $images_string = $conn->real_escape_string(implode(', ', $uploaded_images));
        }
    }

    // Câu lệnh Cập nhật vào CSDL
    $sql_update = "UPDATE products SET 
                    product_name = '$name',
                    price = '$price',
                    category_id = '$category_id',
                    size = '$size',
                    color = '$color',
                    description = '$description',
                    image = '$images_string'
                   WHERE product_id = $id";

    if ($conn->query($sql_update) === TRUE) {
        // Chuyển hướng về trang danh sách admin chuẩn
        echo "<script>alert('Cập nhật thành công!'); window.location='index.php';</script>";
        exit();
    } else {
        echo "<h3 style='color:red;'>Lỗi CSDL: " . $conn->error . "</h3>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh Sửa Sản Phẩm (Admin)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; }
        input[type="text"], input[type="number"], textarea, select { width: 350px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-save { background: #2ed573; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-cancel { margin-left: 10px; color: #555; text-decoration: none; }
        .img-preview { display: flex; gap: 10px; margin: 10px 0; }
        .img-preview img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <h2>✏️ Chỉnh Sửa Sản Phẩm (Admin)</h2>

    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Tên sản phẩm:</label><br>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Giá bán (VNĐ):</label><br>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="form-group">
            <label>Danh mục sản phẩm:</label><br>
            <select name="category_id" required>
                <?php
                $cats = $conn->query("SELECT * FROM categories");
                if ($cats && $cats->num_rows > 0) {
                    while ($cat = $cats->fetch_assoc()) {
                        $selected = ($cat['category_id'] == $product['category_id']) ? 'selected' : '';
                        echo "<option value='" . $cat['category_id'] . "' $selected>" . htmlspecialchars($cat['category_name']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <!-- TỰ ĐỘNG TÍCH BẢNG SIZE CŨ -->
        <div class="form-group">
            <label>Kích thước (Size) - Chọn nhiều:</label><br>
            <?php 
            $all_sizes = ['S', 'M', 'L', 'XL', 'Freesize'];
            foreach ($all_sizes as $s): 
                $checked = in_array($s, $current_sizes) ? 'checked' : '';
            ?>
                <label style="font-weight: normal; margin-right: 10px;">
                    <input type="checkbox" name="size[]" value="<?php echo $s; ?>" <?php echo $checked; ?>> <?php echo $s; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- TỰ ĐỘNG ĐIỀN MÀU CŨ -->
        <div class="form-group">
            <label>Màu sắc (Color):</label><br>
            <input type="text" name="color" value="<?php echo htmlspecialchars($product['color']); ?>" placeholder="VD: Trắng, Đen, Xanh">
        </div>

        <div class="form-group">
            <label>Mô tả sản phẩm:</label><br>
            <textarea name="description" rows="4" style="width: 350px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Ảnh hiện tại:</label>
            <div class="img-preview">
                <?php 
                $old_imgs = !empty($product['image']) ? explode(',', $product['image']) : [];
                foreach ($old_imgs as $img): 
                    if (!empty(trim($img))):
                ?>
                    <img src="../assets/images/<?php echo trim($img); ?>">
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
            <label>Đổi/Thêm ảnh mới (Để trống nếu muốn giữ nguyên ảnh cũ):</label><br>
            <input type="file" name="images[]" multiple accept="image/*">
        </div>

        <br>
        <button type="submit" class="btn-save">💾 Lưu Thay Đổi</button>
        <a href="index.php" class="btn-cancel">Hủy bỏ</a>

    </form>

</body>
</html>