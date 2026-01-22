<?php
require_once 'app/Model/ProductModel.php';

class ProductController
{
    public function index()
    {
        global $dbh;
        $model = new ProductModel($dbh);

        // =====================
        // 1. CẤU HÌNH PHÂN TRANG
        // =====================
        $keyword = $_GET['keyword'] ?? '';
        $cat     = strtolower(trim($_GET['cat'] ?? 'all'));
        
        // Lấy số trang hiện tại, mặc định là 1
        $page    = max(1, (int)($_GET['page'] ?? 1)); 

        // 🔥 SỬA Ở ĐÂY: Đổi limit từ 10 thành 9
        $limit = 9; 
        
        // Tính vị trí bắt đầu lấy dữ liệu (Offset)
        $start = ($page - 1) * $limit;

        // =====================
        // 2. MAP CATEGORY & FILTERS
        // =====================
        $categoryMap = [
            'trasua'  => 1,
            'traicay' => 2,
            'daxay'   => 3,
            'caphe'   => 4
        ];

        $filters = [
            'keyword'  => $keyword,
            'category' => ''
        ];

        if ($cat !== 'all' && isset($categoryMap[$cat])) {
            $filters['category'] = $categoryMap[$cat];
        }

        // =====================
        // 3. TÍNH TỔNG SỐ TRANG (Logic đếm)
        // =====================
        $sqlCount = "SELECT COUNT(*) FROM products WHERE 1=1";
        $paramsCount = [];

        // Lọc theo Category
        if (!empty($filters['category'])) {
            $sqlCount .= " AND category_id = :cat";
            $paramsCount[':cat'] = $filters['category'];
        }

        // Lọc theo Keyword
        if (!empty($filters['keyword'])) {
            $sqlCount .= " AND name LIKE :kw";
            $paramsCount[':kw'] = '%' . $filters['keyword'] . '%';
        }

        $stmtCount = $dbh->prepare($sqlCount);
        $stmtCount->execute($paramsCount);
        $totalProducts = $stmtCount->fetchColumn(); // Tổng số lượng sản phẩm

        // Tính tổng số trang (chia cho 9 và làm tròn lên)
        $totalPages = ceil($totalProducts / $limit);
        $currentPage = $page; 

        // =====================
        // 4. LẤY DỮ LIỆU SẢN PHẨM (Limit 9)
        // =====================
        $products    = $model->getProducts($filters, $start, $limit);
        $bestSellers = $model->getBestSellers();
        $categories  = $model->getCategories();

        // =====================
        // 5. VIEW ROUTING
        // =====================
        switch ($cat) {
            case 'trasua':
                require 'app/View/milktea/trasua.php';
                break;

            case 'traicay':
                require 'app/View/milktea/tratraicay.php';
                break;

            case 'daxay':
                require 'app/View/milktea/daxay.php';
                break;

            case 'caphe':
                require 'app/View/milktea/caphe.php';
                break;

            case 'all':
            default:
                require 'app/View/milktea/products.php';
                break;
        }
    }

    /* ================= SIZE (GIỮ NGUYÊN) ================= */
    public function getSizes()
    {
        global $dbh;
        $model = new ProductModel($dbh);

        $productId = (int)($_GET['id'] ?? 0);
        header('Content-Type: application/json');

        if (!$productId) {
            echo json_encode([]);
            exit;
        }

        $sizes = $model->getSizesByProductId($productId);
        echo json_encode($sizes);
        exit;
    }

    /* ================= CHI TIẾT SẢN PHẨM (GIỮ NGUYÊN) ================= */
    public function getDetail()
    {
        global $dbh;
        header('Content-Type: application/json');

        $productId = (int)($_GET['id'] ?? 0);
        if (!$productId) {
            echo json_encode(['error' => 'Invalid product id']);
            exit;
        }

        $stmt = $dbh->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['error' => 'Product not found']);
            exit;
        }

        $stmt = $dbh->prepare("
            SELECT size, price
            FROM product_sizes
            WHERE product_id = :id
            ORDER BY price ASC
        ");
        $stmt->execute([':id' => $productId]);
        $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'product' => $product,
            'sizes'   => $sizes
        ]);
        exit;
    }
}