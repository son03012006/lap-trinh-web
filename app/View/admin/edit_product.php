<!DOCTYPE html>
<html>
<head>
<title>Sửa sản phẩm</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-10 bg-gray-100">

<h1 class="text-3xl font-extrabold mb-6">✏️ Sửa sản phẩm</h1>

<form method="post" action="?c=admin&a=updateProduct"
      enctype="multipart/form-data"
      class="bg-white p-8 rounded-2xl shadow space-y-5 max-w-xl">

<input type="hidden" name="id" value="<?= $product['id'] ?>">

<input name="name" value="<?= $product['name'] ?>" class="w-full p-3 border rounded-xl">

<select name="category_id" class="w-full p-3 border rounded-xl">
<?php foreach($cats as $c): ?>
<option value="<?= $c['id'] ?>" <?= $c['id']==$product['category_id']?'selected':'' ?>>
  <?= $c['name'] ?>
</option>
<?php endforeach; ?>
</select>

<textarea name="description" class="w-full p-3 border rounded-xl">
<?= $product['description'] ?>
</textarea>

<input name="price" type="number" value="<?= $product['price'] ?>" class="w-full p-3 border rounded-xl">
<input name="stock" type="number" value="<?= $product['stock'] ?>" class="w-full p-3 border rounded-xl">

<p>Ảnh hiện tại:</p>
<img src="public/assets/img/<?= $product['image'] ?>" class="w-32 rounded">

<input type="file" name="image">

<button class="w-full py-3 bg-blue-500 text-white font-bold rounded-xl">
  Cập nhật
</button>

</form>
</body>
</html>
