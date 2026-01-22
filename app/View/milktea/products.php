<?php
require_once 'app/Config/database.php';
global $dbh;

/* ================= BEST SELLER ================= */
$stmt = $dbh->prepare("
  SELECT id, name, description, price, image
  FROM products
  WHERE is_best_seller = 1
  ORDER BY created_at DESC
  LIMIT 6
");
$stmt->execute();
$bestSellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= GỢI Ý ================= */
$stmt = $dbh->prepare("
  SELECT id, name, description, price, image
  FROM products
  ORDER BY RAND()
  LIMIT 6
");
$stmt->execute();
$featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once "app/View/layouts/header.php"; ?>

<main class="max-w-7xl mx-auto px-6 py-20 space-y-32">

    <!-- ================= HERO (GIỮ NGUYÊN) ================= -->
    <section class="rounded-3xl bg-gradient-to-br from-orange-50 to-amber-100 shadow-xl overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center px-12 py-24">
            <div>
                <span class="bg-orange-100 text-orange-600 px-4 py-1 rounded-full font-bold text-sm">
                    TSN MILKTEA
                </span>
                <h1 class="mt-6 text-5xl font-black leading-tight">
                    Trà sữa chuẩn vị<br>
                    <span class="text-orange-500">Dành cho giới trẻ</span>
                </h1>
                <p class="mt-6 text-gray-600 text-lg max-w-xl">
                    Website bán đồ uống trà sữa.
                </p>
            </div>

            <div class="relative flex justify-center">
                <div class="absolute w-[380px] h-[380px] bg-orange-200 rounded-full blur-3xl"></div>
                <img src="/bantrasuamain/public/assets/img/hero1.png" class="relative max-h-[460px] drop-shadow-2xl"
                    alt="Hero">
            </div>
        </div>
    </section>

    <!-- ================= BEST SELLER ================= -->
    <section>
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold">🔥 Sản phẩm bán chạy</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-16">
            <?php foreach ($bestSellers as $b): ?>
            <div class="text-center group">

                <!-- IMAGE + BADGE -->
                <div class="relative">
                    <img src="/bantrasuamain/public/assets/img/<?= htmlspecialchars($b['image']) ?>"
                        class="h-[300px] mx-auto drop-shadow-xl group-hover:scale-105 transition"
                        alt="<?= htmlspecialchars($b['name']) ?>">

                    <span
                        class="absolute top-4 left-4 px-3 py-1 rounded-full bg-orange-500 text-white text-xs font-bold">
                        Best Seller
                    </span>
                </div>

                <!-- NAME -->
                <h4 class="mt-6 text-lg font-semibold text-orange-600">
                    <?= htmlspecialchars($b['name']) ?>
                </h4>

                <!-- DESC -->
                <p class="mt-2 text-sm text-gray-500 max-w-xs mx-auto line-clamp-2">
                    <?= htmlspecialchars($b['description']) ?>
                </p>

                <!-- TAGS -->
                <div class="mt-3 flex justify-center gap-2 text-xs">
                    <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full font-semibold">Đậm vị</span>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-semibold">Tươi mới</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">Hot tuần này</span>
                </div>

                <!-- PRICE -->
                <p class="mt-3 text-orange-500 font-bold text-lg">
                    <?= number_format($b['price']) ?>đ
                </p>

                <!-- CHI TIẾT (GIỮ UI – DÙNG MODAL SIZE) -->
                <button onclick="openModal(
              <?= (int)$b['id'] ?>,
              '<?= htmlspecialchars($b['name'], ENT_QUOTES) ?>',
              '/bantrasuamain/public/assets/img/<?= htmlspecialchars($b['image'], ENT_QUOTES) ?>'
            )" class="mt-4 inline-flex items-center gap-2 px-6 py-2
                   rounded-full border-2 border-orange-500
                   text-orange-600 font-semibold
                   hover:bg-orange-500 hover:text-white transition">
                    👁 Chi tiết
                </button>

            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ================= GỢI Ý HÔM NAY (OPTIONAL) ================= -->
    <section>
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold">✨ Gợi ý hôm nay</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-16">
            <?php foreach ($featured as $f): ?>
            <div class="text-center group">

                <div class="relative">
                    <img src="/bantrasuamain/public/assets/img/<?= htmlspecialchars($f['image']) ?>"
                        class="h-[300px] mx-auto drop-shadow-xl group-hover:scale-105 transition"
                        alt="<?= htmlspecialchars($f['name']) ?>">

                    <span
                        class="absolute top-4 left-4 px-3 py-1 rounded-full bg-amber-500 text-white text-xs font-bold">
                        Gợi ý
                    </span>
                </div>

                <h4 class="mt-6 text-lg font-semibold text-orange-600">
                    <?= htmlspecialchars($f['name']) ?>
                </h4>

                <p class="mt-2 text-sm text-gray-500 max-w-xs mx-auto line-clamp-2">
                    <?= htmlspecialchars($f['description']) ?>
                </p>

                <div class="mt-3 flex justify-center gap-2 text-xs">
                    <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full font-semibold">Ngon</span>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-semibold">Đề xuất</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">Đáng thử</span>
                </div>

                <p class="mt-3 text-orange-500 font-bold text-lg">
                    <?= number_format($f['price']) ?>đ
                </p>

                <button onclick="openModal(
              <?= (int)$f['id'] ?>,
              '<?= htmlspecialchars($f['name'], ENT_QUOTES) ?>',
              '/bantrasuamain/public/assets/img/<?= htmlspecialchars($f['image'], ENT_QUOTES) ?>'
            )" class="mt-4 inline-flex items-center gap-2 px-6 py-2
                   rounded-full border-2 border-orange-500
                   text-orange-600 font-semibold
                   hover:bg-orange-500 hover:text-white transition">
                    👁 Chi tiết
                </button>

            </div>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<!-- ================= MODAL (SIZE + GIÁ + THÊM GIỎ) ================= -->
<div id="productModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-[#fdeee6] rounded-2xl max-w-4xl w-full p-10 relative grid grid-cols-1 md:grid-cols-2 gap-10">

        <button onclick="closeModal()"
            class="absolute top-4 right-4 text-2xl font-bold text-gray-500 hover:text-red-500">✕</button>

        <!-- IMAGE -->
        <div class="flex justify-center">
            <img id="modalImage" class="h-[420px] object-contain" alt="Product">
        </div>

        <!-- INFO -->
        <div>
            <h3 id="modalName" class="text-2xl font-extrabold mb-6 text-red-600"></h3>

            <p class="font-semibold mb-3">Chọn size:</p>
            <div id="sizeOptions" class="flex flex-wrap gap-4 mb-6"></div>

            <p class="text-xl font-bold text-orange-500 mb-6">
                Giá: <span id="modalPrice">—</span>
            </p>

            <button onclick="addToCartWithAnimation()"
                class="w-full py-3 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold transition">
                🛒 Thêm vào giỏ hàng
            </button>

            <input type="hidden" id="cartProductId">
            <input type="hidden" id="cartSize">
        </div>
    </div>
</div>

<!-- ================= JS (MODAL + SIZE + CART + ANIMATION) ================= -->
<script>
function openModal(id, name, image) {
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalImage').src = image;
    document.getElementById('cartProductId').value = id;

    fetch(`?c=product&a=getSizes&id=${id}`)
        .then(res => res.json())
        .then(sizes => {
            const box = document.getElementById('sizeOptions');
            box.innerHTML = '';

            if (!sizes.length) {
                box.innerHTML = '<span class="text-gray-500">Chưa có size</span>';
                document.getElementById('modalPrice').innerText = '—';
                document.getElementById('cartSize').value = '';
                return;
            }

            sizes.forEach((s, index) => {
                box.innerHTML += `
          <label class="cursor-pointer">
            <input type="radio" name="size"
                   ${index === 0 ? 'checked' : ''}
                   onchange="changePrice(${s.price}, '${s.size}')"
                   class="hidden peer">
            <div class="px-4 py-2 border rounded-lg
                        peer-checked:border-orange-500
                        peer-checked:bg-orange-100
                        hover:border-orange-400 transition">
              Size ${s.size}
            </div>
          </label>
        `;
            });

            changePrice(sizes[0].price, sizes[0].size);
        });

    document.getElementById('productModal').classList.remove('hidden');
    document.getElementById('productModal').classList.add('flex');
}

function changePrice(price, size) {
    document.getElementById('modalPrice').innerText = price.toLocaleString() + 'đ';
    document.getElementById('cartSize').value = size;
}

function closeModal() {
    document.getElementById('productModal').classList.add('hidden');
    document.getElementById('productModal').classList.remove('flex');
}

function addToCartWithAnimation() {
    const productId = document.getElementById('cartProductId').value;
    const size = document.getElementById('cartSize').value;
    const modalImg = document.getElementById('modalImage');

    if (!productId) return alert('Thiếu product id!');
    if (!size) return alert('Vui lòng chọn size!');

    const price = parseInt(document.getElementById('modalPrice').innerText.replace(/\D/g, '')) || 0;

    // ===== Animation bay vào giỏ (nếu có #cartCount thì ok) =====
    try {
        const rect = modalImg.getBoundingClientRect();
        const img = document.createElement('img');
        img.src = modalImg.src;
        img.style.position = 'fixed';
        img.style.left = rect.left + 'px';
        img.style.top = rect.top + 'px';
        img.style.width = rect.width + 'px';
        img.style.height = rect.height + 'px';
        img.style.borderRadius = '12px';
        img.style.zIndex = 9999;
        img.style.transition = 'all 0.8s ease-in-out';
        img.style.transformOrigin = 'center center';
        document.body.appendChild(img);

        // Nếu header bạn có icon giỏ hàng, hãy để nó có id="cartIcon"
        const cartEl = document.getElementById('cartIcon') || document.getElementById('cartCount') || document
            .querySelector('#cartCount');
        if (cartEl) {
            const cartRect = cartEl.getBoundingClientRect();
            const dx = cartRect.left + cartRect.width / 2 - (rect.left + rect.width / 2);
            const dy = cartRect.top + cartRect.height / 2 - (rect.top + rect.height / 2);

            requestAnimationFrame(() => {
                img.style.transform = `translate(${dx}px, ${dy}px) scale(0.2)`;
                img.style.opacity = '0.5';
            });

            img.addEventListener('transitionend', () => img.remove());
        } else {
            // không có icon giỏ, bỏ animation
            img.remove();
        }
    } catch (e) {}

    // ===== AJAX ADD CART =====
    fetch('?c=cart&a=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `product_id=${encodeURIComponent(productId)}&size=${encodeURIComponent(size)}&price=${encodeURIComponent(price)}`
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const cartCount = document.getElementById('cartCount');
                if (cartCount) cartCount.innerText = res.count;
                closeModal();
            } else {
                alert(res.message || 'Thêm vào giỏ hàng thất bại!');
            }
        })
        .catch(() => alert('Lỗi kết nối khi thêm vào giỏ!'));
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php require_once "app/View/layouts/footer.php"; ?>