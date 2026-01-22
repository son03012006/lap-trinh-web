<?php
require_once 'app/Config/database.php';

class CartController
{
    /* ================== ADD TO CART ================== */
    public function add()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success'=>false,'message'=>'Vui lòng đăng nhập']);
            exit;
        }

        global $dbh;

        $userId    = $_SESSION['user']['id'];
        $productId = (int)($_POST['product_id'] ?? 0);
        $size      = trim($_POST['size'] ?? '');
        $note      = trim($_POST['note'] ?? '');

        if (!$productId || $size === '') {
            echo json_encode(['success'=>false,'message'=>'Thiếu sản phẩm hoặc size']);
            exit;
        }

        /* ===== LẤY GIÁ ===== */
        $stmt = $dbh->prepare("
            SELECT price FROM product_sizes
            WHERE product_id = :pid AND size = :size
            LIMIT 1
        ");
        $stmt->execute([
            ':pid'=>$productId,
            ':size'=>$size
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            echo json_encode(['success'=>false,'message'=>'Size không hợp lệ']);
            exit;
        }
        $price = (int)$row['price'];

        /* ===== CHECK CART ===== */
        $stmt = $dbh->prepare("
            SELECT id FROM carts
            WHERE user_id=:uid AND product_id=:pid AND size=:size
            LIMIT 1
        ");
        $stmt->execute([
            ':uid'=>$userId,
            ':pid'=>$productId,
            ':size'=>$size
        ]);

        if ($cart = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Nếu đã có thì cộng dồn số lượng
            $stmt = $dbh->prepare("
                UPDATE carts 
                SET qty = qty + 1,
                    note = :note
                WHERE id = :id
            ");
            $stmt->execute([
                ':id'=>$cart['id'],
                ':note'=>$note
            ]);
        } else {
            // Nếu chưa có thì thêm mới
            $stmt = $dbh->prepare("
                INSERT INTO carts(user_id,product_id,size,note,price,qty,created_at)
                VALUES(:uid,:pid,:size,:note,:price,1,NOW())
            ");
            $stmt->execute([
                ':uid'=>$userId,
                ':pid'=>$productId,
                ':size'=>$size,
                ':note'=>$note,
                ':price'=>$price
            ]);
        }

        // Đếm tổng số lượng để cập nhật icon giỏ hàng
        $count = (int)$dbh->query("
            SELECT IFNULL(SUM(qty),0) FROM carts WHERE user_id=$userId
        ")->fetchColumn();

        echo json_encode(['success'=>true,'count'=>$count]);
    }

    /* ================== UPDATE QUANTITY (MỚI THÊM) ================== */
    public function updateQty() 
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success'=>false, 'message'=>'Vui lòng đăng nhập']);
            exit;
        }

        global $dbh;
        $uid = $_SESSION['user']['id'];
        $cartId = (int)($_POST['id'] ?? 0); // ID của dòng trong bảng carts
        $delta  = (int)($_POST['delta'] ?? 0); // +1 hoặc -1

        if ($cartId <= 0 || $delta == 0) {
            echo json_encode(['success'=>false, 'message'=>'Dữ liệu không hợp lệ']);
            exit;
        }

        // 1. Lấy số lượng hiện tại của dòng cart đó
        $stmt = $dbh->prepare("SELECT qty FROM carts WHERE id=:id AND user_id=:uid");
        $stmt->execute([':id'=>$cartId, ':uid'=>$uid]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            echo json_encode(['success'=>false, 'message'=>'Không tìm thấy sản phẩm']);
            exit;
        }

        $newQty = $item['qty'] + $delta;

        // 2. Nếu giảm xuống dưới 1 thì giữ nguyên là 1 (muốn xóa phải bấm nút xóa)
        if ($newQty < 1) $newQty = 1;

        // 3. Cập nhật vào DB
        $update = $dbh->prepare("UPDATE carts SET qty = :qty WHERE id=:id");
        $result = $update->execute([':qty'=>$newQty, ':id'=>$cartId]);

        if ($result) {
            echo json_encode(['success'=>true]);
        } else {
            echo json_encode(['success'=>false, 'message'=>'Lỗi cập nhật DB']);
        }
    }

    /* ================== GET CART ================== */
    public function get()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode([]);
            exit;
        }

        global $dbh;
        $uid = $_SESSION['user']['id'];

        // Lấy c.id để làm Key định danh cho việc Xóa/Sửa
        $stmt = $dbh->prepare("
            SELECT c.id, c.size, c.note, c.price, c.qty,
                   p.name, p.image
            FROM carts c
            JOIN products p ON p.id=c.product_id
            WHERE c.user_id=:uid
            ORDER BY c.id DESC
        ");
        $stmt->execute([':uid'=>$uid]);

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /* ================== REMOVE ITEM ================== */
    public function remove()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        global $dbh;
        
        if (!isset($_SESSION['user'])) {
             echo json_encode(['success'=>false]); return;
        }
        
        $uid = $_SESSION['user']['id'];
        $id  = (int)($_POST['id'] ?? 0);

        $dbh->prepare("
            DELETE FROM carts WHERE id=:id AND user_id=:uid
        ")->execute([
            ':id'=>$id,
            ':uid'=>$uid
        ]);

        $count = (int)$dbh->query("
            SELECT IFNULL(SUM(qty),0) FROM carts WHERE user_id=$uid
        ")->fetchColumn();

        echo json_encode(['success'=>true,'count'=>$count]);
    }

    /* ================== CHECKOUT PAGE ================== */
    public function checkoutPage()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user'])) {
            header('Location: ?c=auth&a=login');
            exit;
        }

        global $dbh;
        $uid = $_SESSION['user']['id'];

        $stmt = $dbh->prepare("
            SELECT c.id AS cart_id,
                   p.name,p.image,
                   c.size,c.note,c.price,c.qty,
                   u.fullname,u.email,u.phone
            FROM carts c
            JOIN products p ON p.id=c.product_id
            JOIN users u ON u.id=c.user_id
            WHERE c.user_id=:uid
        ");
        $stmt->execute([':uid'=>$uid]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $user = $cartItems[0] ?? ['fullname'=>'','email'=>'','phone'=>''];
        $shippingFee = 15000;

        require 'app/View/milktea/checkout.php';
    }

    /* ================== CHECKOUT ACTION ================== */
    public function checkout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        try {
            global $dbh;
            
            if (!isset($_SESSION['user'])) throw new Exception('Vui lòng đăng nhập');
            
            $uid     = $_SESSION['user']['id'];
            $address = trim($_POST['address'] ?? '');

            if ($address === '') throw new Exception('Thiếu địa chỉ');

            $stmt = $dbh->prepare("
                SELECT IFNULL(SUM(qty * price),0)
                FROM carts WHERE user_id=:uid
            ");
            $stmt->execute([':uid'=>$uid]);
            $subTotal = (int)$stmt->fetchColumn();
            if ($subTotal <= 0) throw new Exception('Giỏ hàng trống');

            $shippingFee = 15000;
            $totalAmount = $subTotal + $shippingFee;

            $dbh->prepare("
                INSERT INTO orders(user_id,address,total_amount,shipping_fee,created_at)
                VALUES(:uid,:addr,:total,:ship,NOW())
            ")->execute([
                ':uid'=>$uid,
                ':addr'=>$address,
                ':total'=>$totalAmount,
                ':ship'=>$shippingFee
            ]);

            $orderId = $dbh->lastInsertId();

            $dbh->prepare("
                INSERT INTO order_items(order_id,product_id,size,note,price,qty)
                SELECT :oid,product_id,size,note,price,qty
                FROM carts WHERE user_id=:uid
            ")->execute([
                ':oid'=>$orderId,
                ':uid'=>$uid
            ]);

            $dbh->prepare("
                DELETE FROM carts WHERE user_id=:uid
            ")->execute([':uid'=>$uid]);

            echo json_encode(['success'=>true,'order_id'=>$orderId]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        }
    }
}