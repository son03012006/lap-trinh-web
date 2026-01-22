<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
global $dbh;

/* ================= UNREAD CHAT COUNT ================= */
$unreadCount = 0;

if (isset($_SESSION['admin'])) {
    $stmt = $dbh->query("
        SELECT COUNT(*) 
        FROM chats 
        WHERE sender = 'user' AND is_read = 0
    ");
    $unreadCount = (int)$stmt->fetchColumn();
}
?>

<aside class="fixed top-0 left-0 w-72 h-screen bg-white shadow-xl flex flex-col">
    <!-- LOGO -->
    <div class="p-6 border-b">
        <h2 class="text-3xl font-extrabold text-orange-500 flex items-center gap-2">
            🧋 TSN MilkTea
        </h2>
    </div>

    <!-- ADMIN INFO -->
    <div class="p-6 flex items-center gap-4 border-b">
        <img src="/bantrasuamain/public/assets/img/avatars/<?= $_SESSION['admin']['avatar'] ?? 'admin.png' ?>"
            class="w-14 h-14 rounded-full object-cover border">
        <div>
            <p class="font-bold text-gray-800">
                <?= htmlspecialchars($_SESSION['admin']['fullname'] ?? 'Admin') ?>
            </p>
            <p class="text-sm text-gray-500">Quản trị viên</p>
        </div>
    </div>

    <!-- MENU -->
    <nav class="flex-1 p-4 space-y-2 text-lg">

        <a href="?c=admin&a=dashboard"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-100 font-semibold">
            📊 Dashboard
        </a>

        <a href="?c=admin&a=products"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-100 font-semibold">
            🧋 Quản lý sản phẩm
        </a>

        <a href="?c=admin&a=categories"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-100 font-semibold">
            📂 Danh mục
        </a>

        <a href="?c=admin&a=orders"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-100 font-semibold">
            📦 Đơn hàng
        </a>

        <a href="?c=admin&a=customers"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-100 font-semibold">
            👤 Khách hàng
        </a>

        <a href="?c=admin&a=stats"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-100 font-semibold">
            📈 Thống kê
        </a>

        <!-- ================= CHAT KHÁCH HÀNG ================= -->
        <a href="?c=admin&a=chat" class="flex items-center justify-between px-4 py-3 rounded-xl font-semibold
              <?= $unreadCount > 0
                  ? 'bg-orange-50 text-orange-600'
                  : 'hover:bg-orange-100 text-gray-700' ?>">
            <span class="flex items-center gap-3">
                💬 Chat khách hàng
            </span>

            <?php if ($unreadCount > 0): ?>
            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                <?= $unreadCount ?>
            </span>
            <?php endif; ?>
        </a>

        <!-- LOGOUT -->
        <a href="?c=admin&a=logout" class="flex items-center gap-3 px-4 py-3 rounded-xl
              bg-red-500 hover:bg-red-600 text-white font-extrabold">
            🚪 Đăng xuất
        </a>

    </nav>

</aside>