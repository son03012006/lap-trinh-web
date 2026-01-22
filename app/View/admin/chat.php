<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header('Location: ?c=admin&a=login');
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hỗ trợ khách hàng | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Nunito', sans-serif;
    }

    /* Thanh cuộn tin nhắn */
    .chat-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .chat-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .chat-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    </style>
</head>

<body class="bg-gray-100 h-screen flex overflow-hidden">

    <?php require 'app/View/layouts/layoutsadmin/sidebaradmin.php'; ?>

    <main class="ml-72 flex-1 flex flex-col h-full">

        <div class="bg-white p-4 shadow-sm border-b flex justify-between items-center z-10">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                💬 Hỗ trợ trực tuyến
            </h1>
        </div>

        <div class="flex flex-1 overflow-hidden">

            <div class="w-80 bg-white border-r flex flex-col">
                <div class="p-4 bg-gray-50 border-b font-bold text-gray-600">
                    Danh sách khách hàng
                </div>
                <div class="overflow-y-auto flex-1">
                    <?php if (empty($users)): ?>
                    <p class="p-4 text-gray-400 text-center text-sm">Chưa có tin nhắn nào</p>
                    <?php endif; ?>

                    <?php foreach ($users as $u): ?>
                    <div onclick="loadChat(<?= $u['id'] ?>, '<?= htmlspecialchars($u['fullname']) ?>')"
                        class="user-item p-4 border-b cursor-pointer hover:bg-blue-50 transition relative group"
                        data-id="<?= $u['id'] ?>">
                        <div class="font-bold text-gray-800"><?= htmlspecialchars($u['fullname']) ?></div>
                        <div class="text-xs text-gray-500 truncate"><?= htmlspecialchars($u['email']) ?></div>
                        <div class="text-[10px] text-gray-400 mt-1">
                            <?= date('H:i d/m', strtotime($u['last_msg_time'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex-1 flex flex-col bg-gray-50 relative">

                <div id="chatHeader" class="p-3 bg-white border-b shadow-sm text-center font-bold text-blue-600 hidden">
                    Đang chat với: <span id="currentUserName">...</span>
                </div>

                <div id="welcomeScreen" class="flex-1 flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-16 h-16 mb-2 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                        <path
                            d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                    </svg>
                    <p>Chọn một khách hàng để bắt đầu hỗ trợ</p>
                </div>

                <div id="messagesArea" class="flex-1 overflow-y-auto p-4 space-y-3 chat-scroll hidden">
                </div>

                <div id="inputArea" class="p-4 bg-white border-t hidden">
                    <form id="chatForm" class="flex gap-2" onsubmit="sendReply(event)">
                        <input type="text" id="msgInput"
                            class="flex-1 border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-200 transition"
                            placeholder="Nhập câu trả lời..." autocomplete="off">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full font-bold shadow-md transition transform active:scale-95 flex items-center gap-1">
                            <span>Gửi</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9-2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <script>
    let currentUserId = null;
    let chatInterval = null;

    // 1. Hàm chọn user để chat
    function loadChat(userId, userName) {
        currentUserId = userId;
        document.getElementById('currentUserName').innerText = userName;

        // UI Switch
        document.getElementById('welcomeScreen').classList.add('hidden');
        document.getElementById('chatHeader').classList.remove('hidden');
        document.getElementById('messagesArea').classList.remove('hidden');
        document.getElementById('inputArea').classList.remove('hidden');

        // Highlight user đang chọn
        document.querySelectorAll('.user-item').forEach(el => el.classList.remove('bg-blue-100', 'border-l-4',
            'border-blue-600'));
        const activeUser = document.querySelector(`.user-item[data-id='${userId}']`);
        if (activeUser) activeUser.classList.add('bg-blue-100', 'border-l-4', 'border-blue-600');

        fetchMessages();

        // Auto refresh tin nhắn mỗi 3 giây
        if (chatInterval) clearInterval(chatInterval);
        chatInterval = setInterval(fetchMessages, 3000);
    }

    // 2. Lấy tin nhắn từ Server
    function fetchMessages() {
        if (!currentUserId) return;

        fetch(`?c=admin&a=getChatHistory&user_id=${currentUserId}`)
            .then(res => res.json())
            .then(data => {
                renderMessages(data);
            });
    }

    // 3. Hiển thị tin nhắn ra màn hình
    function renderMessages(messages) {
        const container = document.getElementById('messagesArea');
        // Lưu vị trí scroll hiện tại (để tránh bị nhảy lung tung nếu user đang đọc tin cũ)
        const isAtBottom = container.scrollHeight - container.scrollTop === container.clientHeight;

        let html = '';
        messages.forEach(msg => {
            const isAdmin = (msg.sender === 'admin');

            html += `
                    <div class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-[70%] px-4 py-2 rounded-2xl text-sm shadow-sm 
                                    ${isAdmin 
                                        ? 'bg-blue-600 text-white rounded-br-none' 
                                        : 'bg-white text-gray-800 border border-gray-200 rounded-bl-none'}">
                            <div class="break-words">${msg.message}</div>
                            <div class="text-[10px] mt-1 opacity-70 ${isAdmin ? 'text-blue-100 text-right' : 'text-gray-400'}">
                                ${new Date(msg.created_at).toLocaleTimeString('vi-VN', {hour:'2-digit', minute:'2-digit'})}
                            </div>
                        </div>
                    </div>
                `;
        });

        // Chỉ render lại nếu nội dung thay đổi để tránh nháy (đơn giản hoá bằng cách gán luôn innerHTML)
        if (container.innerHTML !== html) {
            container.innerHTML = html;
            // Scroll xuống dưới cùng khi mới load xong
            container.scrollTop = container.scrollHeight;
        }
    }

    // 4. Gửi tin nhắn trả lời
    function sendReply(e) {
        e.preventDefault();
        const input = document.getElementById('msgInput');
        const message = input.value.trim();

        if (!message || !currentUserId) return;

        const formData = new FormData();
        formData.append('user_id', currentUserId);
        formData.append('message', message);

        // Gửi đi
        fetch('?c=admin&a=sendAdminReply', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    input.value = ''; // Xóa ô nhập
                    fetchMessages(); // Load lại tin nhắn ngay lập tức
                }
            });
    }
    </script>

</body>

</html>