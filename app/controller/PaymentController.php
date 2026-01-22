<?php
class PaymentController {

  public function fakeBank(){
    if (session_status() === PHP_SESSION_NONE) session_start();

    $orderId = (int)($_GET['order'] ?? 0);
    if(!$orderId){
      echo 'Đơn hàng không hợp lệ';
      exit;
    }

    global $dbh;

    $stmt = $dbh->prepare("
      SELECT * FROM orders WHERE id = :id
    ");
    $stmt->execute([':id'=>$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$order){
      echo 'Không tìm thấy đơn hàng';
      exit;
    }

    require 'app/View/payment/fake_bank.php';
  }

}
