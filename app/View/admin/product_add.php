<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm sản phẩm</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">

<form method="post" enctype="multipart/form-data"
      class="bg-white p-8 rounded-xl shadow w-full max-w-lg">

<h2 class="text-2xl font-bold mb-6">➕ Thêm sản phẩm</h2>

<select name="category_id" class="w-full p-3 border rounded mb-4">
<?php foreach($categories as $c): ?>
  <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
<?php endforeach; ?>
</select>

<input name="name" placeholder="Tên sản phẩm"
       class="w-full p-3 border rounded mb-4" required>

<textarea name="description"
          placeholder="Mô tả"
          class="w-full p-3 border rounded mb-4"></textarea>

<input name="price" type="number" placeholder="Giá"
       class="w-full p-3 border rounded mb-4" required>

<input name="stock" type="number" placeholder="Số lượng"
       class="w-full p-3 border rounded mb-4" required>

<label class="flex items-center gap-2 mb-4">
  <input type="checkbox" name="is_best_seller" value="1">
  Sản phẩm bán chạy
</label>

<input type="file" name="image" required class="mb-4">

<button class="w-full bg-orange-500 text-white py-3 rounded font-bold">
  Lưu sản phẩm
</button>

</form>
</body>
</html>
