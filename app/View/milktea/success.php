<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Đặt hàng thành công</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f8f7f6] min-h-screen flex items-center justify-center">

<div class="bg-white rounded-2xl shadow p-10 max-w-md text-center">
  <div class="text-green-500 text-6xl mb-4">✅</div>

  <h2 class="text-2xl font-extrabold mb-2">
    Đặt hàng thành công!
  </h2>

  <p class="text-gray-600 mb-6">
    Đơn hàng #<?= htmlspecialchars($_GET['id']) ?> đã được ghi nhận.<br>
    TSN MilkTea sẽ giao hàng sớm nhất 🚚
  </p>

  <div class="flex gap-4 justify-center">
    <a href="?c=order&a=history"
       class="px-5 py-2 rounded-lg bg-orange-500 text-white font-bold">
       🧾 Xem lịch sử mua hàng
    </a>

    <a href="?c=product&cat=all"
       class="px-5 py-2 rounded-lg border font-semibold">
       🧋 Tiếp tục mua
    </a>
  </div>
</div>

</body>
</html>
