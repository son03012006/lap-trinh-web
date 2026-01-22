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
    <title>Quản lý sản phẩm | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR -->
    <?php require 'app/View/layouts/layoutsadmin/sidebaradmin.php'; ?>

    <!-- MAIN -->
    <main class="ml-72 flex-1 p-8">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold">🧋 Quản lý sản phẩm</h1>
            <button onclick="openAddModal()"
                class="px-6 py-3 bg-orange-500 text-white rounded-xl font-bold text-lg shadow">
                ➕ Thêm sản phẩm
            </button>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-2xl shadow-xl overflow-x-auto">
            <table class="w-full text-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-5">Ảnh</th>
                        <th>Tên</th>
                        <th class="text-center">Danh mục</th>
                        <th class="text-center">Giá</th>
                        <th class="text-center">Kho</th>
                        <th class="text-center">Bán chạy</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr class="border-t hover:bg-orange-50">

                        <td class="p-5">
                            <img src="public/assets/img/<?= htmlspecialchars($p['image']) ?>"
                                class="w-24 h-24 object-cover rounded-xl border">
                        </td>

                        <td class="font-bold"><?= htmlspecialchars($p['name']) ?></td>

                        <td class="text-center">
                            <span class="px-4 py-2 bg-gray-100 rounded-full">
                                <?= htmlspecialchars($p['category_name']) ?>
                            </span>
                        </td>

                        <td class="text-center text-orange-600 font-extrabold">
                            <?= number_format($p['price']) ?>đ
                        </td>

                        <td class="text-center"><?= $p['stock'] ?></td>

                        <td class="text-center text-2xl">
                            <?= $p['is_best_seller'] ? '🔥' : '—' ?>
                        </td>

                        <td class="text-center space-x-2">
                            <button onclick='openEditModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                class="px-5 py-3 bg-blue-500 text-white rounded-xl font-bold">
                                ✏️ Sửa
                            </button>

                            <a href="?c=admin&a=deleteProduct&id=<?= $p['id'] ?>"
                                onclick="return confirm('Xóa sản phẩm này?')"
                                class="px-5 py-3 bg-red-500 text-white rounded-xl font-bold">
                                ❌ Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </table>
        </div>

        <!-- ========== PAGINATION ========== -->
        <div class="flex justify-center mt-8">
            <nav class="flex items-center gap-2">

                <!-- PREV -->
                <?php if ($page > 1): ?>
                <a href="?c=admin&a=products&page=<?= $page - 1 ?>"
                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold">
                    «
                </a>
                <?php endif; ?>

                <!-- PAGE NUMBERS -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?c=admin&a=products&page=<?= $i ?>" class="px-4 py-2 rounded-lg font-bold
                <?= $i == $page
                    ? 'bg-orange-500 text-white'
                    : 'bg-gray-200 hover:bg-gray-300' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <!-- NEXT -->
                <?php if ($page < $totalPages): ?>
                <a href="?c=admin&a=products&page=<?= $page + 1 ?>"
                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 font-semibold">
                    »
                </a>
                <?php endif; ?>

            </nav>
        </div>

        </div>

    </main>

    <!-- MODAL -->
    <div id="productModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="bg-white w-full max-w-2xl rounded-2xl p-8 relative">

            <button onclick="closeModal()" class="absolute top-4 right-4 text-2xl font-bold">✕</button>

            <h2 id="modalTitle" class="text-3xl font-extrabold mb-6"></h2>

            <form id="productForm" enctype="multipart/form-data" class="space-y-5">

                <input type="hidden" name="id" id="productId">

                <input name="name" id="name" class="w-full p-4 border rounded-xl" required>

                <select name="category_id" id="category" class="w-full p-4 border rounded-xl">
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <input name="price" id="price" type="number" class="w-full p-4 border rounded-xl">
                <input name="stock" id="stock" type="number" class="w-full p-4 border rounded-xl">

                <textarea name="description" id="description" class="w-full p-4 border rounded-xl"></textarea>

                <input type="file" name="image">

                <label class="flex items-center gap-3 font-semibold">
                    <input type="checkbox" id="best" name="is_best_seller" value="1">
                    Bán chạy
                </label>

                <button class="w-full py-4 bg-orange-500 text-white rounded-xl font-extrabold text-xl">
                    💾 Lưu sản phẩm
                </button>

            </form>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
    function openAddModal() {
        document.getElementById('modalTitle').innerText = '➕ Thêm sản phẩm';
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('best').checked = false;
        showModal();
    }

    function openEditModal(p) {
        document.getElementById('modalTitle').innerText = '✏️ Sửa sản phẩm';
        productId.value = p.id;
        name.value = p.name;
        category.value = p.category_id;
        price.value = p.price;
        stock.value = p.stock;
        description.value = p.description;
        best.checked = p.is_best_seller == 1;
        showModal();
    }

    function showModal() {
        productModal.classList.remove('hidden');
        productModal.classList.add('flex');
    }

    function closeModal() {
        productModal.classList.add('hidden');
    }

    productForm.onsubmit = e => {
        e.preventDefault();
        const fd = new FormData(productForm);
        fetch(fd.get('id') ? '?c=admin&a=updateProduct' : '?c=admin&a=addProduct', {
            method: 'POST',
            body: fd
        }).then(() => location.reload());
    }
    </script>

</body>

</html>