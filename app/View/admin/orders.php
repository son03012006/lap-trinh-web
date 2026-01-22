<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Kiểm tra quyền Admin
if (!isset($_SESSION['admin'])) {
    header('Location: ?c=admin&a=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Nunito', sans-serif;
    }

    /* CSS cho thanh cuộn mảnh đẹp hơn */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 flex min-h-screen">

    <?php require 'app/View/layouts/layoutsadmin/sidebaradmin.php'; ?>

    <main class="ml-72 flex-1 p-6 lg:p-10 transition-all duration-300">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
                    <span class="bg-orange-100 text-orange-600 p-2 rounded-lg">📦</span>
                    Quản lý đơn hàng
                </h1>
                <p class="text-gray-500 mt-1 text-base">Theo dõi và xử lý các đơn đặt hàng từ khách.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-[0_4px_25px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-base uppercase tracking-wider">
                            <th class="p-5 font-bold border-b text-center w-20">ID</th>
                            <th class="p-5 font-bold border-b">Khách hàng</th>
                            <th class="p-5 font-bold border-b">Liên hệ</th>
                            <th class="p-5 font-bold border-b w-48 text-center">Ghi chú</th>
                            <th class="p-5 font-bold border-b text-right">Tổng tiền</th>
                            <th class="p-5 font-bold border-b text-center">Trạng thái</th>
                            <th class="p-5 font-bold border-b text-center">Ngày tạo</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 text-base">

                        <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7"
                                class="p-12 text-center text-gray-400 flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-20" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span class="text-lg">Chưa có dữ liệu đơn hàng nào.</span>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($orders as $o):
                            $status = $o['status'] ?? 'pending';
                            $itemNote = $o['ghi_chu_mon'] ?? '';
                            $orderNote = $o['note'] ?? '';
                            $hasNote = !empty($itemNote) || !empty($orderNote);
                            ?>
                        <tr class="hover:bg-orange-50/60 transition-colors duration-200 group">

                            <td class="p-5 text-center font-bold text-gray-700 text-lg">
                                #<?= $o['id'] ?>
                            </td>

                            <td class="p-5">
                                <div class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($o['fullname']) ?>
                                </div>
                                <div class="text-sm text-gray-500 mt-1 truncate max-w-[220px]"
                                    title="<?= htmlspecialchars($o['address'] ?? '') ?>">
                                    📍 <?= htmlspecialchars($o['address'] ?? 'N/A') ?>
                                </div>
                            </td>

                            <td class="p-5 font-medium text-gray-700 text-base">
                                <?= htmlspecialchars($o['phone']) ?>
                            </td>

                            <td class="p-5 text-center relative group/tooltip">
                                <?php if ($hasNote): ?>
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-bold cursor-help shadow-sm border border-yellow-200 transition-transform hover:scale-105">
                                    <span>📝 Lưu ý</span>
                                    <?php if (!empty($itemNote)): ?>
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <?php endif; ?>
                                </div>

                                <div
                                    class="absolute z-50 hidden group-hover/tooltip:block bg-white text-left p-5 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] border border-gray-100 w-80 -left-16 top-full mt-2 ring-1 ring-black/5">
                                    <div
                                        class="absolute -top-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white border-t border-l border-gray-100 transform rotate-45">
                                    </div>

                                    <?php if (!empty($itemNote)): ?>
                                    <div class="mb-4">
                                        <p class="text-xs uppercase font-bold text-blue-600 mb-1 tracking-wider">Yêu cầu
                                            món ăn:</p>
                                        <div
                                            class="bg-blue-50 text-blue-900 text-sm p-3 rounded-lg border border-blue-100 leading-relaxed font-medium">
                                            <?= nl2br(htmlspecialchars(str_replace([', ', ','], "\n• ", '• ' . $itemNote))) ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($orderNote)): ?>
                                    <div>
                                        <p class="text-xs uppercase font-bold text-orange-600 mb-1 tracking-wider">Lời
                                            nhắn đơn hàng:</p>
                                        <div
                                            class="bg-orange-50 text-orange-900 text-sm p-3 rounded-lg border border-orange-100 italic">
                                            "<?= htmlspecialchars($orderNote) ?>"
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <span class="text-gray-300 text-sm font-medium">--</span>
                                <?php endif; ?>
                            </td>

                            <td class="p-5 text-right font-extrabold text-orange-600 text-xl">
                                <?= number_format($o['total_amount']) ?>đ
                            </td>

                            <td class="p-5 text-center">
                                <div class="relative inline-block w-full max-w-[160px]">
                                    <select onchange="updateStatus(<?= $o['id'] ?>, this.value)" class="appearance-none w-full px-4 py-2.5 text-sm font-bold rounded-lg border cursor-pointer outline-none focus:ring-2 focus:ring-offset-1 transition-all shadow-sm
                                        <?php
                                                if ($status == 'pending') {
                                                    echo 'bg-yellow-100 text-yellow-800 border-yellow-200 focus:ring-yellow-300';
                                                } elseif ($status == 'processing') {
                                                    echo 'bg-blue-100 text-blue-800 border-blue-200 focus:ring-blue-300';
                                                } elseif ($status == 'completed') {
                                                    echo 'bg-green-100 text-green-800 border-green-200 focus:ring-green-300';
                                                } else {
                                                    echo 'bg-red-100 text-red-800 border-red-200 focus:ring-red-300';
                                                }
                            ?>">
                                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>⏳ Chờ xử
                                            lý</option>
                                        <option value="processing" <?= $status == 'processing' ? 'selected' : '' ?>>🚚
                                            Đang giao</option>
                                        <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>✅ Hoàn
                                            thành</option>
                                        <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>❌ Đã
                                            hủy</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-600">
                                        <svg class="fill-current h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </td>

                            <td class="p-5 text-center text-sm text-gray-600 font-medium">
                                <span
                                    class="block text-gray-900 font-bold"><?= date('H:i', strtotime($o['created_at'])) ?></span>
                                <span class="text-gray-500"><?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
    function updateStatus(orderId, status) {
        document.body.style.cursor = 'wait';
        fetch('?c=admin&a=updateOrderStatus', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${orderId}&status=${status}`
            })
            .then(response => {
                if (response.ok) location.reload();
                else {
                    alert('Lỗi cập nhật');
                    document.body.style.cursor = 'default';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.body.style.cursor = 'default';
            });
    }
    </script>
</body>

</html>