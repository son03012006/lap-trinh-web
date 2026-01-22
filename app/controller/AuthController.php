<?php
require_once 'app/Model/UserModel.php';

class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        global $dbh;
        $this->userModel = new UserModel($dbh);
    }

    /* ================= VIEW ================= */

    public function login()
    {
        require_once 'app/View/auth/login.php';
    }

    public function register()
    {
        require_once 'app/View/auth/register.php';
    }

    /* ================= HANDLE REGISTER ================= */

    public function handleRegister()
    {
        global $dbh;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?c=auth&a=register');
            exit;
        }

        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate
        if ($fullname === '' || $email === '' || $phone === '' || $password === '') {
            $_SESSION['auth_error'] = 'Vui lòng nhập đầy đủ thông tin';
            header('Location: ?c=auth&a=register');
            exit;
        }

        // Check email tồn tại
        $check = $dbh->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);

        if ($check->fetch()) {
            $_SESSION['auth_error'] = 'Email đã tồn tại';
            header('Location: ?c=auth&a=register');
            exit;
        }

        // Hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user (status mặc định = active)
        $stmt = $dbh->prepare("
            INSERT INTO users (fullname, email, phone, password, status)
            VALUES (:fullname, :email, :phone, :password, 'active')
        ");

        $stmt->execute([
            ':fullname' => $fullname,
            ':email'    => $email,
            ':phone'    => $phone,
            ':password' => $hash
        ]);

        $_SESSION['auth_success'] = 'Đăng ký thành công! Vui lòng đăng nhập';
        header('Location: ?c=auth&a=login');
        exit;
    }

    /* ================= HANDLE LOGIN ================= */

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?c=auth&a=login');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['auth_error'] = 'Vui lòng nhập email và mật khẩu';
            header('Location: ?c=auth&a=login');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        // Sai tài khoản
        if (!$user || !password_verify($password, $user->password)) {
            $_SESSION['auth_error'] = 'Email hoặc mật khẩu không đúng';
            header('Location: ?c=auth&a=login');
            exit;
        }

        // 🚫 TÀI KHOẢN BỊ KHÓA
        if (isset($user->status) && $user->status === 'blocked') {
            $_SESSION['auth_error'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.';
            header('Location: ?c=auth&a=login');
            exit;
        }

        // ❗ TRÁNH XUNG ĐỘT ADMIN / USER
        unset($_SESSION['admin']);

        // Lưu session user
        $_SESSION['user'] = [
            'id'       => $user->id,
            'fullname' => $user->fullname,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'avatar'   => $user->avatar ?? null,
            'address'  => $user->address ?? null
        ];

        header('Location: ?c=product');
        exit;
    }

    /* ================= LOGOUT ================= */

    public function logout()
    {
        session_destroy();
        header('Location: ?c=product');
        exit;
    }
}
