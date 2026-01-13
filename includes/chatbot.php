<!-- CHATBOT BUTTON -->
<button
    class="btn btn-dark rounded-circle shadow-lg position-fixed"
    style="bottom: 25px; right: 25px; width: 60px; height: 60px; z-index: 1050;"
    data-bs-toggle="modal"
    data-bs-target="#horologeChatbot">

    <i class="bi bi-chat-dots-fill fs-4"></i>
</button>

<!-- CHATBOT MODAL -->
<div class="modal fade" id="horologeChatbot" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-black text-white border border-secondary">

            <!-- HEADER -->
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-semibold">
                    Horologe Concierge
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <!-- CHAT AREA -->
                <div id="chatBox"
                     class="mb-3 p-3 rounded"
                     style="
                        height: 320px;
                        overflow-y: auto;
                        background-color: #111;
                        border: 1px solid #333;
                     ">
                </div>

                <!-- INPUT -->
                <div class="input-group">
                    <input
                        type="text"
                        id="chatInput"
                        class="form-control bg-dark text-white border-secondary"
                        placeholder="Ask about our watches..."
                        onkeydown="if(event.key==='Enter') sendChat()">

                    <button class="btn btn-outline-light" onclick="sendChat()">
                        Send
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- INLINE JAVASCRIPT -->
<script>
function sendChat() {
    const input = document.getElementById("chatInput");
    const chatBox = document.getElementById("chatBox");
    const message = input.value.trim();

    if (!message) return;

    // User message
    chatBox.innerHTML += `
        <div class="mb-2 text-end">
            <span class="badge bg-warning text-dark">You</span>
            <div class="mt-1">${message}</div>
        </div>
    `;

    input.value = "";

    // Loading indicator
    const replyId = "reply_" + Date.now();
    chatBox.innerHTML += `
        <div id="${replyId}" class="mb-2">
            <span class="badge bg-secondary">Horologe</span>
            <div class="mt-1 text-muted">Thinking...</div>
        </div>
    `;

    chatBox.scrollTop = chatBox.scrollHeight;

    // Send to backend
    fetch('../actions/watchassistant.php', {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "prompt=" + encodeURIComponent(message)
    })
    .then(res => res.text())
    .then(reply => {
        document.getElementById(replyId).innerHTML = `
            <span class="badge bg-light text-dark">Horologe</span>
            <div class="mt-1">${reply}</div>
        `;
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(() => {
        document.getElementById(replyId).innerHTML = `
            <span class="badge bg-danger">Error</span>
            <div class="mt-1">Unable to contact concierge.</div>
        `;
    });
}
</script>
