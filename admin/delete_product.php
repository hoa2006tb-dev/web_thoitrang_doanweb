<?php
include '../config/db.php'; // Điều chỉnh đường dẫn file db.php cho đúng với dự án của bạn

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 1. Lấy thông tin hình ảnh của sản phẩm để xóa ảnh khỏi thư mục
    $sql_select = "SELECT image FROM products WHERE product_id = $id";
    $result = $conn->query($sql_select);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Tách các ảnh nếu sản phẩm có nhiều ảnh
        if (!empty($row['image'])) {
            $images = explode(', ', $row['image']);
            foreach ($images as $img) {
                $file_path = "../assets/images/" . trim($img);
                if (file_exists($file_path) && !empty($img)) {
                    unlink($file_path); // Xóa file ảnh khỏi ổ đĩa
                }
            }
        }

        // 2. Xóa sản phẩm khỏi cơ sở dữ liệu
        $sql_delete = "DELETE FROM products WHERE product_id = $id";
        if ($conn->query($sql_delete) === TRUE) {
            echo "<script>
                alert('Xóa sản phẩm thành công!');
                window.location = '../index.php'; // Hoặc chuyển hướng về trang danh sách sản phẩm admin
            </script>";
        } else {
            echo "Lỗi khi xóa: " . $conn->error;
        }
    } else {
        echo "<script>alert('Sản phẩm không tồn tại!'); window.location='../index.php';</script>";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>