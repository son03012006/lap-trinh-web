<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Internet Banking</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center">

<div class="bg-white w-full max-w-md rounded-2xl shadow p-6">

<h2 class="text-xl font-extrabold text-center mb-4 text-blue-700">
🏦 Internet Banking
</h2>

<div class="space-y-3 text-sm">

<div>
<b>Ngân hàng:</b> Vietcombank
</div>

<div>
<b>Số tài khoản nhận:</b> 0935193460
</div>

<div>
<b>Chủ tài khoản:</b> Trần Lê Quang Trọng
</div>

<div>
<b>Số tiền:</b>
<span class="text-red-600 font-bold">
<?= number_format($order['total_amount']) ?> đ
</span>
</div>

<div>
<b>Nội dung chuyển khoản:</b>
Thanh toán đơn hàng #<?= $order['id'] ?>
</div>

</div>

<button id="confirmBtn"
        class="w-full mt-6 py-3 bg-blue-600 hover:bg-blue-700
               text-white font-bold rounded-xl">
Xác nhận chuyển khoản
</button>

<p class="text-xs text-gray-500 text-center mt-3">
* Đây là giao diện giả lập phục vụ mục đích demo
</p>

</div>

<script>
document.getElementById('confirmBtn').addEventListener('click', ()=>{
  if(!confirm('Xác nhận hoàn tất giao dịch?')) return;

  fetch('?c=order&a=completeFake', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'order_id=<?= $order['id'] ?>'
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.success){
      alert('Thanh toán thành công!');
      window.location.href='/?c=order&a=success&id=<?= $order['id'] ?>';
    }else{
      alert('Lỗi');
    }
  });
});
</script>

</body>
</html>
