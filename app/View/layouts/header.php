<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$currentCat = strtolower($_GET['cat'] ?? 'all');

$isLoggedIn = false;
$userAvatar = 'default-avatar.png';

if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    if (!empty($_SESSION['user']['id'])) $isLoggedIn = true;
    if (!empty($_SESSION['user']['avatar'])) {
        $userAvatar = $_SESSION['user']['avatar'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>TSN MilkTea</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
.material-symbols-outlined{
  font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;
}
/* Hiệu ứng trượt lên cho chat */
#chatWidget {
    transition: transform 0.3s ease-in-out, opacity 0.3s;
}
.chat-hidden {
    transform: translateY(100%) scale(0.9);
    opacity: 0;
    pointer-events: none;
}
/* Animation tin nhắn */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>

<body class="bg-[#f8f7f6] font-['Plus_Jakarta_Sans']">

<header class="sticky top-0 bg-white shadow-sm z-40">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

    <a href="?c=product&cat=all" class="text-xl font-extrabold text-orange-500">
      🧋 TSN MilkTea
    </a>

    <nav class="hidden lg:flex gap-6">
      <?php
      $cats = [
        'all'=>'Trang chủ',
        'trasua'=>'Trà sữa',
        'traicay'=>'Trà trái cây',
        'daxay'=>'Đá xay',
        'caphe'=>'Cà phê'
      ];
      foreach ($cats as $k=>$v):
      ?>
        <a href="?c=product&cat=<?= $k ?>"
           class="<?= $currentCat===$k
             ? 'text-orange-500 border-b-2 border-orange-500 font-semibold'
             : 'text-gray-600 hover:text-orange-500' ?>">
          <?= $v ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="flex items-center gap-4 relative">

      <?php if ($isLoggedIn): ?>

        <button onclick="toggleChat()" class="p-2 hover:bg-gray-100 rounded-lg relative">
          <span class="material-symbols-outlined text-2xl text-gray-600">chat</span>
          <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></span>
        </button>

        <button onclick="toggleUserMenu()"
                class="w-10 h-10 rounded-full overflow-hidden border-2 border-orange-400">
          <img src="/bantrasuamain/public/assets/img/avatars/<?= htmlspecialchars($userAvatar) ?>"
               class="w-full h-full object-cover">
        </button>

        <div id="userMenu"
             class="hidden absolute right-0 top-14 w-60 bg-white rounded-xl shadow-xl border z-50">
          <div class="p-4 border-b bg-gray-50 rounded-t-xl">
             <p class="font-bold text-gray-800">Xin chào,</p>
             <p class="text-sm text-gray-500 truncate"><?= $_SESSION['user']['fullname'] ?? 'Khách hàng' ?></p>
          </div>
          <a href="?c=user&a=profile"
             class="block px-4 py-3 hover:bg-gray-100 flex items-center gap-2">
             <span class="material-symbols-outlined text-[20px]">person</span> Thông tin cá nhân
          </a>
          <a href="?c=order&a=history"
             class="block px-4 py-3 hover:bg-gray-100 flex items-center gap-2">
             <span class="material-symbols-outlined text-[20px]">receipt_long</span> Lịch sử mua hàng
          </a>
          <div class="border-t"></div>
          <a href="?c=auth&a=logout"
             class="block px-4 py-3 text-red-500 hover:bg-red-50 flex items-center gap-2 rounded-b-xl">
             <span class="material-symbols-outlined text-[20px]">logout</span> Đăng xuất
          </a>
        </div>

      <?php else: ?>
        <a href="?c=auth&a=login"
           class="text-orange-500 font-semibold hover:underline">
          Đăng nhập
        </a>
      <?php endif; ?>

      <button onclick="toggleCart()"
              class="relative p-2 rounded-lg hover:bg-gray-100">
        <span class="material-symbols-outlined text-2xl text-gray-600">shopping_cart</span>
        <span id="cartCount"
              class="absolute -top-1 -right-1 bg-orange-500 text-white
                     text-xs w-5 h-5 flex items-center justify-center rounded-full font-bold shadow-sm">
          0
        </span>
      </button>

    </div>
  </div>
</header>

<div id="cartSidebar"
     class="fixed top-0 right-0 w-96 h-full bg-white shadow-2xl
            translate-x-full transition-transform duration-300
            z-[60] flex flex-col">

  <div class="p-5 border-b flex justify-between items-center bg-gray-50">
    <h3 class="text-xl font-extrabold text-gray-800">🛒 Giỏ hàng</h3>
    <button onclick="toggleCart()" class="text-2xl text-gray-500 hover:text-red-500 transition">✕</button>
  </div>

  <div id="cartItems" class="flex-1 p-5 space-y-4 overflow-y-auto">
    <p class="text-gray-500 text-center mt-10">Đang tải giỏ hàng...</p>
  </div>

  <div class="p-5 border-t bg-gray-50">
    <div class="flex justify-between items-end mb-4">
        <span class="text-gray-600 font-medium">Tổng cộng:</span>
        <span class="font-extrabold text-2xl text-orange-600">
            <span id="cartTotal">0</span>đ
        </span>
    </div>
    <button onclick="location.href='?c=cart&a=checkoutPage'"
            class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-bold transition shadow-lg shadow-orange-200">
      Thanh toán ngay
    </button>
  </div>
</div>

<div id="chatWidget"
     class="fixed bottom-4 right-4 w-[350px] bg-white rounded-2xl shadow-2xl border border-gray-100 z-[55] flex flex-col overflow-hidden chat-hidden h-[450px]">

    <div class="bg-gradient-to-r from-orange-500 to-amber-500 p-4 flex justify-between items-center text-white cursor-pointer shadow-md" onclick="toggleChat()">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">support_agent</span>
            </div>
            <div>
                <h4 class="font-bold text-sm">Hỗ trợ khách hàng</h4>
                <p class="text-[10px] text-orange-100 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Trực tuyến
                </p>
            </div>
        </div>
        <button class="hover:bg-white/20 rounded-full p-1"><span class="material-symbols-outlined text-lg">expand_more</span></button>
    </div>

    <div id="chatBody" class="flex-1 bg-[#f9f9f9] p-4 overflow-y-auto space-y-4">
        <div class="text-center text-gray-400 text-xs mt-10">Bắt đầu trò chuyện...</div>
    </div>

    <div class="p-3 bg-white border-t flex gap-2 items-center">
        <input type="text" id="chatInput"
               class="flex-1 bg-gray-100 border-0 rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:bg-white transition outline-none"
               placeholder="Nhập tin nhắn..."
               onkeypress="handleChatKey(event)">
        <button onclick="sendChat()"
                class="w-9 h-9 bg-orange-500 hover:bg-orange-600 text-white rounded-full flex items-center justify-center transition shadow-md transform hover:scale-105">
            <span class="material-symbols-outlined text-[18px] ml-0.5">send</span>
        </button>
    </div>
</div>

<script>
/* --- User Menu --- */
function toggleUserMenu(){
  const menu = document.getElementById('userMenu');
  if(menu) menu.classList.toggle('hidden');
}

/* --- Cart Logic --- */
function toggleCart(){
  const s = document.getElementById('cartSidebar');
  s.classList.toggle('translate-x-full');
  // Mỗi khi mở giỏ hàng sẽ load lại dữ liệu để đảm bảo chính xác
  if(!s.classList.contains('translate-x-full')) loadCart();
}

function loadCart(){
  fetch('?c=cart&a=get')
    .then(r => r.json())
    .then(items => {
      const box = document.getElementById('cartItems');
      const cartCount = document.getElementById('cartCount');
      const cartTotal = document.getElementById('cartTotal');
      box.innerHTML = '';

      if(!items || !items.length){
        box.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <span class="material-symbols-outlined text-6xl mb-2 opacity-20">shopping_basket</span>
                <p>Giỏ hàng trống</p>
            </div>`;
        cartCount.innerText = 0;
        cartTotal.innerText = '0';
        return;
      }

      let totalQty = 0, total = 0;

      items.forEach(i => {
        // Ép kiểu số để tính toán tránh lỗi cộng chuỗi
        const qty = parseInt(i.qty);
        const price = parseInt(i.price);
        
        totalQty += qty;
        total += qty * price;

        /* SỬA LỖI UI: Thêm thẻ span hiển thị số lượng ở giữa 2 nút cộng trừ */
        /* SỬA LỖI JS: Thêm dấu nháy đơn vào '${i.id}' để tránh lỗi nếu ID là chuỗi */
        box.innerHTML += `
          <div class="flex gap-3 border-b border-dashed border-gray-200 pb-4 animate-[fadeIn_0.3s_ease-out]">
            <div class="relative">
                <img src="/bantrasuamain/public/assets/img/${i.image}" 
                     class="w-16 h-16 rounded-xl object-cover border border-gray-100 shadow-sm">
                <span class="absolute -top-2 -left-2 w-5 h-5 bg-gray-800 text-white text-[10px] flex items-center justify-center rounded-full border border-white">
                    ${qty}
                </span>
            </div>

            <div class="flex-1 min-w-0">
              <p class="font-bold text-gray-800 truncate">${i.name}</p>
              <p class="text-xs text-gray-500 font-medium bg-gray-100 inline-block px-1.5 py-0.5 rounded">Size ${i.size}</p>
              
              ${i.note && i.note.trim() !== '' 
                ? `<p class="mt-1 text-xs italic text-gray-400 leading-snug truncate">📝 ${i.note}</p>` 
                : '' 
              }

              <div class="flex justify-between items-center mt-2">
                  <p class="text-orange-600 font-extrabold text-sm">
                    ${(price * qty).toLocaleString()}đ
                  </p>
                  
                  <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200">
                    <button onclick="changeQty('${i.id}', -1)" class="w-7 h-7 flex items-center justify-center hover:bg-gray-200 text-gray-600 rounded-l-lg transition font-bold"> - </button>
                    
                    <span class="w-8 text-center text-sm font-bold text-gray-800 border-x border-gray-200 bg-white leading-7">
                        ${qty}
                    </span>

                    <button onclick="changeQty('${i.id}', 1)" class="w-7 h-7 flex items-center justify-center hover:bg-gray-200 text-gray-600 rounded-r-lg transition font-bold"> + </button>
                  </div>
              </div>
            </div>

            <button onclick="removeItem('${i.id}')" 
                    class="self-start text-gray-300 hover:text-red-500 transition p-1">
              <span class="material-symbols-outlined text-lg">close</span>
            </button>
          </div>
        `;
      });

      cartCount.innerText = totalQty;
      cartTotal.innerText = total.toLocaleString();
    })
    .catch(err => {
        console.error('Lỗi load giỏ hàng:', err);
    });
}

function changeQty(id, delta){
  // Thêm encodeURIComponent để an toàn dữ liệu
  fetch('?c=cart&a=updateQty',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`id=${encodeURIComponent(id)}&delta=${delta}`
  })
  .then(r => r.json())
  .then(res => { 
      if(res.success) {
          loadCart(); 
      } else {
          // Thông báo nếu server trả về lỗi (ví dụ hết hàng)
          alert(res.message || 'Không thể cập nhật số lượng');
      }
  })
  .catch(err => console.error('Lỗi updateQty:', err));
}

function removeItem(id){
  if(!confirm('Xóa sản phẩm này?')) return;
  
  fetch('?c=cart&a=remove',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`id=${encodeURIComponent(id)}`
  })
  .then(r => r.json())
  .then(res => { 
      if(res.success) loadCart(); 
      else alert(res.message || 'Lỗi xóa sản phẩm');
  })
  .catch(err => console.error('Lỗi removeItem:', err));
}

/* --- CHAT LOGIC (Giữ nguyên) --- */
let chatInterval = null;
function toggleChat(){
    const chat = document.getElementById('chatWidget');
    if(chat){
        chat.classList.toggle('chat-hidden');
        if(!chat.classList.contains('chat-hidden')){
            document.getElementById('chatInput').focus();
            loadMessages(); 
            if(!chatInterval) chatInterval = setInterval(loadMessages, 3000);
        } else {
            if(chatInterval) { clearInterval(chatInterval); chatInterval = null; }
        }
    }
}
function handleChatKey(e){ if(e.key === 'Enter') sendChat(); }
function sendChat(){
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if(!msg) return;
    fetch('?c=chat&a=send', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `message=${encodeURIComponent(msg)}`
    })
    .then(res => res.json())
    .then(res => {
        if(res.success){ input.value = ''; loadMessages(); } 
        else { alert(res.message || 'Lỗi gửi tin nhắn'); }
    })
    .catch(err => console.error('Chat Error:', err));
}
function loadMessages(){
    fetch('?c=chat&a=load')
    .then(res => res.json())
    .then(data => {
        const body = document.getElementById('chatBody');
        let html = `
            <div class="flex gap-2 items-end mb-4 animate-[fadeIn_0.3s_ease-out]">
                <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xs font-bold">A</div>
                <div class="bg-white p-3 rounded-2xl rounded-bl-none shadow-sm text-sm text-gray-700 border max-w-[85%]">
                    Xin chào! 👋<br>TSN MilkTea có thể giúp gì cho bạn hôm nay?
                </div>
            </div>`;
        if(data && data.length > 0){
            data.forEach(msg => {
                if(msg.sender === 'user'){
                    html += `<div class="flex gap-2 items-end justify-end mb-2 animate-[fadeIn_0.3s_ease-out]">
                                <div class="bg-orange-500 p-3 rounded-2xl rounded-br-none shadow-md text-sm text-white max-w-[85%] break-words">${escapeHtml(msg.message)}</div>
                            </div>`;
                } else {
                    html += `<div class="flex gap-2 items-end mb-2 animate-[fadeIn_0.3s_ease-out]">
                                <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xs font-bold">A</div>
                                <div class="bg-white p-3 rounded-2xl rounded-bl-none shadow-sm text-sm text-gray-700 border max-w-[85%]">${escapeHtml(msg.message)}</div>
                            </div>`;
                }
            });
        }
        body.innerHTML = html;
        scrollToBottom();
    })
    .catch(err => console.error('Load Msg Error:', err));
}
function scrollToBottom(){ const body = document.getElementById('chatBody'); body.scrollTop = body.scrollHeight; }
function escapeHtml(text) {
  if (!text) return text;
  return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

/* Load cart khi trang vừa chạy */
document.addEventListener('DOMContentLoaded', loadCart);
</script>

</body>
</html>