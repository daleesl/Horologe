<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
    #chatPanel {
        overflow: hidden;
        width: 440px;
        height: 580px;
        bottom: 95px;
        right: 25px;
        display: none;
        z-index: 1050;
        border-radius: 1.2rem;
        box-shadow: 0 0.7rem 1.5rem rgba(0, 0, 0, 0.6);
        background-color: rgba(40, 40, 40, 0.9);
        backdrop-filter: blur(7px);
        flex-direction: column;
        position: fixed;
        font-family: "degular", sans-serif;
        font-size: 15px;
    }

    #chatBox {
        overflow-y: auto;
        flex-grow: 1;
        font-size: 18px;
        line-height: 1.6;
        padding: 1rem;
    }

    #chatBox::-webkit-scrollbar {
        width: 6px;
    }

    #chatBox::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .msg-user {
        max-width: 75%;
        margin-left: auto;
        margin-bottom: 0.9rem;
        background: #2d517d;
        color: #000;
        padding: 0.6rem 0.9rem;
        border-radius: 1rem 1rem 0.3rem 1rem;
        line-height: 1.5;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        word-wrap: break-word;
    }

    .msg-bot {
        max-width: 75%;
        margin-right: auto;
        margin-bottom: 0.9rem;
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        padding: 0.6rem 0.9rem;
        border-radius: 1rem 1rem 1rem 0.3rem;
        line-height: 1.5;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        word-wrap: break-word;
    }

    #chatInput {
        font-size: 15px;
        padding: 10px 14px;
        background: #000;
        border: none;
        color: #fff;
    }

    #chatInput::placeholder {
        color: #fff;
    }

    #chatSend {
        font-size: 14px;
        font-weight: 500;
        border: none;
        background: #4377b9;
        color: #000;
    }

    #chatPanel .border-bottom span {
        font-size: 17px;
        letter-spacing: 0.3px;
        font-weight: 500;
        color: #fff;
    }

    #chatPanel .border-bottom button {
        color: #fff !important;
        font-size: 18px;
    }

    #chatPanel * {
        font-family: "degular", sans-serif !important;
    }
</style>

<button id="chatToggle"
        class="btn btn-dark shadow-lg position-fixed rounded-circle"
        style="bottom:25px; right:25px; width:60px; height:60px; padding:0; z-index:1050;">
    <i class="bi bi-chat-dots-fill fs-4"></i>
</button>


<div id="chatPanel">

    <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary">
        <span>Ask Horologe</span>
        <button id="chatClose" class="btn btn-sm text-secondary-bold">×</button>
    </div>


    <div id="chatBox" class="p-3 small"></div>


    <div class="input-group border-top border-secondary">
        <input type="text" id="chatInput" class="form-control bg-black border-0" placeholder="Ask about our watches...">
        <button id="chatSend" class="btn btn-outline-light text-secondary-bold">Send</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const chatPanel = document.getElementById("chatPanel");
    const chatToggle = document.getElementById("chatToggle");
    const chatClose = document.getElementById("chatClose");
    const chatSend = document.getElementById("chatSend");
    const chatInput = document.getElementById("chatInput");
    const chatBox = document.getElementById("chatBox");


    const navigation = performance.getEntriesByType("navigation")[0];
    if (!navigation || navigation.type === "reload") {
        sessionStorage.removeItem("chatHistory");
        sessionStorage.removeItem("welcomeShown");
        sessionStorage.removeItem("chatScrollTop");
    }

    if (sessionStorage.getItem("chatHistory")) {
        chatBox.innerHTML = sessionStorage.getItem("chatHistory");
    }


    if (sessionStorage.getItem("chatScrollTop")) {
        chatBox.scrollTop = parseInt(sessionStorage.getItem("chatScrollTop"));
    }

    function toggleChat() {
        chatPanel.style.display = (chatPanel.style.display === "flex") ? "none" : "flex";

    }

    chatToggle.onclick = toggleChat;
    chatClose.onclick = toggleChat;


    function saveChatState() {
        sessionStorage.setItem("chatHistory", chatBox.innerHTML);
        sessionStorage.setItem("chatScrollTop", chatBox.scrollTop);
    }

    function addUserMessage(text) {
        chatBox.innerHTML += `<div class="msg-user">${text}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
        saveChatState();
    }

    function addBotMessage(text) {
        chatBox.innerHTML += `<div class="msg-bot">${text}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
        saveChatState();
    }

    if (!sessionStorage.getItem("welcomeShown")) {
        addBotMessage(
        "Hello, welcome to Horologe! I’m your watch assistant. " +
        "You can ask me about our available watch brands, models under each brand, " +
        "or the description of a specific watch model."
        );
        sessionStorage.setItem("welcomeShown", "true");
    }

    function sendChat() {
        const message = chatInput.value.trim();
        if (!message) return;

        addUserMessage(message);
        chatInput.value = "";

        const replyId = "reply_" + Date.now();
        chatBox.innerHTML += `<div id="${replyId}" class="msg-bot">Thinking...</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
        saveChatState();

        fetch("/HOROLOGE/actions/watchAssistant.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "prompt=" + encodeURIComponent(message)
        })
        .then(res => res.json())
        .then(data => {
            const reply = (data.reply || "I'm sorry, I only handle questions about Horologe watches.").replace(/\n/g, "<br>");
            document.getElementById(replyId).innerHTML = reply;
            chatBox.scrollTop = chatBox.scrollHeight;
            saveChatState();
        })
        .catch(() => {
            document.getElementById(replyId).innerHTML = "Unable to contact concierge.";
            saveChatState();
        });
    }

    chatSend.onclick = sendChat;
    chatInput.addEventListener("keydown", e => {
        if (e.key === "Enter") sendChat();
    });

    chatBox.addEventListener("scroll", () => {
        sessionStorage.setItem("chatScrollTop", chatBox.scrollTop);
    });
});
</script>
