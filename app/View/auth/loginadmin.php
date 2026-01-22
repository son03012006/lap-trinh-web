<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin Login | TSN MilkTea</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
  <h2 class="text-3xl font-extrabold text-center mb-6 text-orange-500">
    🔐 ADMIN LOGIN
  </h2>

  <?php if(!empty($_SESSION['admin_error'])): ?>
    <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-center">
      <?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
    </div>
  <?php endif; ?>

  <form method="post" action="?c=admin&a=handleLogin" class="space-y-5">
    <input name="email" type="email" placeholder="Admin Email"
           class="w-full p-3 border rounded-lg" required>

    <input name="password" type="password" placeholder="Password"
           class="w-full p-3 border rounded-lg" required>

    <button class="w-full bg-orange-500 hover:bg-orange-600
                   text-white py-3 rounded-lg font-bold">
      Đăng nhập Admin
    </button>
  </form>

  <p class="text-center text-sm mt-6">
    <a href="?c=auth&a=login&switch=1&logoutAdmin=1"
   class="text-sm text-blue-600 underline">
   ← Quay lại đăng nhập người dùng
</a>

  </p>
</div>

</body>
</html>
