<?php
require_once 'app/Config/database.php';

class AdminController
{
    /* ================= LOGIN PAGE ================= */
    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require 'app/View/auth/loginadmin.php';
    }

    /* ================= HANDLE LOGIN ================= */
    public function handleLogin()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        global $dbh;

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if ($email === '' || $password === '') {
            $_SESSION['admin_error'] = 'Vui lòng nhập đầy đủ thông tin';
            header('Location: ?c=admin&a=login');
            exit;
        }

        // Lấy admin
        $stmt = $dbh->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Hash SHA256
        $hashedInput = hash('sha256', $password);

        if (!$admin || $hashedInput !== $admin['password']) {
            $_SESSION['admin_error'] = 'Email hoặc mật khẩu admin không đúng';
            header('Location: ?c=admin&a=login');
            exit;
        }
unset($_SESSION['user']);  // ❗ CỰC QUAN TRỌNG



        // Đăng nhập thành công
        $_SESSION['admin'] = [
            'id'       => $admin['id'],
            'fullname' => $admin['fullname'],
            'email'    => $admin['email'],
            'avatar'   => $admin['avatar'] ?? 'admin.png'
        ];

        header('Location: ?c=admin&a=dashboard');
        exit;
    }

    /* ================= DASHBOARD ================= */
    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin'])) {
            header('Location: ?c=admin&a=login');
            exit;
        }

        global $dbh;

        $totalOrders = $dbh->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $totalRevenue = $dbh->query("SELECT IFNULL(SUM(total_amount),0) FROM orders")->fetchColumn();
        $totalProducts = $dbh->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $totalCustomers = $dbh->query("SELECT COUNT(*) FROM users")->fetchColumn();

        $latestOrders = $dbh->query("
            SELECT o.id, u.fullname, o.total_amount, o.created_at
            FROM orders o
            JOIN users u ON u.id = o.user_id
            ORDER BY o.id DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        require 'app/View/admin/dashboard.php';
    }

    /* ================= PRODUCTS LIST ================= */
   public function products() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: ?c=admin&a=login');
        exit;
    }

    global $dbh;

    // ===== PAGINATION =====
    $limit = 10; // 1 trang 10 sản phẩm
    $page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    // Tổng sản phẩm
    $totalProducts = $dbh->query(
        "SELECT COUNT(*) FROM products"
    )->fetchColumn();

    $totalPages = ceil($totalProducts / $limit);

    // Lấy sản phẩm theo trang
    $stmt = $dbh->prepare("
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        ORDER BY p.id DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Danh mục (cho modal)
    $categories = $dbh->query(
        "SELECT * FROM categories ORDER BY name"
    )->fetchAll(PDO::FETCH_ASSOC);

    require 'app/View/admin/products.php';
}


    public function addProduct() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: ?c=admin&a=login');
        exit;
    }

    global $dbh;

    // ===== VALIDATE =====
    $name        = trim($_POST['name'] ?? '');
    $price       = (int)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $best        = isset($_POST['is_best_seller']) ? 1 : 0;

    if ($name === '' || $price <= 0 || $category_id <= 0) {
        http_response_code(400);
        exit('Dữ liệu không hợp lệ');
    }

    // ===== IMAGE =====
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            'public/assets/img/' . $image
        );
    }

    // ===== INSERT =====
    $stmt = $dbh->prepare("
        INSERT INTO products
        (category_id, name, description, price, image, stock, is_best_seller)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $category_id,
        $name,
        $description,
        $price,
        $image,
        $stock,
        $best
    ]);

    http_response_code(200);
    exit;
}


    /* ================= STORE PRODUCT ================= */
    public function storeProduct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin'])) {
            header('Location: ?c=admin&a=login');
            exit;
        }

        global $dbh;

        $name        = $_POST['name'];
        $price       = $_POST['price'];
        $category_id = $_POST['category_id'];
        $description = $_POST['description'];
        $stock       = $_POST['stock'];
        $best        = $_POST['is_best_seller'] ?? 0;

        $image = $_FILES['image']['name'];
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            'public/assets/img/' . $image
        );

        $stmt = $dbh->prepare("
            INSERT INTO products
            (category_id, name, description, price, image, stock, is_best_seller)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $category_id, $name, $description, $price, $image, $stock, $best
        ]);

        header('Location: ?c=admin&a=products');
        exit;
    }

    /* ================= DELETE PRODUCT ================= */
    public function deleteProduct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin'])) {
            header('Location: ?c=admin&a=login');
            exit;
        }

        global $dbh;
        $id = $_GET['id'];
        $dbh->prepare("DELETE FROM products WHERE id=?")->execute([$id]);

        header('Location: ?c=admin&a=products');
        exit;
    }

    /* ================= LOGOUT ================= */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['admin']);
        header('Location: ?c=admin&a=login');
        exit;
    }
    /* ================= EDIT PRODUCT FORM ================= */
public function editProduct()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: ?c=admin&a=login');
        exit;
    }

    global $dbh;
    $id = (int)($_GET['id'] ?? 0);

    // Lấy sản phẩm
    $stmt = $dbh->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: ?c=admin&a=products');
        exit;
    }

    // Lấy danh mục
    $categories = $dbh->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

    require 'app/View/admin/edit_product.php';
}

/* ================= UPDATE PRODUCT ================= */
public function updateProduct() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: ?c=admin&a=login');
        exit;
    }

    global $dbh;

    $id          = (int)($_POST['id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $price       = (int)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $best        = isset($_POST['is_best_seller']) ? 1 : 0;

    if ($id <= 0 || $name === '') {
        http_response_code(400);
        exit;
    }

    // Lấy ảnh cũ
    $oldImage = $dbh->prepare("SELECT image FROM products WHERE id=?");
    $oldImage->execute([$id]);
    $oldImage = $oldImage->fetchColumn();

    $image = $oldImage;

    // Nếu có ảnh mới
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            'public/assets/img/' . $image
        );
    }

    $stmt = $dbh->prepare("
        UPDATE products SET
            category_id = ?,
            name = ?,
            description = ?,
            price = ?,
            image = ?,
            stock = ?,
            is_best_seller = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $category_id,
        $name,
        $description,
        $price,
        $image,
        $stock,
        $best,
        $id
    ]);

    http_response_code(200);
    exit;
}
/* ================= DANH MỤC ================= */
public function categories()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: ?c=admin&a=login');
        exit;
    }

    global $dbh;

    $categories = $dbh->query("
        SELECT c.*,
               (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS total_products
        FROM categories c
        ORDER BY c.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    require 'app/View/admin/categories.php';
}

/* ================= THÊM DANH MỤC ================= */
public function addCategory()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    global $dbh;

    $name = trim($_POST['name'] ?? '');

    if ($name === '') exit;

    $stmt = $dbh->prepare("INSERT INTO categories(name) VALUES (?)");
    $stmt->execute([$name]);
}

/* ================= CẬP NHẬT DANH MỤC ================= */
public function updateCategory()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    global $dbh;

    $id   = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if (!$id || $name === '') exit;

    $stmt = $dbh->prepare("UPDATE categories SET name=? WHERE id=?");
    $stmt->execute([$name, $id]);
}

/* ================= XÓA DANH MỤC ================= */
public function deleteCategory()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    global $dbh;

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) exit;

    // ❌ Không cho xóa nếu còn sản phẩm
    $count = $dbh->prepare(
        "SELECT COUNT(*) FROM products WHERE category_id=?"
    );
    $count->execute([$id]);

    if ($count->fetchColumn() > 0) {
        $_SESSION['error'] = 'Danh mục còn sản phẩm, không thể xóa!';
        header('Location: ?c=admin&a=categories');
        exit;
    }

    $dbh->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    header('Location: ?c=admin&a=categories');
}
/* ================= QUẢN LÝ ĐƠN HÀNG ================= */
public function orders()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: ?c=admin&a=login');
        exit;
    }

    global $dbh;

    // SỬA CÂU SQL Ở ĐÂY:
    $sql = "SELECT 
                o.*, 
                u.fullname, 
                u.phone, 
                u.email,
                -- Dùng GROUP_CONCAT để lấy note từ bảng order_items và nối lại bằng dấu phẩy
                GROUP_CONCAT(oi.note SEPARATOR ', ') as ghi_chu_mon
            FROM orders o
            JOIN users u ON u.id = o.user_id
            -- Kết nối với bảng order_items để lấy dữ liệu note
            LEFT JOIN order_items oi ON oi.order_id = o.id
            -- Bắt buộc phải Group By ID đơn hàng
            GROUP BY o.id
            ORDER BY o.id DESC";

    $orders = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    require 'app/View/admin/orders.php';
}

    /* ================= CẬP NHẬT TRẠNG THÁI ================= */
    public function updateOrderStatus()
    {
        global $dbh;

        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!$id || $status === '') {
            echo 'error';
            exit;
        }

        try {
            $stmt = $dbh->prepare("UPDATE orders SET status=? WHERE id=?");
            $stmt->execute([$status, $id]);
            echo 'ok'; // Trả về 'ok' để JavaScript biết đã thành công
        } catch (Exception $e) {
            echo 'error';
        }
        exit;
    }
public function customers()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: ?c=admin&a=login');
        exit;
    }

    global $dbh;

    $stmt = $dbh->prepare("
        SELECT 
            u.id,
            u.fullname,
            u.email,
            u.phone,
            u.avatar,
            u.status,          -- 🔥 THÊM DÒNG NÀY
            u.created_at,
            COUNT(o.id) AS total_orders
        FROM users u
        LEFT JOIN orders o ON o.user_id = u.id
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");

    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require 'app/View/admin/customers.php';
}

public function toggleUserStatus()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        http_response_code(403);
        exit;
    }

    global $dbh;

    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (!$id || !in_array($status, ['active', 'blocked'])) {
        http_response_code(400);
        exit;
    }

    $stmt = $dbh->prepare(
        "UPDATE users SET status = :status WHERE id = :id"
    );
    $stmt->execute([
        ':status' => $status,
        ':id' => $id
    ]);

    echo 'OK';
}
public function stats()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin'])) {
        header('Location: ?c=admin&a=login');
        exit;
    }

    global $dbh;

    /* ================== LẤY NGÀY LỌC ================== */
    $fromDate = $_GET['from'] ?? '';
    $toDate   = $_GET['to'] ?? '';

    $dateCondition = '';
    $params = [];

    if ($fromDate !== '' && $toDate !== '') {
        // ⚠️ FIX AMBIGUOUS created_at (luôn dùng o.created_at)
        $dateCondition = " AND DATE(o.created_at) BETWEEN :from AND :to ";
        $params = [
            ':from' => $fromDate,
            ':to'   => $toDate
        ];
    }

    /* ================== 1. THỐNG KÊ TRẠNG THÁI ================== */
    $pendingOrders    = $this->countOrdersByStatus('pending',    $dateCondition, $params);
    $processingOrders = $this->countOrdersByStatus('processing', $dateCondition, $params);
    $completedOrders  = $this->countOrdersByStatus('completed',  $dateCondition, $params);
    $cancelledOrders  = $this->countOrdersByStatus('cancelled',  $dateCondition, $params);

    /* ================== 2. BIỂU ĐỒ SỐ ĐƠN THEO NGÀY ================== */
    $sqlChart = "
        SELECT DATE(o.created_at) AS order_date, COUNT(*) AS total
        FROM orders o
        WHERE 1=1 $dateCondition
        GROUP BY DATE(o.created_at)
        ORDER BY order_date
    ";
    $stmt = $dbh->prepare($sqlChart);
    $params ? $stmt->execute($params) : $stmt->execute();
    $orderByDay = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* ================== 3. TOP KHÁCH HÀNG ================== */
    $sqlTop = "
        SELECT 
            u.fullname,
            COUNT(o.id) AS total_orders,
            COALESCE(SUM(o.total_amount), 0) AS total_spent
        FROM orders o
        JOIN users u ON u.id = o.user_id
        WHERE 1=1 $dateCondition
        GROUP BY u.id, u.fullname
        ORDER BY total_spent DESC
        LIMIT 5
    ";
    $stmt = $dbh->prepare($sqlTop);
    $params ? $stmt->execute($params) : $stmt->execute();
    $topCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* ================== LOAD VIEW ================== */
    require 'app/View/admin/stats.php';
}

/* ================== HÀM PHỤ ĐẾM THEO TRẠNG THÁI ================== */
private function countOrdersByStatus(string $status, string $dateCondition, array $params): int
{
    global $dbh;

    $sql = "
        SELECT COUNT(*) 
        FROM orders o
        WHERE o.status = :status $dateCondition
    ";
    $stmt = $dbh->prepare($sql);

    $bind = array_merge([':status' => $status], $params);
    $stmt->execute($bind);

    return (int)$stmt->fetchColumn();
}
/* ================= CHAT ================= */

/* ================== TRANG GIAO DIỆN CHAT ================== */
    public function chat()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin'])) {
            header('Location: ?c=admin&a=login');
            exit;
        }

        global $dbh;

        // 1. Lấy danh sách User đã nhắn tin
        // SỬA LỖI SQL: Dùng GROUP BY và MAX(created_at) để sắp xếp người nhắn mới nhất lên đầu chuẩn xác
        $sql = "SELECT 
                    u.id, 
                    u.fullname, 
                    u.email, 
                    u.avatar,
                    MAX(c.created_at) as last_msg_time,
                    -- Đếm xem user này có bao nhiêu tin nhắn chưa đọc (sender='user' và is_read=0)
                    SUM(CASE WHEN c.sender = 'user' AND c.is_read = 0 THEN 1 ELSE 0 END) as unread_count
                FROM chats c
                JOIN users u ON u.id = c.user_id
                GROUP BY u.id, u.fullname, u.email, u.avatar
                ORDER BY last_msg_time DESC";

        $users = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // Lưu ý: KHÔNG update is_read ở đây. Chỉ update khi bấm vào từng người.

        require 'app/View/admin/chat.php';
    }

    /* ================== API: LẤY LỊCH SỬ TIN NHẮN (AJAX) ================== */
    public function getChatHistory()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Kiểm tra quyền admin
        if (!isset($_SESSION['admin'])) { echo json_encode([]); exit; }

        global $dbh;
        $userId = (int)($_GET['user_id'] ?? 0);

        if ($userId > 0) {
            // 1. Đánh dấu tất cả tin nhắn của user này là ĐÃ ĐỌC
            $updateStmt = $dbh->prepare("
                UPDATE chats 
                SET is_read = 1 
                WHERE user_id = ? AND sender = 'user'
            ");
            $updateStmt->execute([$userId]);

            // 2. Lấy nội dung tin nhắn
            $stmt = $dbh->prepare("
                SELECT sender, message, created_at 
                FROM chats 
                WHERE user_id = ? 
                ORDER BY created_at ASC
            ");
            $stmt->execute([$userId]);
            
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } else {
            echo json_encode([]);
        }
        exit;
    }

    /* ================== API: ADMIN GỬI TIN TRẢ LỜI (AJAX) ================== */
    public function sendAdminReply()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin'])) { echo json_encode(['success'=>false]); exit; }

        global $dbh;
        
        $userId  = (int)($_POST['user_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        if ($userId && $message !== '') {
            try {
                $stmt = $dbh->prepare("
                    INSERT INTO chats (user_id, sender, message, created_at, is_read)
                    VALUES (?, 'admin', ?, NOW(), 0)
                ");
                $stmt->execute([$userId, $message]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu rỗng']);
        }
        exit;
    }







}
