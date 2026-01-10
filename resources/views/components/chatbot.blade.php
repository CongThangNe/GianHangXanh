<!-- Simple Chatbot Widget (Bottom Right) -->
<div id="simple-chatbot" class="simple-chatbot" aria-live="polite">
    <!-- Floating button (Messenger-like) -->
    <button id="chatbot-toggle" type="button" class="chatbot-toggle" aria-label="Mở chatbot">
        <i class="bi bi-messenger"></i>
    </button>

    <!-- Chat window -->
    <div id="chatbot-window" class="chatbot-window" role="dialog" aria-label="Chatbot hỗ trợ" aria-hidden="true">
        <div class="chatbot-header">
            <div class="chatbot-title">
                <span class="chatbot-dot"></span>
                <span>Chatbot hỗ trợ</span>
            </div>
            <button id="chatbot-close" type="button" class="chatbot-close" aria-label="Đóng chatbot">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div id="chatbot-messages" class="chatbot-messages"></div>

        <form id="chatbot-form" class="chatbot-form" autocomplete="off">
            <input id="chatbot-input" type="text" class="chatbot-input" placeholder="Nhập câu hỏi..." />
            <button id="chatbot-send" type="submit" class="chatbot-send" aria-label="Gửi">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </div>
</div>
