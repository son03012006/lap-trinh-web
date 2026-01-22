<?php

class UserController
{
    public function profile()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ?c=auth&a=login');
            exit;
        }

        require_once 'app/View/milktea/Profile.php';
    }

    
    public function updateProfile() {
    global $dbh;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user']['id'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $avatar = $_SESSION['user']['avatar'] ?? 'default-avatar.png';

        // Nếu có file avatar mới
        if (!empty($_FILES['avatar']['name'])) {
            $filename = uniqid() . '_' . $_FILES['avatar']['name'];
            move_uploaded_file($_FILES['avatar']['tmp_name'],
                               __DIR__ . '/../../public/assets/img/avatars/' . $filename);
            $avatar = $filename;
        }

        $sql = "UPDATE users SET fullname=:fullname, email=:email, phone=:phone, avatar=:avatar WHERE id=:id";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':fullname'=>$fullname,
            ':email'=>$email,
            ':phone'=>$phone,
            ':avatar'=>$avatar,
            ':id'=>$userId
        ]);

        // **Cập nhật luôn session**
        $_SESSION['user']['fullname'] = $fullname;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['phone'] = $phone;
        $_SESSION['user']['avatar'] = $avatar;

        // Thông báo thành công
        $_SESSION['profile_success'] = "Lưu thông tin thành công!";

        // Reload lại profile
        header('Location: ?c=user&a=profile');
        exit;
    }
}

public function updateAvatar() {
    session_start();
    global $dbh;

    $userId = $_SESSION['user']['id'];

    if (!isset($_FILES['avatar'])) {
        header('Location: ?c=user&a=profile');
        exit;
    }

    $file = $_FILES['avatar'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg','jpeg','png','gif'];

    if (!in_array(strtolower($ext), $allowed)) {
        $_SESSION['avatar_error'] = "Chỉ chấp nhận ảnh JPG, PNG, GIF";
        header('Location: ?c=user&a=profile');
        exit;
    }

    $folder = __DIR__ . '/../../public/assets/img/avatars/';

    // Xóa avatar cũ (nếu không phải default)
    $oldAvatar = $_SESSION['user']['avatar'] ?? '';
    if ($oldAvatar && $oldAvatar != 'default-avatar.png' && file_exists($folder.$oldAvatar)) {
        unlink($folder.$oldAvatar);
    }

    // Lưu file mới với tên dạng: avatar_userID.ext
    $newName = "avatar_{$userId}.".$ext;
    move_uploaded_file($file['tmp_name'], $folder.$newName);

    // Cập nhật DB
    $sql = "UPDATE users SET avatar = :avatar WHERE id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':avatar', $newName);
    $stmt->bindValue(':id', $userId);
    $stmt->execute();

    // Update session luôn
    $_SESSION['user']['avatar'] = $newName;

    header('Location: ?c=user&a=profile');
    exit;
}



}
