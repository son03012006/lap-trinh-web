<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'app/Config/database.php';

/* ===== BẮT BUỘC ĐĂNG NHẬP ===== */
if (!isset($_SESSION['user'])) {
    header('Location: ?c=auth&a=login');
    exit;
}

global $dbh;
$user   = $_SESSION['user'];
$userId = $user['id'];

/* ===== LẤY GIỎ HÀNG ===== */
$stmt = $dbh->prepare("
        SELECT c.id, c.product_id, c.size, c.price, c.qty, c.note, p.name, p.image
        FROM carts c
        JOIN products p ON p.id = c.product_id
        WHERE c.user_id = :uid
    ");
$stmt->execute([':uid' => $userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cartItems) {
    header('Location: ?c=product');
    exit;
}

/* ===== TÍNH TIỀN ===== */
$shippingFee = 15000;
$subTotal = 0;
foreach ($cartItems as $i) {
    $subTotal += $i['price'] * $i['qty'];
}
$total = $subTotal + $shippingFee;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>TSN MilkTea | Thanh toán</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    /* Tùy chỉnh thanh cuộn cho đẹp */
    #addressSuggest::-webkit-scrollbar {
        width: 6px;
    }

    #addressSuggest::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
    </style>
</head>

<body class="bg-[#fff8f0] min-h-screen font-sans">

    <div class="max-w-7xl mx-auto px-6 py-10">

        <a href="?c=product&cat=all" class="text-orange-500 font-bold inline-block mb-6 hover:underline">
            ← Tiếp tục mua sắm
        </a>

        <h2 class="text-3xl font-extrabold mb-8 text-gray-800">🛒 Xác nhận thanh toán</h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xl font-bold mb-5 flex items-center gap-2">
                        📦 Thông tin giao hàng
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Khách hàng</p>
                            <p class="font-bold text-gray-800"><?= htmlspecialchars($user['fullname']) ?></p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Số điện thoại</p>
                            <p class="font-bold text-gray-800"><?= htmlspecialchars($user['phone']) ?></p>
                        </div>
                        <div class="md:col-span-2 p-3 bg-gray-50 rounded-xl">
                            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Email xác nhận</p>
                            <p class="font-bold text-gray-800"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-semibold mb-2 text-gray-700">
                            Địa chỉ nhận hàng <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <input id="address" type="text" autocomplete="off"
                                placeholder="Nhập số nhà, tên đường, phường/xã..."
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none transition shadow-sm">

                            <div id="addressSuggest"
                                class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-50 hidden max-h-60 overflow-y-auto">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 ml-1">Gợi ý địa chỉ từ OpenStreetMap</p>
                    </div>
                </div>


                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xl font-bold mb-5 flex items-center gap-2">
                        🧋 Chi tiết đơn hàng
                    </h3>

                    <div class="space-y-6">
                        <?php foreach ($cartItems as $i): ?>
                        <div class="flex items-start gap-4 border-b border-gray-100 last:border-b-0 pb-6 last:pb-0">
                            <img src="/bantrasuamain/public/assets/img/<?= $i['image'] ?>"
                                class="w-20 h-20 rounded-xl object-cover shadow-sm border border-gray-100">

                            <div class="flex-1">
                                <p class="font-bold text-lg text-gray-800"><?= htmlspecialchars($i['name']) ?></p>
                                <div class="flex flex-wrap gap-2 text-sm text-gray-500 mt-1">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-semibold">Size
                                        <?= htmlspecialchars($i['size']) ?></span>
                                    <span>x<?= $i['qty'] ?></span>
                                </div>

                                <?php if (!empty($i['note'])): ?>
                                <p
                                    class="mt-2 text-sm italic text-gray-500 bg-orange-50 p-2 rounded-lg inline-block border border-orange-100">
                                    📝 <?= htmlspecialchars($i['note']) ?>
                                </p>
                                <?php endif; ?>
                            </div>

                            <div class="text-right">
                                <p class="text-orange-600 font-bold text-lg">
                                    <?= number_format($i['price'] * $i['qty']) ?>đ
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-6 pt-4 border-t border-dashed text-center">

                    </div>
                </div>

            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 h-fit sticky top-6 space-y-5">

                <h3 class="text-xl font-bold border-b pb-4">💳 Tổng thanh toán</h3>

                <div class="space-y-2 text-gray-600">
                    <div class="flex justify-between">
                        <span>Tạm tính</span>
                        <span class="font-medium"><?= number_format($subTotal) ?>đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí vận chuyển</span>
                        <span class="font-medium"><?= number_format($shippingFee) ?>đ</span>
                    </div>
                </div>

                <div class="border-t pt-4 flex justify-between items-center">
                    <span class="text-gray-800 font-bold">Tổng cộng</span>
                    <span class="text-2xl font-extrabold text-orange-600"><?= number_format($total) ?>đ</span>
                </div>

                <div class="pt-2">
                    <p class="font-semibold mb-3 text-sm uppercase text-gray-500">Hình thức thanh toán</p>

                    <div class="space-y-3">
                        <label
                            class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-200 transition group has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                            <input type="radio" name="payMethod" value="cod" checked class="accent-orange-500 w-5 h-5">
                            <span class="font-medium text-gray-700 group-hover:text-orange-700">Thanh toán khi nhận hàng
                                (COD)</span>
                        </label>

                        <label
                            class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition group has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="payMethod" value="bank_fake" class="accent-blue-500 w-5 h-5">
                            <span class="font-medium text-gray-700 group-hover:text-blue-700">Chuyển khoản (Demo/Giả
                                lập)</span>
                        </label>
                    </div>
                </div>

                <div id="bankFakeBox" class="hidden p-5 bg-blue-50 rounded-xl border border-blue-200 animate-fade-in">
                    <h3 class="text-sm font-bold mb-3 text-blue-800 flex items-center gap-2">
                        🏦 Thông tin chuyển khoản
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-blue-500 font-semibold mb-1">Ngân hàng</p>
                            <select
                                class="w-full border rounded-lg px-3 py-2 text-gray-700 focus:outline-none focus:border-blue-500">
                                <option>MB Bank</option>
                                <option>Vietcombank</option>
                            </select>
                        </div>
                        <div>
                            <p class="text-xs text-blue-500 font-semibold mb-1">Số tài khoản</p>
                            <div class="flex items-center bg-white border rounded-lg overflow-hidden">
                                <input type="text" value="0935193460" readonly
                                    class="w-full px-3 py-2 text-gray-700 outline-none">
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-blue-500 font-semibold mb-1">Số tiền</p>
                            <input type="text" value="<?= number_format($total) ?> VND" readonly
                                class="w-full px-3 py-2 border rounded-lg bg-white text-red-600 font-bold">
                        </div>

                        <button id="fakeBankPayBtn"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition mt-2">
                            ✅ Tôi đã chuyển khoản
                        </button>
                    </div>
                </div>

                <div id="qrBox"
                    class="hidden p-5 bg-orange-50 rounded-xl border border-orange-200 text-center animate-fade-in">
                    <p class="font-bold text-orange-700 mb-2">📷 Quét mã để thanh toán</p>
                    <div class="bg-white p-2 rounded-lg border inline-block mb-3">
                        <img id="qrImage" class="w-40 h-40 object-contain" alt="QR thanh toán giả lập">
                    </div>
                    <button id="openFakeBankBtn"
                        class="w-full py-2 bg-white border border-orange-300 text-orange-600 font-bold rounded-lg hover:bg-orange-100 text-sm transition">
                        Mở form nhập tay
                    </button>
                </div>

                <button id="payBtn"
                    class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white text-lg font-extrabold rounded-xl shadow-lg shadow-orange-200 transition transform hover:-translate-y-1">
                    🧾 Hoàn tất đặt hàng
                </button>

                <p class="text-center text-xs text-gray-400">
                    Nhấn "Hoàn tất" đồng nghĩa bạn đồng ý với điều khoản dịch vụ.
                </p>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- KHAI BÁO BIẾN ---
        const payBtn = document.getElementById('payBtn');
        const fakeBtn = document.getElementById('fakeBankPayBtn');
        const bankBox = document.getElementById('bankFakeBox');
        const qrBox = document.getElementById('qrBox');
        const qrImage = document.getElementById('qrImage');
        const openFake = document.getElementById('openFakeBankBtn');
        const addressInput = document.getElementById('address');
        const suggestBox = document.getElementById('addressSuggest');
        const radioMethods = document.querySelectorAll('input[name="payMethod"]');

        // --- 1. LOGIC GỢI Ý ĐỊA CHỈ (Đã sửa lỗi treo Loading) ---
        let debounceTimer = null;

        if (addressInput && suggestBox) {
            addressInput.addEventListener('input', () => {
                const q = addressInput.value.trim();
                clearTimeout(debounceTimer); // Reset timer

                // Nếu xóa hết hoặc quá ngắn -> Ẩn
                if (q.length < 3) {
                    suggestBox.classList.add('hidden');
                    suggestBox.innerHTML = '';
                    return;
                }

                // Chờ 500ms sau khi ngừng gõ mới gọi API (giảm số lần gọi để tránh bị chặn)
                debounceTimer = setTimeout(() => {
                    // Hiện trạng thái đang tải
                    suggestBox.innerHTML = `
                        <div class="p-4 text-center text-sm text-gray-400 flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang tìm địa chỉ...
                        </div>`;
                    suggestBox.classList.remove('hidden');

                    // GỌI API VỚI XỬ LÝ LỖI KỸ HƠN
                    // Gọi đến file PHP trung gian vừa tạo
                    fetch(`api_address.php?q=${encodeURIComponent(q)}`)
                        .then(res => {
                            if (!res.ok) {
                                throw new Error('Lỗi từ máy chủ bản đồ (403/429)');
                            }
                            return res.json();
                        })
                        .then(data => {
                            suggestBox.innerHTML = ''; // Xóa loading

                            if (!data || data.length === 0) {
                                suggestBox.innerHTML =
                                    '<div class="p-3 text-sm text-gray-500 text-center">Không tìm thấy địa chỉ này.</div>';
                                return;
                            }

                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.className =
                                    'px-4 py-3 hover:bg-orange-50 cursor-pointer text-sm border-b border-gray-100 last:border-b-0 flex items-start gap-3 transition';

                                // Tạo nội dung gợi ý
                                div.innerHTML = `
                                    <span class="text-orange-500 text-xl mt-0.5">📍</span>
                                    <div>
                                        <p class="font-medium text-gray-800">${item.display_name.split(',')[0]}</p>
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">${item.display_name}</p>
                                    </div>
                                `;

                                div.onclick = () => {
                                    addressInput.value = item.display_name;
                                    suggestBox.classList.add('hidden');
                                };
                                suggestBox.appendChild(div);
                            });
                        })
                        .catch(err => {
                            console.error("Lỗi API:", err);
                            // Hiển thị lỗi ra màn hình để biết tại sao
                            suggestBox.innerHTML = `
                                <div class="p-3 text-sm text-red-500 text-center">
                                    Không thể tải gợi ý. <br> 
                                    <span class="text-xs text-gray-400">(Vui lòng tự nhập địa chỉ)</span>
                                </div>
                            `;
                        });
                }, 500); // Tăng thời gian chờ lên 500ms
            });

            // Click ra ngoài thì ẩn
            document.addEventListener('click', (e) => {
                if (!addressInput.contains(e.target) && !suggestBox.contains(e.target)) {
                    suggestBox.classList.add('hidden');
                }
            });
        }
        // --- 2. LOGIC THANH TOÁN ---

        // Xử lý đổi phương thức thanh toán
        radioMethods.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'bank_fake' && radio.checked) {
                    payBtn.classList.add('hidden'); // Ẩn nút đặt hàng chính
                    bankBox.classList.add('hidden'); // Mặc định ẩn form nhập

                    // Tạo QR code
                    const fakeUrl = location.origin + '/bantrasuamain/?fake_bank=1';
                    qrImage.src =
                        'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' +
                        encodeURIComponent(fakeUrl);
                    qrBox.classList.remove('hidden'); // Hiện QR trước
                } else {
                    payBtn.classList.remove('hidden'); // Hiện nút đặt hàng COD
                    qrBox.classList.add('hidden');
                    bankBox.classList.add('hidden');
                }
            });
        });

        // Nút "Mở form nhập tay" trong box QR
        if (openFake) {
            openFake.addEventListener('click', () => {
                qrBox.classList.add('hidden');
                bankBox.classList.remove('hidden');
            });
        }

        // --- 3. XỬ LÝ GỬI ĐƠN HÀNG ---

        // Hàm chung gửi Request
        function checkoutRequest(address, method) {
            // Hiệu ứng nút loading (tùy chọn)
            const activeBtn = method === 'cod' ? payBtn : fakeBtn;
            const originalText = activeBtn.innerText;
            activeBtn.innerText = 'Đang xử lý...';
            activeBtn.disabled = true;

            fetch('?c=cart&a=checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `address=${encodeURIComponent(address)}&method=${method}`
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        location.href = '?c=order&a=success&id=' + res.order_id;
                    } else {
                        alert(res.message || 'Có lỗi xảy ra!');
                        activeBtn.innerText = originalText;
                        activeBtn.disabled = false;
                    }
                })
                .catch(err => {
                    alert('Lỗi kết nối: ' + err);
                    activeBtn.innerText = originalText;
                    activeBtn.disabled = false;
                });
        }

        // Nút COD
        if (payBtn) {
            payBtn.addEventListener('click', () => {
                const addr = addressInput.value.trim();
                if (!addr) {
                    alert('Vui lòng nhập địa chỉ nhận hàng!');
                    addressInput.focus();
                    addressInput.classList.add('ring-2', 'ring-red-500'); // Highlight lỗi
                    return;
                }
                addressInput.classList.remove('ring-2', 'ring-red-500');
                checkoutRequest(addr, 'cod');
            });
        }

        // Nút Chuyển khoản
        if (fakeBtn) {
            fakeBtn.addEventListener('click', () => {
                const addr = addressInput.value.trim();
                if (!addr) {
                    alert('Vui lòng nhập địa chỉ trước khi xác nhận chuyển khoản!');
                    // Quay lại tab COD để nhập địa chỉ hoặc focus input
                    addressInput.focus();
                    return;
                }

                if (!confirm('Bạn xác nhận đã chuyển khoản thành công theo thông tin trên?')) return;

                checkoutRequest(addr, 'bank_fake');
            });
        }
    });
    </script>
</body>

</html>