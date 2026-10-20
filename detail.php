<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web_thoi_trang_douyin");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy id từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn lấy thông tin sản phẩm
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.product_id = $id";

$result = $conn->query($sql);
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; display: flex; gap: 20px; }
        .img-box img { max-width: 300px; border-radius: 8px; }
        .info-box { flex: 1; }
        .price { color: #ff4757; font-size: 20px; font-weight: bold; }
        .total-price { color: #e84118; font-size: 18px; font-weight: bold; margin-top: 10px; }
        .btn-back { display: inline-block; margin-top: 15px; padding: 8px 15px; background: #ff6b81; color: #fff; text-decoration: none; border-radius: 4px; }
        .btn-cart { padding: 10px 20px; background: #ff4757; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 15px; }
        .btn-cart:hover { background: #e84118; }
        .select-option { padding: 6px; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 250px; font-size: 14px; margin-top: 4px; }
    </style>
</head>
<body>

<div class="container">
    <?php if ($product): ?>
        <div class="img-box">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
        </div>

        <div class="info-box">
            <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
            <p class="price">Giá: <span id="unit-price-text"><?php echo number_format($product['price']); ?></span> VNĐ</p>
            <p><strong>Danh mục:</strong> <?php echo htmlspecialchars($product['category_name'] ?? 'Chưa phân loại'); ?></p>

            <!-- BƯỚC 1: XEM BẢNG SIZE THAM KHẢO -->
            <p style="margin-bottom: 5px;">
                <button type="button" onclick="toggleSizeGuide()" style="background: none; border: none; color: #ff4757; text-decoration: underline; cursor: pointer; font-size: 14px; padding: 0;">
                    📏 Xem bảng gợi ý chọn Size (Theo cân nặng)
                </button>
            </p>

            <div id="sizeGuideBox" style="display: none; margin: 10px 0; padding: 12px; border: 1px dashed #ff4757; border-radius: 8px; background-color: #fff5f5; max-width: 450px;">
                <h4 style="margin-top: 0; color: #d63031;">📐 Bảng Tham Khảo Kích Thước (Size Chart)</h4>
                <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 13px; background: white;">
                    <thead>
                        <tr style="background-color: #ffeaa7; color: #2d3436;">
                            <th style="padding: 6px; border: 1px solid #dfe6e9;">Size</th>
                            <th style="padding: 6px; border: 1px solid #dfe6e9;">Cân nặng</th>
                            <th style="padding: 6px; border: 1px solid #dfe6e9;">Chiều cao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;"><strong>S</strong></td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">Dưới 45 kg</td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">1m50 - 1m58</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;"><strong>M</strong></td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">45 - 52 kg</td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">1m58 - 1m65</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;"><strong>L</strong></td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">53 - 60 kg</td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">1m60 - 1m68</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;"><strong>XL</strong></td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">61 - 68 kg</td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">1m65 - 1m72</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;"><strong>Freesize</strong></td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">Dưới 62 kg</td>
                            <td style="padding: 5px; border: 1px solid #dfe6e9;">Mặc đẹp nhất &lt; 1m68</td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size: 11px; color: #636e72; margin: 6px 0 0 0; font-style: italic;">
                    * LƯU Ý: Nếu muốn mặc rộng rãi (overfit), vui lòng chọn nhích lên 1 size!
                </p>
            </div>

            <p><strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <!-- FORM MUA HÀNG -->
            <form action="cart.php?action=add" method="POST" style="margin-top: 15px; margin-bottom: 15px;">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                
                <!-- BƯỚC 2A: MENU CHỌN SIZE KÈM GỢI Ý CÂN NẶNG -->
                <p>
                    <strong>Chọn Kích thước (Size):</strong><br>
                    <select name="selected_size" class="select-option" required>
                        <?php 
                            $size_guide = [
                                'S' => 'Size S (Dưới 45kg)',
                                'M' => 'Size M (45kg - 52kg)',
                                'L' => 'Size L (53kg - 60kg)',
                                'XL' => 'Size XL (61kg - 68kg)',
                                'Freesize' => 'Freesize (Dưới 62kg)'
                            ];

                            // Tách danh sách size từ CSDL (ví dụ: "S, M, L")
                            $size_list = !empty($product['size']) ? explode(',', $product['size']) : ['Freesize'];

                            foreach ($size_list as $sz) {
                                $sz = trim($sz);
                                $label = isset($size_guide[$sz]) ? $size_guide[$sz] : $sz;
                                echo "<option value='" . htmlspecialchars($sz) . "'>$label</option>";
                            }
                        ?>
                    </select>
                </p>

                <!-- BƯỚC 2B: MENU CHỌN MÀU SẮC -->
                <p>
                    <strong>Chọn Màu sắc:</strong><br>
                    <select name="selected_color" class="select-option" required>
                        <?php 
                            // Tách danh sách màu sắc từ CSDL (ví dụ: "Trắng, Đen, Hồng")
                            $color_list = !empty($product['color']) ? explode(',', $product['color']) : ['Mặc định'];

                            foreach ($color_list as $col) {
                                $col = trim($col);
                                echo "<option value='" . htmlspecialchars($col) . "'>" . htmlspecialchars($col) . "</option>";
                            }
                        ?>
                    </select>
                </p>

                <!-- SỐ LƯỢNG MUA -->
                <p>
                    <strong>Số lượng:</strong><br>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" style="width: 70px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; margin-top: 4px;">
                </p>

                <!-- TỔNG THÀNH TIỀN -->
                <p class="total-price">
                    Thành tiền: <span id="total-price-text"><?php echo number_format($product['price']); ?></span> VNĐ
                </p>

                <button type="submit" class="btn-cart">🛒 Thêm vào giỏ hàng</button>
            </form>

            <a href="index.php" class="btn-back">Quay lại trang chủ</a>
        </div>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm này!</p>
        <a href="index.php" class="btn-back">Quay lại trang chủ</a>
    <?php endif; ?>
</div>

<!-- JS TỰ ĐỘNG TÍNH THÀNH TIỀN VÀ BẬT/TẮT BẢNG SIZE -->
<script>
    function toggleSizeGuide() {
        var box = document.getElementById("sizeGuideBox");
        if (box.style.display === "none") {
            box.style.display = "block";
        } else {
            box.style.display = "none";
        }
    }

    const unitPrice = <?php echo floatval($product['price'] ?? 0); ?>;
    const quantityInput = document.getElementById('quantity');
    const totalPriceText = document.getElementById('total-price-text');

    function updateTotal() {
        let qty = parseInt(quantityInput.value);
        if (isNaN(qty) || qty < 1) {
            qty = 1;
        }
        const total = unitPrice * qty;
        totalPriceText.innerText = total.toLocaleString('vi-VN');
    }

    quantityInput.addEventListener('input', updateTotal);
    quantityInput.addEventListener('change', updateTotal);
</script>

</body>
</html>