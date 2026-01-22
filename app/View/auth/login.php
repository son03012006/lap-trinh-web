<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập | TSN MilkTea</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- TAILWIND CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: '#f97316',   // cam trà sữa
              cream: '#FFF6ED',
              admin: '#1f2937'      // màu admin
            }
          }
        }
      }
    </script>
</head>

<body class="bg-cream min-h-screen flex items-center justify-center px-4">

  <main class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">

    <!-- LOGO / TITLE -->
    <div class="text-center mb-8">
      <div class="w-20 h-20 mx-auto rounded-full bg-orange-100 flex items-center justify-center text-4xl mb-3">
        🧋
      </div>
      <h2 class="text-3xl font-extrabold text-primary">
        TSN MilkTea
      </h2>
      <p class="text-gray-500 mt-1">
        Đăng nhập để tiếp tục mua sắm
      </p>
    </div>

    <!-- ================= LOGIN USER ================= -->
    <form method="post" action="?c=auth&a=handleLogin" class="space-y-5">

      <!-- EMAIL -->
      <div>
        <label class="block text-sm font-semibold text-gray-600 mb-1">
          Email
        </label>
        <div class="relative">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
            📧
          </span>
          <input
            name="email"
            type="email"
            required
            placeholder="example@gmail.com"
            class="w-full pl-12 pr-4 py-3 rounded-full border
                   focus:ring-2 focus:ring-primary focus:border-primary outline-none"
          >
        </div>
      </div>

      <!-- PASSWORD -->
      <div>
        <label class="block text-sm font-semibold text-gray-600 mb-1">
          Mật khẩu
        </label>
        <div class="relative">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
            🔒
          </span>
          <input
            name="password"
            type="password"
            required
            placeholder="••••••••"
            class="w-full pl-12 pr-4 py-3 rounded-full border
                   focus:ring-2 focus:ring-primary focus:border-primary outline-none"
          >
        </div>
      </div>

      <!-- ERROR -->
      <?php if (!empty($_SESSION['auth_error'])): ?>
        <div class="bg-red-50 text-red-600 text-sm p-3 rounded-xl text-center font-semibold">
          <?= $_SESSION['auth_error']; unset($_SESSION['auth_error']); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['auth_success'])): ?>
        <div class="bg-green-50 text-green-600 text-sm p-3 rounded-xl text-center font-semibold">
          <?= $_SESSION['auth_success']; unset($_SESSION['auth_success']); ?>
        </div>
      <?php endif; ?>

      <!-- BUTTON -->
      <button
        type="submit"
        class="w-full py-3 rounded-full font-bold text-lg text-white
               bg-gradient-to-r from-orange-400 to-orange-500
               hover:from-orange-500 hover:to-orange-600
               transition shadow-lg"
      >
        Đăng nhập 🧋
      </button>

    </form>

    <!-- REGISTER -->
    <div class="mt-6 text-center text-sm text-gray-600">
      Chưa có tài khoản?
      <a href="?c=auth&a=register"
         class="text-primary font-semibold hover:underline">
        Đăng ký ngay
      </a>
    </div>

    <!-- ================= DIVIDER ================= -->
    <div class="my-8 flex items-center gap-3">
      <div class="flex-1 h-px bg-gray-200"></div>
      <span class="text-sm text-gray-400">hoặc</span>
      <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <!-- ================= LOGIN ADMIN ================= -->
    <div class="text-center">
      <p class="text-sm text-gray-600 mb-3">
        Dành cho quản trị viên
      </p>

      <a href="?c=admin&a=login"
         class="inline-flex items-center justify-center gap-2
                w-full py-3 rounded-full font-bold
                text-white bg-admin hover:bg-black transition">
        ⚙️ Đăng nhập Admin
      </a>
    </div>

  </main>

</body>
</html>
