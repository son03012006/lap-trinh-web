<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header('Location: ?c=admin&a=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thống kê | Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR -->
    <?php require 'app/View/layouts/layoutsadmin/sidebaradmin.php'; ?>

    <!-- MAIN -->
    <main class="ml-72 flex-1 p-10" id="print-area">

        <h1 class="text-4xl font-extrabold mb-8 flex items-center gap-3">
            📊 Thống kê hệ thống
        </h1>

        <!-- ================= FILTER ================= -->
        <form method="GET" class="bg-white rounded-3xl shadow-xl p-8 mb-10">
            <input type="hidden" name="c" value="admin">
            <input type="hidden" name="a" value="stats">

            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">

                <div class="flex gap-6">
                    <div>
                        <label class="text-sm text-gray-500">Từ ngày</label>
                        <input type="date" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>"
                            class="border rounded-xl px-4 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-500">Đến ngày</label>
                        <input type="date" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>"
                            class="border rounded-xl px-4 py-2">
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-bold">
                        🔍 Lọc thống kê
                    </button>

                    <a href="?c=admin&a=stats" class="px-6 py-3 rounded-xl bg-gray-300 hover:bg-gray-400 font-bold">
                        ♻ Reset
                    </a>

                    <button type="button" onclick="printStats()"
                        class="px-6 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold">
                        🖨 In thống kê
                    </button>
                </div>

            </div>
        </form>

        <!-- ================= STATUS ================= -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">

            <div class="bg-yellow-100 rounded-2xl p-6">
                <p class="text-gray-600">⏳ Chờ xử lý</p>
                <p class="text-3xl font-extrabold"><?= $pendingOrders ?></p>
            </div>

            <div class="bg-blue-100 rounded-2xl p-6">
                <p class="text-gray-600">🚚 Đang giao</p>
                <p class="text-3xl font-extrabold"><?= $processingOrders ?></p>
            </div>

            <div class="bg-green-100 rounded-2xl p-6">
                <p class="text-gray-600">✅ Hoàn thành</p>
                <p class="text-3xl font-extrabold"><?= $completedOrders ?></p>
            </div>

            <div class="bg-red-100 rounded-2xl p-6">
                <p class="text-gray-600">❌ Đã hủy</p>
                <p class="text-3xl font-extrabold"><?= $cancelledOrders ?></p>
            </div>

        </div>

        <!-- ================= CHART ================= -->
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-12">
            <h2 class="text-2xl font-bold mb-6">📊 Số đơn theo ngày</h2>

            <?php if (empty($orderByDay)): ?>
            <p class="text-gray-500">Không có dữ liệu trong khoảng thời gian này</p>
            <?php else: ?>
            <canvas id="orderChart" height="120"></canvas>
            <?php endif; ?>
        </div>

        <!-- ================= TOP CUSTOMERS ================= -->
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold mb-6">🏆 Khách hàng mua nhiều nhất</h2>

            <?php if (empty($topCustomers)): ?>
            <p class="text-gray-500">Chưa có dữ liệu</p>
            <?php else: ?>
            <table class="w-full text-lg">
                <thead class="border-b">
                    <tr>
                        <th class="text-left py-3">Khách hàng</th>
                        <th class="text-center py-3">Số đơn</th>
                        <th class="text-right py-3">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topCustomers as $c): ?>
                    <tr class="border-b hover:bg-orange-50">
                        <td class="py-3"><?= htmlspecialchars($c['fullname']) ?></td>
                        <td class="text-center"><?= $c['total_orders'] ?></td>
                        <td class="text-right text-orange-600 font-extrabold">
                            <?= number_format($c['total_spent']) ?>đ
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

    </main>

    <!-- ================= JS ================= -->
    <script>
    function printStats() {
        const content = document.getElementById('print-area').innerHTML;
        const win = window.open('', '', 'width=1200,height=800');

        win.document.write(`
    <html>
    <head>
      <title>Báo cáo thống kê</title>
      <style>
        body { font-family: Arial; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; }
      </style>
    </head>
    <body>
      <h1>📊 Báo cáo thống kê TSN MilkTea</h1>
      ${content}
    </body>
    </html>
  `);

        win.document.close();
        win.focus();
        win.print();
        win.close();
    }
    </script>

    <?php if (!empty($orderByDay)): ?>
    <script>
    const labels = <?= json_encode(array_column($orderByDay, 'order_date')) ?>;
    const data = <?= json_encode(array_column($orderByDay, 'total')) ?>;

    new Chart(document.getElementById('orderChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Số đơn',
                data: data,
                backgroundColor: '#f97316',
                borderRadius: 10,
                barThickness: 50
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    </script>
    <?php endif; ?>

</body>

</html>