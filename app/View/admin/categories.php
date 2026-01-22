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
    <title>Quản lý danh mục | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- ========== SIDEBAR ========== -->
    <?php require 'app/View/layouts/layoutsadmin/sidebaradmin.php'; ?>

    <!-- ========== MAIN ========== -->
    <main class=" ml-72 flex-1 p-8 overflow-y-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold">📂 Quản lý danh mục</h1>

            <button onclick="openAddModal()" class="px-6 py-3 bg-orange-500 hover:bg-orange-600
           text-white rounded-xl font-bold text-lg shadow">
                ➕ Thêm danh mục
            </button>
        </div>

        <!-- ========== TABLE ========== -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

            <table class="w-full text-lg">
                <thead class="bg-gray-200 text-gray-800">
                    <tr>
                        <th class="p-5 text-left w-24">#</th>
                        <th class="text-left">Tên danh mục</th>
                        <th class="text-center w-64">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($categories as $c): ?>
                    <tr class="border-t hover:bg-orange-50 transition">

                        <td class="p-5 font-bold"><?= $c['id'] ?></td>

                        <td class="font-semibold text-gray-800">
                            <?= htmlspecialchars($c['name']) ?>
                        </td>

                        <td class="text-center space-x-3">
                            <button onclick='openEditModal(<?= json_encode($c, JSON_UNESCAPED_UNICODE) ?>)' class="px-5 py-2 bg-blue-500 hover:bg-blue-600
             text-white rounded-xl font-bold">
                                ✏️ Sửa
                            </button>

                            <a href="?c=admin&a=deleteCategory&id=<?= $c['id'] ?>"
                                onclick="return confirm('Xóa danh mục này?')" class="px-5 py-2 bg-red-500 hover:bg-red-600
              text-white rounded-xl font-bold">
                                ❌ Xóa
                            </a>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>

    </main>

    <!-- ========== MODAL ADD / EDIT ========== -->
    <div id="categoryModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="bg-white w-full max-w-xl rounded-2xl p-8 relative shadow-xl">

            <button onclick="closeModal()" class="absolute top-4 right-4 text-2xl font-bold">✕</button>

            <h2 id="modalTitle" class="text-3xl font-extrabold mb-6">
                ➕ Thêm danh mục
            </h2>

            <form id="categoryForm" class="space-y-6">

                <input type="hidden" name="id" id="categoryId">

                <input type="text" name="name" id="categoryName" placeholder="Tên danh mục"
                    class="w-full p-4 border rounded-xl text-lg" required>

                <button class="w-full py-4 bg-orange-500 hover:bg-orange-600
        text-white rounded-xl font-extrabold text-xl">
                    💾 Lưu danh mục
                </button>

            </form>
        </div>
    </div>

    <!-- ========== SCRIPT ========== -->
    <script>
    function openAddModal() {
        document.getElementById('modalTitle').innerText = '➕ Thêm danh mục';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        showModal();
    }

    function openEditModal(c) {
        document.getElementById('modalTitle').innerText = '✏️ Sửa danh mục';
        document.getElementById('categoryId').value = c.id;
        document.getElementById('categoryName').value = c.name;
        showModal();
    }

    function showModal() {
        const m = document.getElementById('categoryModal');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function closeModal() {
        const m = document.getElementById('categoryModal');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);

        const url = fd.get('id') ?
            '?c=admin&a=updateCategory' :
            '?c=admin&a=addCategory';

        fetch(url, {
                method: 'POST',
                body: fd
            })
            .then(() => location.reload());
    });
    </script>

</body>

</html>