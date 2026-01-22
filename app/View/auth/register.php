<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký tài khoản</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- TAILWIND CDN (BẮT BUỘC) -->
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


<!-- CARD -->
<div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">

  <!-- HEADER -->
  <div class="text-center mb-6">
    <div class="text-4xl mb-2">🧋</div>
    <h2 class="text-3xl font-extrabold text-primary">Đăng ký</h2>
    <p class="text-gray-500 text-sm">Tạo tài khoản TSN MilkTea</p>
  </div>

  <!-- FORM -->
  <form method="post" action="?c=auth&a=handleRegister" class="space-y-5">

    <!-- FULLNAME -->
    <div>
      <label class="block text-sm font-semibold text-gray-600 mb-1">Họ tên</label>
      <div class="relative">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">👤</span>
        <input name="fullname" required
               placeholder="Nguyễn Văn A"
               class="w-full pl-12 pr-4 py-3 rounded-full border
                      focus:ring-2 focus:ring-primary focus:border-primary
                      outline-none">
      </div>
    </div>

    <!-- EMAIL -->
    <div>
      <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
      <div class="relative">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">📧</span>
        <input name="email" type="email" required
               placeholder="email@example.com"
               class="w-full pl-12 pr-4 py-3 rounded-full border
                      focus:ring-2 focus:ring-primary focus:border-primary
                      outline-none">
      </div>
    </div>

    <!-- PHONE -->
    <div>
      <label class="block text-sm font-semibold text-gray-600 mb-1">Số điện thoại</label>
      <div class="relative">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">📱</span>
        <input name="phone" required
               placeholder="VD: 0987654321"
               class="w-full pl-12 pr-4 py-3 rounded-full border
                      focus:ring-2 focus:ring-primary focus:border-primary
                      outline-none">
      </div>
    </div>

    <!-- PASSWORD -->
    <div>
      <label class="block text-sm font-semibold text-gray-600 mb-1">Mật khẩu</label>
      <div class="relative">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔒</span>
        <input name="password" type="password" required
               placeholder="••••••••"
               class="w-full pl-12 pr-4 py-3 rounded-full border
                      focus:ring-2 focus:ring-primary focus:border-primary
                      outline-none">
      </div>
    </div>

    <!-- ERROR -->
    <?php if (!empty($_SESSION['auth_error'])): ?>
      <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg text-center">
        <?= $_SESSION['auth_error']; unset($_SESSION['auth_error']); ?>
      </div>
    <?php endif; ?>

    <!-- SUBMIT -->
    <button
      class="w-full py-3 rounded-full font-bold text-white
             bg-gradient-to-r from-orange-400 to-orange-500
             hover:from-orange-500 hover:to-orange-600
             transition shadow-lg">
      Đăng ký 🧋
    </button>

  </form>

  <!-- FOOTER -->
  <div class="mt-6 text-center text-sm text-gray-600">
    Đã có tài khoản?
    <a href="?c=auth&a=login"
       class="text-primary font-semibold hover:underline">
      Đăng nhập
    </a>
  </div>

</div>

</body>
</html>
