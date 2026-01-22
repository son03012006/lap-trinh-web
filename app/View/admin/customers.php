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
    <title>Khách hàng | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR -->
    <?php require 'app/View/layouts/layoutsadmin/sidebaradmin.php'; ?>

    <!-- MAIN -->
    <main class="ml-72 flex-1 p-10">

        <h1 class="text-4xl font-extrabold mb-10 flex items-center gap-3">
            👤 Quản lý khách hàng
        </h1>

        <div class="bg-white rounded-3xl shadow-xl overflow-x-auto">

            <table class="w-full text-lg">
                <thead class="bg-gray-200 text-gray-800">
                    <tr>
                        <th class="p-4 text-left">#</th>
                        <th class="text-left">Khách hàng</th>
                        <th class="text-left">Email</th>
                        <th class="text-left">SĐT</th>
                        <th class="text-center">Số đơn</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Hành động</th>
                        <th class="text-center">Ngày đăng ký</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="8" class="p-10 text-center text-gray-500">
                            Chưa có khách hàng nào
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php foreach ($customers as $i => $c): ?>
                    <tr class="border-t hover:bg-orange-50 transition">

                        <!-- STT -->
                        <td class="p-4 font-bold">
                            <?= $i + 1 ?>
                        </td>

                        <!-- INFO -->
                        <td class="flex items-center gap-4 p-4">
                            <img src="/bantrasuamain/public/assets/img/avatars/<?= $c['avatar'] ?: 'user.png' ?>"
                                class="w-14 h-14 rounded-full object-cover border">
                            <div>
                                <p class="font-bold text-gray-800">
                                    <?= htmlspecialchars($c['fullname']) ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    ID: <?= $c['id'] ?>
                                </p>
                            </div>
                        </td>

                        <!-- EMAIL -->
                        <td class="p-4">
                            <?= htmlspecialchars($c['email']) ?>
                        </td>

                        <!-- PHONE -->
                        <td class="p-4">
                            <?= htmlspecialchars($c['phone']) ?>
                        </td>

                        <!-- TOTAL ORDERS -->
                        <td class="text-center font-bold text-orange-600">
                            <?= (int)$c['total_orders'] ?>
                        </td>

                        <!-- STATUS -->
                        <td class="text-center font-bold">
                            <?php if ($c['status'] === 'blocked'): ?>
                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700">
                                🚫 Bị khóa
                            </span>
                            <?php else: ?>
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700">
                                ✅ Hoạt động
                            </span>
                            <?php endif; ?>
                        </td>

                        <!-- ACTION -->
                        <td class="text-center">
                            <?php if ($c['status'] === 'blocked'): ?>
                            <button onclick="toggleUser(<?= $c['id'] ?>, 'active')"
                                class="px-4 py-2 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold">
                                🔓 Mở khóa
                            </button>
                            <?php else: ?>
                            <button onclick="toggleUser(<?= $c['id'] ?>, 'blocked')"
                                class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold">
                                🔒 Khóa
                            </button>
                            <?php endif; ?>
                        </td>

                        <!-- DATE -->
                        <td class="text-center">
                            <?= date('d/m/Y', strtotime($c['created_at'])) ?>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>

    </main>

    <script>
    function toggleUser(id, status) {
        const msg = status === 'blocked' ?
            'Bạn có chắc chắn muốn KHÓA tài khoản này?' :
            'Bạn có chắc chắn muốn MỞ KHÓA tài khoản này?';

        if (!confirm(msg)) return;

        fetch('?c=admin&a=toggleUserStatus', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}&status=${status}`
            })
            .then(res => {
                if (res.ok) location.reload();
                else alert('Có lỗi xảy ra');
            });
    }
    </script>

</body>

</html>