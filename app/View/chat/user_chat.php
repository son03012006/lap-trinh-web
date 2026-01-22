<!-- CHAT WIDGET -->
<div id="chatWidget"
     class="fixed bottom-5 right-5 w-80 bg-white rounded-2xl shadow-2xl flex flex-col">

  <!-- HEADER -->
  <div class="bg-orange-500 text-white px-4 py-3 rounded-t-2xl flex justify-between items-center">
    <span class="font-bold">💬 Chat với TSN MilkTea</span>
    <button onclick="toggleChat()">✖</button>
  </div>

  <!-- MESSAGES -->
  <div id="chatMessages"
       class="flex-1 p-4 space-y-3 overflow-y-auto text-sm bg-gray-50">

    <!-- ADMIN -->
    <div class="flex">
      <div class="bg-gray-200 px-3 py-2 rounded-xl max-w-[75%]">
        👋 Xin chào! Bạn cần hỗ trợ gì ạ?
      </div>
    </div>

    <!-- USER -->
    <div class="flex justify-end">
      <div class="bg-orange-400 text-white px-3 py-2 rounded-xl max-w-[75%]">
        Mình muốn hỏi về đơn hàng
      </div>
    </div>

  </div>

  <!-- INPUT -->
  <div class="border-t flex items-center px-3 py-2">
    <input id="chatInput"
           type="text"
           placeholder="Nhập tin nhắn..."
           class="flex-1 outline-none text-sm px-2">
    <button onclick="sendMessage()"
            class="text-orange-500 font-bold px-3">➤</button>
  </div>

</div>

<script>
function toggleChat() {
  document.getElementById('chatWidget').classList.toggle('hidden');
}

function sendMessage() {
  const input = document.getElementById('chatInput');
  if (!input.value.trim()) return;

  const msg = document.createElement('div');
  msg.className = 'flex justify-end';
  msg.innerHTML = `
    <div class="bg-orange-400 text-white px-3 py-2 rounded-xl max-w-[75%]">
      ${input.value}
    </div>`;
  document.getElementById('chatMessages').appendChild(msg);

  input.value = '';
  document.getElementById('chatMessages').scrollTop = 99999;
}
</script>
