<!-- ================= FLOATING CHAT WIDGET (POJOK KIRI BAWAH) ================= -->
<div id="floating-chat-root" style="position: fixed; bottom: 24px; left: 24px; z-index: 999999; font-family: inherit;">

    <!-- Floating Chat Window Card -->
    <div id="floating-chat-window"
        style="position: fixed; bottom: 92px; left: 24px; width: 340px; max-width: calc(100vw - 48px); height: 490px; max-height: 80vh; z-index: 999999; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(79, 70, 229, 0.25), 0 0 0 1px rgba(0,0,0,0.06); display: none; flex-direction: column; overflow: hidden; transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); transform-origin: bottom left;">

        <!-- Header -->
        <div
            style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 18px; color: #ffffff; display: flex; align-items: center; justify-content: space-between; border-top-left-radius: 24px; border-top-right-radius: 24px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div
                    style="width: 42px; height: 42px; border-radius: 14px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.25);">
                    <svg style="width: 22px; height: 22px; fill: #ffffff;" viewBox="0 0 24 24">
                        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
                    </svg>
                </div>
                <div>
                    <h3
                        style="margin: 0; font-size: 14px; font-weight: 800; color: #ffffff; line-height: 1.2; letter-spacing: -0.01em;">
                        Admin Support</h3>
                </div>
            </div>

            <button type="button" id="btn-close-floating-chat"
                style="background: rgba(255,255,255,0.15); border: none; color: #ffffff; cursor: pointer; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                onmouseover="this.style.background='rgba(255,255,255,0.3)';"
                onmouseout="this.style.background='rgba(255,255,255,0.15)';" title="Tutup Chat">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @if(!session()->has('user'))
            <!-- ================= GUEST STATE (NOT LOGGED IN) ================= -->
            <div
                style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex-grow: 1; padding: 32px 24px; text-align: center; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">

                <div
                    style="width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%); border: 1px solid rgba(79, 70, 229, 0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 18px; box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.1);">
                    <svg style="width: 28px; height: 28px; fill: #4f46e5;" viewBox="0 0 24 24">
                        <path
                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
                    </svg>
                </div>

                <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;">Butuh
                    Konsultasi?</h4>

                <p style="margin: 8px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.55; max-width: 230px;">
                    Silakan masuk menggunakan akun Anda untuk mulai mengobrol langsung dengan Admin.
                </p>

                <a href="{{ url('/login') }}"
                    style="margin-top: 24px; width: 100%; max-width: 230px; padding: 13px 0; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; font-weight: 800; font-size: 13px; border-radius: 14px; text-decoration: none; display: block; text-align: center; box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.4); transition: transform 0.2s, box-shadow 0.2s;"
                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 12px 24px -4px rgba(79, 70, 229, 0.5)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px -4px rgba(79, 70, 229, 0.4)';">
                    Login Sekarang
                </a>
            </div>
        @elseif(session('user.role') === 'admin')
            <!-- ================= ADMIN LOGGED IN STATE ================= -->
            <div
                style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex-grow: 1; padding: 32px 24px; text-align: center; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
                <div
                    style="width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%); border: 1px solid rgba(79, 70, 229, 0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; font-size: 26px; box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.1);">
                    👑
                </div>

                <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">Panel Admin Support</h4>
                <p style="margin: 8px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.55; max-width: 240px;">
                    Anda login sebagai <strong>{{ session('user.name') }}</strong>. Kelola dan balas percakapan seluruh
                    pelanggan dari Dashboard Admin.
                </p>

                <a href="{{ url('/admin/chat') }}"
                    style="margin-top: 24px; width: 100%; max-width: 230px; padding: 13px 0; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; font-weight: 800; font-size: 13px; border-radius: 14px; text-decoration: none; display: block; text-align: center; box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.4); transition: transform 0.2s, box-shadow 0.2s;"
                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 12px 24px -4px rgba(79, 70, 229, 0.5)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px -4px rgba(79, 70, 229, 0.4)';">
                    Buka Chat Admin
                </a>
            </div>
        @else
            <!-- ================= LOGGED IN PELANGGAN CHAT STREAM ================= -->
            <div
                style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden; background: linear-gradient(180deg, #faf5ff 0%, #f1f5f9 100%);">
                <!-- Messages Stream Area -->
                <div id="floating-chat-messages"
                    style="flex-grow: 1; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; font-size: 12px;">
                </div>

                <!-- Input Footer -->
                <div
                    style="padding: 12px 14px; background: rgba(255,255,255,0.92); backdrop-filter: blur(8px); border-top: 1px solid #f1f5f9; flex-shrink: 0;">
                    <form id="form-floating-chat" style="display: flex; align-items: center; gap: 8px; margin: 0;">
                        @csrf
                        <input type="text" id="floating-chat-input" placeholder="Ketik pesan untuk Admin..."
                            autocomplete="off" required
                            style="flex-grow: 1; padding: 10px 14px; font-size: 12px; border-radius: 14px; border: 1px solid #e2e8f0; background: #f8fafc; color: #0f172a; outline: none; transition: border-color 0.2s, background 0.2s;"
                            onfocus="this.style.borderColor='#4f46e5'; this.style.background='#ffffff';"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                        <button type="submit" id="btn-floating-send"
                            style="width: 38px; height: 38px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; border: none; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35); flex-shrink: 0;"
                            onmouseover="this.style.transform='translateY(-1px)';"
                            onmouseout="this.style.transform='translateY(0)';">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>

    <!-- Floating Trigger Circular Button (Bottom Left) -->
    <button type="button" id="btn-toggle-floating-chat" aria-label="Buka Chat"
        style="width: 58px; height: 58px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; box-shadow: 0 12px 28px -4px rgba(79, 70, 229, 0.45); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; outline: none;"
        onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='0 16px 32px -4px rgba(79, 70, 229, 0.55)';"
        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 12px 28px -4px rgba(79, 70, 229, 0.45)';">
        <!-- Dual Chat Speech Bubbles Icon -->
        <svg style="width: 26px; height: 26px; fill: #ffffff;" viewBox="0 0 24 24">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
        </svg>
    </button>

</div>

<!-- SCRIPT FLOATING CHAT LOGIC -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnToggle = document.getElementById('btn-toggle-floating-chat');
        const btnClose = document.getElementById('btn-close-floating-chat');
        const chatWindow = document.getElementById('floating-chat-window');
        const messagesContainer = document.getElementById('floating-chat-messages');
        const formChat = document.getElementById('form-floating-chat');
        const inputChat = document.getElementById('floating-chat-input');
        const btnSend = document.getElementById('btn-floating-send');

        let isOpen = false;
        let isUserScrolledUp = false;
        let previousMessagesJson = '';

        if (messagesContainer) {
            messagesContainer.addEventListener('scroll', function () {
                const distance = messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight;
                isUserScrolledUp = (distance > 60);
            });
        }

        function toggleWindow() {
            isOpen = !isOpen;
            if (isOpen) {
                chatWindow.style.display = 'flex';
                setTimeout(() => {
                    chatWindow.style.opacity = '1';
                    chatWindow.style.transform = 'scale(1)';
                }, 10);

                if (messagesContainer) {
                    fetchMessages(true);
                }
            } else {
                chatWindow.style.opacity = '0';
                chatWindow.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    chatWindow.style.display = 'none';
                }, 200);
            }
        }

        if (btnToggle) btnToggle.addEventListener('click', toggleWindow);
        if (btnClose) btnClose.addEventListener('click', toggleWindow);

        @if(session()->has('user') && session('user.role') === 'pelanggan')
            const currentUserName = @json(session('user.name', 'Pelanggan'));

            function fetchMessages(forceScroll = false) {
                if (!messagesContainer) return;
                fetch('{{ url('/pelanggan/chat/messages') }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.messages) {
                            const newJson = JSON.stringify(data.messages);
                            if (newJson !== previousMessagesJson || forceScroll) {
                                previousMessagesJson = newJson;
                                renderMessages(data.messages);

                                if (forceScroll || !isUserScrolledUp) {
                                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                                }
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching chat messages:', err));
            }

            function renderMessages(messages) {
                if (!messagesContainer) return;

                let html = '';

                if (messages && messages.length > 0) {
                    messages.forEach(m => {
                        if (m.is_me) {
                            html += `
                                <div style="display: flex; align-items: flex-end; justify-content: flex-end; gap: 8px;">
                                    <div style="max-width: 82%;">
                                        <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; padding: 10px 14px; border-radius: 16px; border-bottom-right-radius: 4px; box-shadow: 0 4px 12px rgba(79,70,229,0.15);">
                                            <p style="margin: 0; font-size: 12px; line-height: 1.45; white-space: pre-wrap;">${escapeHtml(m.pesan)}</p>
                                        </div>
                                        <span style="font-size: 9px; color: #94a3b8; display: block; text-align: right; margin-top: 3px; font-weight: 500;">${m.time}</span>
                                    </div>
                                </div>`;
                        } else {
                            html += `
                                <div style="display: flex; align-items: flex-end; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #fff; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 10px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(79,70,229,0.2);">
                                        AD
                                    </div>
                                    <div style="max-width: 82%;">
                                        <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; border-bottom-left-radius: 4px; padding: 10px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                            <span style="font-size: 10px; font-weight: 700; color: #4f46e5; display: block; margin-bottom: 2px;">${escapeHtml(m.sender_name)}</span>
                                            <p style="margin: 0; font-size: 12px; color: #1e293b; line-height: 1.45; white-space: pre-wrap;">${escapeHtml(m.pesan)}</p>
                                        </div>
                                        <span style="font-size: 9px; color: #94a3b8; display: block; margin-top: 3px; font-weight: 500;">${m.time}</span>
                                    </div>
                                </div>`;
                        }
                    });
                } else {
                    html = `
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; min-height: 200px; text-align: center; color: #94a3b8; padding: 24px 16px;">
                            <div style="width: 44px; height: 44px; border-radius: 14px; background: #eef2ff; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                <svg style="width: 22px; height: 22px; fill: #4f46e5;" viewBox="0 0 24 24">
                                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
                                </svg>
                            </div>
                            <p style="margin: 0; font-size: 12px; font-weight: 600; color: #64748b;">Belum ada percakapan</p>
                            <span style="font-size: 11px; margin-top: 4px;">Kirim pesan Anda untuk mulai mengobrol</span>
                        </div>`;
                }

                messagesContainer.innerHTML = html;
            }

            if (formChat) {
                formChat.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const pesan = inputChat.value.trim();
                    if (!pesan) return;

                    btnSend.disabled = true;

                    fetch('{{ url('/pelanggan/chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ pesan: pesan })
                    })
                        .then(res => res.json())
                        .then(data => {
                            btnSend.disabled = false;
                            if (data.success) {
                                inputChat.value = '';
                                isUserScrolledUp = false;
                                fetchMessages(true);
                            }
                        })
                        .catch(err => {
                            btnSend.disabled = false;
                            console.error('Error sending message:', err);
                        });
                });
            }

            setInterval(() => {
                if (isOpen && messagesContainer) {
                    fetchMessages(false);
                }
            }, 3000);
        @endif

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>"']/g, function (m) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                })[m];
            });
        }
    });
</script>