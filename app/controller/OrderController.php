<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'app/Config/database.php';

class OrderController {

    /* ================== TRANG ĐẶT HÀNG THÀNH CÔNG ================== */
    public function success() {

        if (!isset($_SESSION['user'])) {
            header('Location: ?c=auth&a=login');
            exit;
        }

        $orderId = (int)($_GET['id'] ?? 0);
        if (!$orderId) {
            echo 'Đơn hàng không hợp lệ';
            exit;
        }

        global $dbh;
        $userId = $_SESSION['user']['id'];

        // Lấy thông tin đơn hàng
        $stmt = $dbh->prepare("
            SELECT * FROM orders 
            WHERE id = :oid AND user_id = :uid
        ");
        $stmt->execute([
            ':oid' => $orderId,
            ':uid' => $userId
        ]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo 'Không tìm thấy đơn hàng';
            exit;
        }

        // Lấy sản phẩm trong đơn
        $stmt = $dbh->prepare("
            SELECT oi.*, p.name, p.image
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = :oid
        ");
        $stmt->execute([':oid' => $orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load view
        require 'app/View/milktea/success.php';
    }
     public function history() {

        if (!isset($_SESSION['user'])) {
            header('Location: ?c=auth&a=login');
            exit;
        }

        global $dbh;
        $userId = $_SESSION['user']['id'];

        // Lấy danh sách đơn hàng của user
        $stmt = $dbh->prepare("
            SELECT *
            FROM orders
            WHERE user_id = :uid
            ORDER BY created_at DESC
        ");
        $stmt->execute([':uid' => $userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy sản phẩm theo từng đơn
        $orderItems = [];
        if ($orders) {
            $orderIds = array_column($orders, 'id');
            $in = implode(',', array_fill(0, count($orderIds), '?'));

            $stmt = $dbh->prepare("
                SELECT oi.*, p.name, p.image
                FROM order_items oi
                JOIN products p ON p.id = oi.product_id
                WHERE oi.order_id IN ($in)
            ");
            $stmt->execute($orderIds);

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
                $orderItems[$item['order_id']][] = $item;
            }
        }

        require 'app/View/milktea/order_history.php';
    }
    public function completeFake(){
  if (session_status() === PHP_SESSION_NONE) session_start();
  header('Content-Type: application/json');

  global $dbh;
  $orderId = (int)($_POST['order_id'] ?? 0);

  $dbh->prepare("
    UPDATE orders
    SET status = 'completed'
    WHERE id = :id
  ")->execute([':id'=>$orderId]);

  echo json_encode(['success'=>true]);
}

}
