<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch sử mua hàng | TSN MilkTea</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-[#FFF6ED] min-h-screen">

<?php if (file_exists('app/View/layouts/header.php')) require 'app/View/layouts/header.php'; ?>

<main class="max-w-6xl mx-auto px-8 py-14">

<a href="?c=product"
   class="inline-flex items-center gap-2 text-orange-500 font-semibold text-lg mb-8">
← Quay lại mua hàng
</a>

<h1 class="text-4xl font-extrabold mb-12 flex items-center gap-3">
📜 Lịch sử mua hàng
</h1>

<?php if (empty($orders)): ?>
  <div class="bg-white rounded-3xl p-10 text-center text-xl text-gray-500 shadow">
    Bạn chưa có đơn hàng nào
  </div>
<?php endif; ?>

<?php foreach ($orders as $order): ?>

<?php
$status = $order['status'] ?? 'pending';

$statusText = [
  'pending'    => 'Chờ xử lý',
  'processing' => 'Đang giao',
  'completed'  => 'Hoàn thành',
  'cancelled'  => 'Đã hủy'
];

$statusColor = [
  'pending'    => 'bg-yellow-100 text-yellow-700',
  'processing' => 'bg-blue-100 text-blue-700',
  'completed'  => 'bg-green-100 text-green-700',
  'cancelled'  => 'bg-red-100 text-red-700'
];

$statusIcon = [
  'pending'    => '⏳',
  'processing' => '🚚',
  'completed'  => '✅',
  'cancelled'  => '❌'
];
?>

<div class="bg-white rounded-3xl shadow-xl mb-10 p-10">

  <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-6 mb-8">

    <div>
      <h3 class="text-2xl font-extrabold mb-1">
        Đơn hàng #<?= $order['id'] ?>
      </h3>

      <p class="text-gray-500 text-base mb-3">
        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
      </p>

      <div class="inline-flex items-center gap-3 px-5 py-2 rounded-full text-base font-bold <?= $statusColor[$status] ?>">
        <?= $statusIcon[$status] ?> <?= $statusText[$status] ?>
      </div>
    </div>

    <div class="text-right">
      <p class="text-orange-500 text-3xl font-extrabold">
        <?= number_format($order['total_amount']) ?>đ
      </p>
      <p class="text-gray-500 text-base mt-1">
        Phí ship: <?= number_format($order['shipping_fee']) ?>đ
      </p>
    </div>

  </div>

  <div class="text-lg mb-6">
    📍 <strong>Địa chỉ giao hàng:</strong>
    <span class="text-gray-700"><?= htmlspecialchars($order['address']) ?></span>
  </div>

  <hr class="mb-8">

  <?php if (!empty($orderItems[$order['id']])): ?>
    <?php foreach ($orderItems[$order['id']] as $item): ?>
      <div class="flex items-start gap-6 mb-6 last:mb-0 border-b border-dashed pb-6 last:border-0 last:pb-0">

        <img
          src="public/assets/img/<?= htmlspecialchars($item['image']) ?>"
          alt="<?= htmlspecialchars($item['name']) ?>"
          class="w-24 h-24 rounded-2xl object-cover border"
        >

        <div class="flex-1">
          <p class="text-xl font-bold mb-1">
            <?= htmlspecialchars($item['name']) ?>
          </p>
          <p class="text-base text-gray-500">
            Size <?= htmlspecialchars($item['size']) ?> × <?= (int)$item['qty'] ?>
          </p>
          
          <?php if (!empty($item['note'])): ?>
            <p class="mt-2 text-sm text-gray-600 bg-gray-100 p-2 rounded-lg inline-block">
                📝 Ghi chú: <span class="italic"><?= htmlspecialchars($item['note']) ?></span>
            </p>
          <?php endif; ?>
          </div>

        <div class="text-orange-500 text-xl font-extrabold">
          <?= number_format($item['price']) ?>đ
        </div>

      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>
<?php endforeach; ?>

</main>

<?php if (file_exists('app/View/layouts/footer.php')) require 'app/View/layouts/footer.php'; ?>

</body>
</html>