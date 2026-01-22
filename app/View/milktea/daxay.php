<?php
// an toàn biến
$products = $products ?? [];
?>

<?php require_once "app/View/layouts/header.php"; ?>

<main class="max-w-7xl mx-auto px-6 py-16">

  <div class="mb-16">
    <h2 class="text-4xl font-extrabold flex items-center gap-3 text-sky-600">
      🧊 Đá xay mát lạnh
    </h2>
    <div class="w-28 h-1 bg-gradient-to-r from-sky-400 to-cyan-400 mt-4 rounded-full"></div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-24">

    <?php foreach ($products as $p): ?>
      <div class="text-center group">

        <div class="overflow-hidden rounded-xl">
             <img
              src="/bantrasuamain/public/assets/img/<?= htmlspecialchars($p->image) ?>"
              alt="<?= htmlspecialchars($p->name) ?>"
              class="h-[320px] mx-auto object-cover
                     drop-shadow-[0_25px_35px_rgba(0,0,0,0.15)]
                     transition-transform duration-500
                     group-hover:scale-110"
            >
        </div>

        <h4 class="mt-6 text-lg font-semibold text-sky-700">
          <?= htmlspecialchars($p->name) ?>
        </h4>

        <button
          onclick="openModal(
            <?= (int)$p->id ?>,
            '<?= htmlspecialchars($p->name, ENT_QUOTES) ?>',
            '/bantrasuamain/public/assets/img/<?= htmlspecialchars($p->image, ENT_QUOTES) ?>'
          )"
          class="mt-4 inline-flex items-center gap-2
                 px-6 py-2.5 rounded-full
                 border-2 border-sky-500
                 text-sky-600 font-semibold
                 hover:bg-sky-500 hover:text-white
                 transition shadow-sm hover:shadow-md"
        >
          <span class="material-symbols-outlined text-[20px]">visibility</span>
          Chi tiết
        </button>

      </div>
    <?php endforeach; ?>

  </div>

  <?php if (isset($totalPages) && $totalPages > 1): ?>
      <div class="mt-20 flex justify-center items-center gap-2">
          
          <?php 
              // Giữ lại tham số cat và keyword khi chuyển trang
              $currentCat = $_GET['cat'] ?? 'daxay'; 
              $currentKeyword = $_GET['keyword'] ?? '';
              $baseLink = "?c=product&a=index&cat=" . urlencode($currentCat) . "&keyword=" . urlencode($currentKeyword) . "&page=";
          ?>

          <?php if ($currentPage > 1): ?>
              <a href="<?= $baseLink . ($currentPage - 1) ?>" 
                 class="w-10 h-10 flex items-center justify-center rounded-full border border-sky-300 text-sky-600 hover:bg-sky-50 hover:border-sky-500 transition">
                  <span class="material-symbols-outlined text-sm">arrow_back_ios_new</span>
              </a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a href="<?= $baseLink . $i ?>" 
                 class="w-10 h-10 flex items-center justify-center rounded-full font-bold transition
                        <?= $i == $currentPage 
                            ? 'bg-sky-600 text-white shadow-lg shadow-sky-200 pointer-events-none' 
                            : 'border border-sky-300 text-sky-600 hover:bg-sky-50 hover:border-sky-500' ?>">
                  <?= $i ?>
              </a>
          <?php endfor; ?>

          <?php if ($currentPage < $totalPages): ?>
              <a href="<?= $baseLink . ($currentPage + 1) ?>" 
                 class="w-10 h-10 flex items-center justify-center rounded-full border border-sky-300 text-sky-600 hover:bg-sky-50 hover:border-sky-500 transition">
                  <span class="material-symbols-outlined text-sm">arrow_forward_ios</span>
              </a>
          <?php endif; ?>

      </div>
  <?php endif; ?>
  </main>

<div id="productModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm">

  <div class="bg-[#eef9ff] rounded-3xl max-w-4xl w-full p-12
              relative grid grid-cols-1 md:grid-cols-2 gap-12 shadow-2xl transform transition-all scale-95" id="modalContent">

    <button onclick="closeModal()"
            class="absolute top-4 right-5 text-3xl font-bold
                   text-gray-400 hover:text-sky-500 transition">
      ✕
    </button>

    <div class="flex justify-center items-center">
      <img id="modalImage"
           class="h-[440px] object-contain drop-shadow-xl">
    </div>

    <div class="flex flex-col justify-center">
      <h3 id="modalName"
          class="text-3xl font-extrabold mb-6 text-sky-600"></h3>

      <p class="font-semibold mb-3 text-gray-700">Chọn size:</p>
      <div id="sizeOptions" class="flex flex-wrap gap-4 mb-6"></div>

      <div class="flex items-baseline gap-2 mb-6">
        <span class="text-gray-600 font-medium">Giá:</span>
        <span id="modalPrice" class="text-3xl font-bold text-sky-600">—</span>
      </div>

      <div class="mb-6">
          <label class="block font-semibold mb-2 text-sm text-gray-700">Ghi chú:</label>
          <textarea id="cartNote" rows="3"
              class="w-full px-4 py-3 rounded-xl
                     border border-sky-300 bg-white
                     focus:outline-none focus:ring-2 focus:ring-sky-400
                     text-sm resize-none shadow-sm"
              placeholder="Thêm kem, ít ngọt, nhiều đá..."></textarea>
      </div>

      <button
        onclick="addToCartWithAnimation()"
        class="w-full py-3.5 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-500
               hover:from-sky-600 hover:to-cyan-600 text-white font-bold text-lg shadow-lg
               transform active:scale-95 transition-all flex justify-center items-center gap-2">
        <span class="material-symbols-outlined">shopping_cart</span>
        Thêm vào giỏ hàng
      </button>

      <input type="hidden" id="cartProductId">
      <input type="hidden" id="cartSize">

      <p class="mt-4 italic text-sm text-gray-500 text-center">
        * Sản phẩm dùng ngon nhất khi uống lạnh ❄️
      </p>
    </div>

  </div>
</div>

<script>
function openModal(id, name, image) {
  document.getElementById('modalName').innerText = name;
  document.getElementById('modalImage').src = image;
  document.getElementById('cartProductId').value = id;
  document.getElementById('cartNote').value = ''; // Reset ghi chú

  const modal = document.getElementById('productModal');
  const content = document.getElementById('modalContent');
  
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  
  // Animation popup
  setTimeout(() => {
    content.classList.remove('scale-95');
    content.classList.add('scale-100');
  }, 10);

  fetch(`?c=product&a=getSizes&id=${id}`)
    .then(res => res.json())
    .then(sizes => {
      const box = document.getElementById('sizeOptions');
      box.innerHTML = '';

      if (!sizes.length) {
        box.innerHTML = '<span class="text-gray-500 italic">Chưa có size</span>';
        document.getElementById('modalPrice').innerText = 'Liên hệ';
        return;
      }

      sizes.forEach((s, index) => {
        box.innerHTML += `
          <label class="cursor-pointer group">
            <input type="radio" name="size"
                   ${index === 0 ? 'checked' : ''}
                   onchange="changePrice(${s.price}, '${s.size}')"
                   class="hidden peer">
            <div class="px-5 py-2 border-2 rounded-xl text-gray-600 font-bold
                        peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-700
                        group-hover:border-sky-300 transition-all">
              Size ${s.size}
            </div>
          </label>
        `;
      });

      if(sizes.length > 0) {
        changePrice(sizes[0].price, sizes[0].size);
      }
    });
}

function changePrice(price, size) {
  document.getElementById('modalPrice').innerText = price.toLocaleString() + 'đ';
  document.getElementById('cartSize').value = size;
}

function closeModal() {
  const modal = document.getElementById('productModal');
  const content = document.getElementById('modalContent');

  content.classList.remove('scale-100');
  content.classList.add('scale-95');

  setTimeout(() => {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
  }, 150);
}

function addToCartWithAnimation() {
  const productId = document.getElementById('cartProductId').value;
  const size      = document.getElementById('cartSize').value;
  const note      = document.getElementById('cartNote').value.trim();
  const modalImg  = document.getElementById('modalImage');

  if (!size) {
       alert('Vui lòng chọn size!');
       return;
  }

  /* ===== ANIMATION ===== */
  const rect = modalImg.getBoundingClientRect();
  const img = modalImg.cloneNode(true);
  
  Object.assign(img.style, {
      position: 'fixed',
      left: rect.left + 'px',
      top: rect.top + 'px',
      width: rect.width + 'px',
      height: rect.height + 'px',
      borderRadius: '12px',
      zIndex: 9999,
      transition: 'all 0.8s cubic-bezier(0.2, 1, 0.3, 1)',
      pointerEvents: 'none'
  });
  
  document.body.appendChild(img);

  const cartIcon = document.getElementById('cartCount') || document.querySelector('.fa-shopping-cart');

  if (cartIcon) {
      const cartRect = cartIcon.getBoundingClientRect();
      const dx = cartRect.left + cartRect.width/2 - (rect.left + rect.width/2);
      const dy = cartRect.top + cartRect.height/2 - (rect.top + rect.height/2);

      requestAnimationFrame(() => {
        img.style.transform = `translate(${dx}px, ${dy}px) scale(0.1)`;
        img.style.opacity = '0';
      });
  } else {
      requestAnimationFrame(() => {
        img.style.transform = `translate(0px, -200px) scale(0.1)`;
        img.style.opacity = '0';
      });
  }

  // Khi animation kết thúc
  img.addEventListener('transitionend', () => img.remove());

  /* ===== AJAX GỬI DATA ===== */
  fetch('?c=cart&a=add', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `product_id=${productId}&size=${encodeURIComponent(size)}&note=${encodeURIComponent(note)}`
  })
  .then(res => res.json())
  .then(res => {
    if (res.success) {
      const countEl = document.getElementById('cartCount');
      if(countEl) countEl.innerText = res.count;
      closeModal();
    } else {
      alert(res.message || 'Thêm vào giỏ hàng thất bại!');
    }
  })
  .catch(err => console.error(err));
}
</script>

<?php require_once "app/View/layouts/footer.php"; ?>