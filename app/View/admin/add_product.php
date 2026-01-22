<!DOCTYPE html>
<html>
<head>
<title>Thêm sản phẩm</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-10 bg-gray-100">

<h1 class="text-3xl font-extrabold mb-6">➕ Thêm sản phẩm</h1>

<form method="post" action="?c=admin&a=storeProduct"
      enctype="multipart/form-data"
      class="bg-white p-8 rounded-2xl shadow space-y-5 max-w-xl">

<input name="name" required placeholder="Tên sản phẩm" class="w-full p-3 border rounded-xl">

<select name="category_id" class="w-full p-3 border rounded-xl">
<?php foreach($cats as $c): ?>
  <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
<?php endforeach; ?>
</select>

<textarea name="description" placeholder="Mô tả" class="w-full p-3 border rounded-xl"></textarea>

<input name="price" type="number" required placeholder="Giá" class="w-full p-3 border rounded-xl">
<input name="stock" type="number" required placeholder="Kho" class="w-full p-3 border rounded-xl">

<input type="file" name="image" required>

<button class="w-full py-3 bg-orange-500 text-white font-bold rounded-xl">
  Lưu sản phẩm
</button>

</form>
</body>
</html>
