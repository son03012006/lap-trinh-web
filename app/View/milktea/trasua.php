<?php require_once "app/View/layouts/header.php"; ?>

<main class="max-w-7xl mx-auto px-6 py-14">

    <h2 class="text-3xl font-extrabold mb-16 flex items-center gap-2 text-red-600">
        🧋 Trà sữa truyền thống
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-24">
        <?php foreach ($products as $p): ?>
        <div class="text-center group"> <div class="overflow-hidden rounded-xl">
                <img src="/bantrasuamain/public/assets/img/<?= htmlspecialchars($p->image) ?>"
                     alt="<?= htmlspecialchars($p->name) ?>"
                     class="h-[340px] mx-auto object-cover transition-transform duration-500 group-hover:scale-110">
            </div>

            <h4 class="mt-6 text-lg font-semibold text-red-700">
                <?= htmlspecialchars($p->name) ?>
            </h4>

            <button
                onclick="openModal(
                    <?= (int)$p->id ?>,
                    '<?= htmlspecialchars($p->name, ENT_QUOTES) ?>',
                    '/bantrasuamain/public/assets/img/<?= htmlspecialchars($p->image, ENT_QUOTES) ?>'
                )"
                class="mt-4 inline-flex items-center gap-2 px-6 py-2.5
                       rounded-full border-2 border-red-500
                       text-red-600 font-semibold
                       hover:bg-red-500 hover:text-white transition shadow-sm hover:shadow-md">
                <span class="material-symbols-outlined text-[20px]">visibility</span>
                Chi tiết
            </button>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="mt-16 flex justify-center items-center gap-2">
            
            <?php 
                // Lấy các tham số hiện tại để giữ nguyên khi bấm chuyển trang (ví dụ đang xem trà sữa thì qua trang 2 vẫn là trà sữa)
                $currentCat = $_GET['cat'] ?? 'all';
                $currentKeyword = $_GET['keyword'] ?? '';
                $baseLink = "?c=product&a=index&cat=" . urlencode($currentCat) . "&keyword=" . urlencode($currentKeyword) . "&page=";
            ?>

            <?php if ($currentPage > 1): ?>
                <a href="<?= $baseLink . ($currentPage - 1) ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-600 transition">
                    <span class="material-symbols-outlined text-sm">arrow_back_ios_new</span>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= $baseLink . $i ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-full font-bold transition
                          <?= $i == $currentPage 
                              ? 'bg-red-600 text-white shadow-lg shadow-red-200 pointer-events-none' 
                              : 'border border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-600' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $baseLink . ($currentPage + 1) ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-600 transition">
                    <span class="material-symbols-outlined text-sm">arrow_forward_ios</span>
                </a>
            <?php endif; ?>

        </div>
    <?php endif; ?>
    </main>

<div id="productModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-[#fdeee6] rounded-2xl max-w-4xl w-full p-10 relative grid grid-cols-1 md:grid-cols-2 gap-10 shadow-2xl scale-95 transition-transform" id="modalContent">

        <button onclick="closeModal()"
                class="absolute top-4 right-4 text-2xl font-bold text-gray-400 hover:text-red-500 transition">
            ✕
        </button>

        <div class="flex justify-center items-center">
            <img id="modalImage" class="h-[420px] object-contain drop-shadow-xl">
        </div>

        <div class="flex flex-col justify-center">
            <h3 id="modalName" class="text-3xl font-extrabold mb-6 text-red-600"></h3>

            <p class="font-semibold mb-3 text-gray-700">Chọn size:</p>
            <div id="sizeOptions" class="flex flex-wrap gap-3 mb-6"></div>

            <div class="flex items-baseline gap-2 mb-6">
                <span class="text-gray-600 font-medium">Giá:</span>
                <span id="modalPrice" class="text-3xl font-bold text-orange-600">—</span>
            </div>

            <div class="mb-6">
                <label class="block font-semibold mb-2 text-sm text-gray-700">Ghi chú:</label>
                <textarea id="cartNote" rows="3"
                    class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white
                           focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                           text-sm resize-none shadow-sm"
                    placeholder="Ít đá, nhiều đá, ít đường..."></textarea>
            </div>

            <button onclick="addToCartWithAnimation()"
                class="w-full py-3.5 rounded-xl bg-gradient-to-r from-orange-500 to-red-500
                       hover:from-orange-600 hover:to-red-600 text-white font-bold text-lg shadow-lg 
                       transform active:scale-95 transition-all duration-200 flex justify-center items-center gap-2">
                <span class="material-symbols-outlined">shopping_cart</span>
                Thêm vào giỏ hàng
            </button>

            <input type="hidden" id="cartProductId">
            <input type="hidden" id="cartSize">
        </div>
    </div>
</div>

<script>
function openModal(id, name, image) {
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalImage').src = image;
    document.getElementById('cartProductId').value = id;
    document.getElementById('cartNote').value = '';
    document.getElementById('cartSize').value = '';

    const modal = document.getElementById('productModal');
    const content = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Animation đơn giản khi mở
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
                box.innerHTML = '<span class="text-gray-500 italic">Sản phẩm chưa có size</span>';
                document.getElementById('modalPrice').innerText = 'Liên hệ';
                return;
            }

            sizes.forEach((s, index) => {
                box.innerHTML += `
                    <label class="cursor-pointer group">
                        <input type="radio" name="size"
                               ${index === 0 ? 'checked' : ''}
                               onclick="changePrice(${s.price}, '${s.size}')"
                               class="hidden peer">
                        <div class="px-5 py-2 border-2 border-gray-200 rounded-lg text-gray-600 font-bold
                                    peer-checked:border-orange-500 peer-checked:text-orange-600 peer-checked:bg-orange-50
                                    group-hover:border-orange-300 transition-all">
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
    const size = document.getElementById('cartSize').value;
    const note = document.getElementById('cartNote').value.trim();
    const modalImg = document.getElementById('modalImage');

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
        pointerEvents: 'none' // Để click xuyên qua
    });
    
    document.body.appendChild(img);

    const cartCount = document.getElementById('cartCount');
    
    // Nếu có icon giỏ hàng trên header thì bay về đó, không thì bay lên trời
    if (cartCount) {
        const cartRect = cartCount.getBoundingClientRect();
        const dx = cartRect.left + cartRect.width / 2 - (rect.left + rect.width / 2);
        const dy = cartRect.top + cartRect.height / 2 - (rect.top + rect.height / 2);

        requestAnimationFrame(() => {
            img.style.transform = `translate(${dx}px, ${dy}px) scale(0.1)`;
            img.style.opacity = '0';
        });
    } else {
        requestAnimationFrame(() => {
            img.style.transform = `translate(0px, -300px) scale(0.1)`;
            img.style.opacity = '0';
        });
    }

    img.addEventListener('transitionend', () => img.remove());

    /* ===== AJAX ===== */
    fetch('?c=cart&a=add', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `product_id=${productId}&size=${encodeURIComponent(size)}&note=${encodeURIComponent(note)}`
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            if(document.getElementById('cartCount')){
                 document.getElementById('cartCount').innerText = res.count;
            }
            closeModal();
        } else {
            alert(res.message || 'Thêm vào giỏ hàng thất bại!');
        }
    })
    .catch(err => console.error("Lỗi:", err));
}
</script>
<?php require_once "app/View/layouts/footer.php"; ?>