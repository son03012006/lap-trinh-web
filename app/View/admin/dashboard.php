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
    <title>Admin Dashboard | TSN MilkTea</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- ================= SIDEBAR ================= -->
    <?php require 'app/View/layouts/layoutsadmin/sidebaradmin.php'; ?>

    <!-- ================= MAIN ================= -->
    <main class="ml-72 flex-1 p-8 overflow-x-hidden">

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold mb-2">⚙️ Admin Dashboard</h1>
            <p class="text-gray-600">
                Xin chào,
                <span class="font-bold text-orange-500">
                    <?= htmlspecialchars($_SESSION['admin']['fullname']) ?>
                </span>
            </p>
        </div>

        <!-- ================= STATS ================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

            <div class="bg-white rounded-2xl shadow p-6">
                <p class="text-gray-500 text-sm">Tổng đơn hàng</p>
                <p class="text-4xl font-extrabold mt-2"><?= $totalOrders ?></p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6">
                <p class="text-gray-500 text-sm">Doanh thu</p>
                <p class="text-4xl font-extrabold mt-2 text-green-600">
                    <?= number_format($totalRevenue) ?>đ
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6">
                <p class="text-gray-500 text-sm">Sản phẩm</p>
                <p class="text-4xl font-extrabold mt-2"><?= $totalProducts ?></p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6">
                <p class="text-gray-500 text-sm">Khách hàng</p>
                <p class="text-4xl font-extrabold mt-2"><?= $totalCustomers ?></p>
            </div>

        </div>

        <!-- ================= QUICK ACTIONS ================= -->
        <div class="bg-white rounded-2xl shadow p-6 mb-10">
            <h2 class="text-xl font-bold mb-4">⚡ Thao tác nhanh</h2>

            <div class="flex flex-wrap gap-4">
                <a href="?c=admin&a=products"
                    class="px-6 py-3 bg-orange-500 text-white rounded-xl font-bold hover:bg-orange-600">
                    ➕ Thêm sản phẩm
                </a>

                <a href="?c=admin&a=orders"
                    class="px-6 py-3 bg-blue-500 text-white rounded-xl font-bold hover:bg-blue-600">
                    📦 Xem đơn hàng
                </a>

                <a href="?c=admin&a=categories"
                    class="px-6 py-3 bg-gray-700 text-white rounded-xl font-bold hover:bg-gray-800">
                    📂 Quản lý danh mục
                </a>
            </div>
        </div>

        <!-- ================= LATEST ORDERS ================= -->
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-xl font-bold mb-4">🧾 Đơn hàng mới</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-lg">
                    <thead>
                        <tr class="border-b text-gray-600">
                            <th class="py-3">#</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestOrders)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500">
                                Chưa có đơn hàng nào
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($latestOrders as $o): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 font-semibold">#<?= $o['id'] ?></td>
                            <td><?= htmlspecialchars($o['fullname']) ?></td>
                            <td class="text-orange-500 font-bold">
                                <?= number_format($o['total_amount']) ?>đ
                            </td>
                            <td><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>

</html>