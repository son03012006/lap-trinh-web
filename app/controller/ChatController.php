<?php
class ChatController
{
    // Không cần hàm __construct để tạo kết nối mới
    // Chúng ta sẽ dùng biến $dbh có sẵn từ index.php

    public function load()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            echo json_encode([]);
            exit;
        }

        // 2. Gọi biến kết nối Database toàn cục
        global $dbh; 
        
        // Nếu $dbh chưa có (do lỗi include), dừng luôn để tránh lỗi Fatal
        if (!$dbh) {
            echo json_encode([]); 
            exit;
        }

        $uid = $_SESSION['user']['id'];

        try {
            $stmt = $dbh->prepare("
                SELECT sender, message, created_at
                FROM chats
                WHERE user_id = ?
                ORDER BY created_at ASC
            ");
            $stmt->execute([$uid]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($messages);
        } catch (Exception $e) {
            echo json_encode([]);
        }
        exit;
    }

    public function send()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
            exit;
        }

        // 2. Gọi biến kết nối Database toàn cục
        global $dbh;

        $uid = $_SESSION['user']['id'];
        $msg = trim($_POST['message'] ?? '');

        if ($msg === '') {
            echo json_encode(['success' => false, 'message' => 'Tin nhắn rỗng']);
            exit;
        }

        try {
            $stmt = $dbh->prepare("
                INSERT INTO chats (user_id, sender, message)
                VALUES (?, 'user', ?)
            ");
            $stmt->execute([$uid, $msg]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            // Ghi log lỗi nếu cần: error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi Server: ' . $e->getMessage()]);
        }
        exit;
    }
}