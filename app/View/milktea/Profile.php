<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Bắt buộc đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: ?c=auth&a=login');
    exit;
}

$user = $_SESSION['user'];
$avatar = $user['avatar'] ?? 'default-avatar.png';

// Thông báo thành công
$successMessage = $_SESSION['profile_success'] ?? null;
unset($_SESSION['profile_success']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin cá nhân</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: '#f97316'
      }
    }
  }
}
</script>
</head>

<body class="min-h-screen flex items-center justify-center px-4
             bg-gradient-to-br from-orange-50 via-[#fff7ed] to-orange-100">

<div class="w-full max-w-md bg-white rounded-3xl
            shadow-[0_30px_80px_rgba(0,0,0,0.12)]
            p-8 mt-12 mb-12">

  <!-- TITLE -->
  <h2 class="text-3xl font-extrabold text-center text-orange-500 mb-6">
    Thông tin cá nhân
  </h2>

  <!-- SUCCESS MESSAGE -->
  <?php if ($successMessage): ?>
  <div class="mb-4 p-3 text-center bg-green-50 text-green-700 rounded-lg shadow">
    <?= htmlspecialchars($successMessage) ?>
  </div>
  <?php endif; ?>

  <!-- PROFILE FORM -->
  <form action="?c=user&a=updateProfile" method="post" enctype="multipart/form-data" class="space-y-4">

    <!-- AVATAR -->
    <div class="text-center mb-8">
      <div class="w-28 h-28 mx-auto rounded-full overflow-hidden
                  border-4 border-orange-400 shadow-md">
        <img id="avatarPreview"
             src="/bantrasuamain/public/assets/img/avatars/<?= htmlspecialchars($avatar) ?>"
             class="w-full h-full object-cover">
      </div>

      <label class="inline-flex items-center gap-2 cursor-pointer
                    text-sm text-primary font-semibold hover:underline mt-2">
        📷 Chọn ảnh đại diện
        <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(event)">
      </label>
    </div>

    <!-- SỐ ĐIỆN THOẠI -->
    <div>
      <label class="text-sm text-gray-500">Số điện thoại</label>
      <input name="phone"
             value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
             placeholder="Nhập số điện thoại"
             class="w-full mt-1 px-4 py-3 border rounded-full
                    focus:ring-2 focus:ring-orange-400 outline-none">
    </div>

    <!-- HỌ TÊN -->
    <div>
      <label class="text-sm text-gray-500">Họ tên</label>
      <input name="fullname"
             value="<?= htmlspecialchars($user['fullname'] ?? '') ?>"
             class="w-full mt-1 px-4 py-3 border rounded-full
                    focus:ring-2 focus:ring-orange-400 outline-none">
    </div>

    <!-- EMAIL -->
    <div>
      <label class="text-sm text-gray-500">Email</label>
      <input name="email"
             value="<?= htmlspecialchars($user['email'] ?? '') ?>"
             class="w-full mt-1 px-4 py-3 border rounded-full
                    focus:ring-2 focus:ring-orange-400 outline-none">
    </div>

    <!-- NÚT LƯU -->
    <button type="submit"
            class="w-full py-3 rounded-full font-bold text-white
                   bg-gradient-to-r from-orange-400 to-orange-500
                   hover:from-orange-500 hover:to-orange-600
                   transition shadow-lg">
      Lưu thay đổi
    </button>

    <!-- QUAY VỀ TRANG CHỦ -->
    <a href="?c=product"
       class="block w-full text-center py-3 rounded-full
              border border-gray-300 font-semibold text-gray-600
              hover:bg-gray-100 transition">
      ← Quay về trang chủ
    </a>

  </form>
</div>

<script>
function previewAvatar(event) {
  const img = document.getElementById('avatarPreview');
  img.src = URL.createObjectURL(event.target.files[0]);
}
</script>

</body>
</html>
