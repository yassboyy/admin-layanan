@extends('layouts.dashboard')

@section('title', 'Chat')
@section('page_title', 'Layanan Chat Pelanggan')

@section('content')
    <style>
        .chat-container {
            height: calc(100vh - 280px);
            min-height: 420px;
        }

        .chat-sidebar::-webkit-scrollbar,
        .chat-messages::-webkit-scrollbar {
            width: 5px;
        }

        .chat-sidebar::-webkit-scrollbar-thumb,
        .chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .chat-sidebar::-webkit-scrollbar-track,
        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .customer-item-btn {
            transition: background-color 0.15s ease-in-out;
        }

        .customer-item-btn:hover {
            background-color: #f8fafc;
        }

        .customer-item-btn.is-active {
            background-color: #f1f5f9;
        }

        .dark .customer-item-btn.is-active {
            background-color: #334155;
        }

        .msg-bubble-out {
            background-color: #2563eb;
            color: #ffffff;
        }

        .msg-bubble-in {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        .dark .msg-bubble-in {
            background-color: #1e293b;
            color: #f8fafc;
            border-color: #334155;
        }
    </style>

    <div
        class="chat-container bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col mb-4 shadow-xs transition-colors">

        {{-- ================== TOP ROW (HEADER SEJAJAR 100%) ================== --}}
        <div class="flex flex-row border-b border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800 h-[76px]">
            {{-- Left Header: Judul Chat + Search Input --}}
            <div
                class="w-80 lg:w-96 border-r border-slate-200 dark:border-slate-700 px-4 py-2.5 flex flex-col justify-center gap-1.5 shrink-0">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Chat</h2>

                <div class="relative w-full flex items-center">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="search-pelanggan-input" placeholder="Cari..."
                        style="padding-left: 2.25rem;"
                        class="w-full pr-3 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500 placeholder:text-slate-400">
                </div>
            </div>

            {{-- Right Header: Informasi Pelanggan Aktif --}}
            <div class="flex-grow px-5 py-2.5 flex items-center justify-between">
                @if(isset($selectedPelanggan) && $selectedPelanggan)
                    <div class="flex items-center gap-3 min-w-0">
                        <div
                            class="w-9 h-9 rounded-full bg-black text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate leading-tight">
                                {{ $selectedPelanggan->name }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                {{ $selectedPelanggan->email }}</p>
                        </div>
                    </div>
                @else
                    <div></div>
                @endif
            </div>
        </div>

        {{-- ================== BOTTOM ROW (BODY & FOOTER) ================== --}}
        <div class="flex-grow flex flex-row overflow-hidden min-h-0">

            {{-- Left Column: Daftar Pelanggan --}}
            <div class="w-80 lg:w-96 border-r border-slate-200 dark:border-slate-700 flex flex-col shrink-0 bg-white dark:bg-slate-800 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700/60 chat-sidebar"
                id="chat-list-container">
                @if(isset($pelanggans) && count($pelanggans) > 0)
                    @foreach($pelanggans as $p)
                        @php
                            $isSelected = (isset($selectedPelanggan) && $selectedPelanggan && $selectedPelanggan->id == $p->id);
                            $lastMsg = $p->last_chat ? $p->last_chat->pesan : ('Halo ' . $p->name . '! 👋 Tim Admin kami siap melayani Anda.');
                        @endphp
                        <a href="{{ url('/admin/chat') }}?pelanggan_id={{ $p->id }}"
                            class="customer-item-btn px-4 py-2.5 block {{ $isSelected ? 'is-active' : '' }}"
                            data-name="{{ strtolower($p->name) }}" data-email="{{ strtolower($p->email) }}">
                            <div class="flex items-center gap-3">
                                {{-- Avatar Solid Black with White User Silhouette --}}
                                <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate leading-tight">
                                        {{ $p->name }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                        {{ $lastMsg }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="p-6 text-center text-slate-400 text-xs">
                        Belum ada pelanggan terdaftar.
                    </div>
                @endif
            </div>

            {{-- Right Column: Messages Stream + Input Footer --}}
            <div class="flex-grow flex flex-col h-full bg-white dark:bg-slate-800 overflow-hidden relative"
                id="chat-main-panel">

                {{-- Messages Area --}}
                <div id="admin-chat-messages" class="flex-grow p-4 sm:p-5 overflow-y-auto space-y-3 chat-messages min-h-0">
                    @if(!isset($selectedPelanggan) || !$selectedPelanggan)
                        {{-- State 1: Belum Memilih Pelanggan (Gambar 1) --}}
                        <div class="h-full flex items-center justify-center text-center p-6">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Klik salah satu pelanggan untuk membuka
                                chat.</p>
                        </div>
                    @elseif(isset($messages) && count($messages) > 0)
                        {{-- State 2: Ada Pesan Percakapan (Gambar 2) --}}
                        @php $lastDate = ''; @endphp
                        @foreach($messages as $m)
                            @php
                                $msgDate = \Carbon\Carbon::parse($m->created_at)->format('d M Y');
                                $msgTime = \Carbon\Carbon::parse($m->created_at)->format('H:i');
                                $isMe = ($m->user_id == getUserId());
                            @endphp

                            @if($msgDate !== $lastDate)
                                <div class="flex items-center justify-center my-2">
                                    <span
                                        class="px-3 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                        {{ $msgDate }}
                                    </span>
                                </div>
                                @php $lastDate = $msgDate; @endphp
                            @endif

                            @if($isMe)
                                {{-- Outgoing Message (Admin - Kanan) --}}
                                <div class="flex items-end justify-end gap-2">
                                    <div class="max-w-[75%]">
                                        <div class="msg-bubble-out px-3.5 py-2 rounded-2xl rounded-tr-none space-y-1 shadow-2xs">
                                            <p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">{{ $m->pesan }}</p>
                                        </div>
                                        <div class="flex items-center justify-end gap-1 mt-0.5 pr-1">
                                            <span class="text-[9px] text-slate-400 font-semibold">{{ $msgTime }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Incoming Message (Pelanggan - Kiri) --}}
                                <div class="flex items-start gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center text-[9px] shrink-0 mt-0.5">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </div>
                                    <div class="max-w-[75%]">
                                        <div class="msg-bubble-in px-3.5 py-2 rounded-2xl rounded-tl-none space-y-1">
                                            <span
                                                class="text-[10px] font-bold text-slate-700 dark:text-slate-300 block">{{ $selectedPelanggan->name }}</span>
                                            <p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">{{ $m->pesan }}</p>
                                        </div>
                                        <span class="text-[9px] text-slate-400 font-medium pl-1 mt-0.5 block">{{ $msgTime }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        {{-- State 2: Pesan sapaan awal dari Admin --}}
                        <div class="flex items-end justify-end gap-2">
                            <div class="max-w-[75%]">
                                <div class="msg-bubble-out px-3.5 py-2 rounded-2xl rounded-tr-none space-y-1 shadow-2xs">
                                    <p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">Halo {{ $selectedPelanggan->name }}! 👋&#10;Tim Admin kami siap melayani Anda.</p>
                                </div>
                                <div class="flex items-center justify-end gap-1 mt-0.5 pr-1">
                                    <span class="text-[9px] text-slate-400 font-semibold">{{ date('H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Chat Input Footer (Tombol Kirim Biru Selalu Tersedia di Bawah) --}}
                <div
                    class="p-2.5 sm:p-3 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 shrink-0 z-10">
                    <form id="form-admin-chat" action="{{ url('/admin/chat') }}" method="POST"
                        class="flex items-center gap-2.5">
                        @csrf
                        <input type="hidden" name="pelanggan_id" id="pelanggan_id_input"
                            value="{{ $selectedPelanggan ? $selectedPelanggan->id : '' }}">

                        <input type="text" id="admin-chat-input" name="pesan" {{ $selectedPelanggan ? 'required' : 'disabled' }}
                            placeholder="{{ $selectedPelanggan ? 'Ketik pesan...' : 'Pilih pelanggan terlebih dahulu...' }}"
                            autocomplete="off"
                            class="flex-grow px-3.5 py-2 text-xs sm:text-sm rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 placeholder:text-slate-400 disabled:bg-slate-50 disabled:text-slate-400">

                        <button type="submit" id="btn-send-admin-chat" {{ $selectedPelanggan ? '' : 'disabled' }}
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs sm:text-sm rounded-lg transition-colors inline-flex items-center justify-center gap-1.5 shrink-0 shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>Kirim</span>
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>

    {{-- SCRIPT CHAT ADMIN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var searchInput = document.getElementById('search-pelanggan-input');
            var messagesContainer = document.getElementById('admin-chat-messages');
            var formChat = document.getElementById('form-admin-chat');
            var inputChat = document.getElementById('admin-chat-input');
            var btnSend = document.getElementById('btn-send-admin-chat');
            var pelangganIdEl = document.getElementById('pelanggan_id_input');
            var pelangganId = pelangganIdEl ? pelangganIdEl.value : null;

            var isUserScrolledUp = false;
            var previousMessagesJson = '';

            if (messagesContainer) {
                messagesContainer.addEventListener('scroll', function () {
                    var distance = messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight;
                    isUserScrolledUp = (distance > 60);
                });
                setTimeout(function () {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }, 100);
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    var q = this.value.toLowerCase().trim();
                    document.querySelectorAll('.customer-item-btn').forEach(function (item) {
                        var name = item.getAttribute('data-name') || '';
                        var email = item.getAttribute('data-email') || '';
                        if (!q || name.indexOf(q) !== -1 || email.indexOf(q) !== -1) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            if (formChat && pelangganId) {
                formChat.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var pesan = inputChat.value.trim();
                    if (!pesan) return;

                    btnSend.disabled = true;
                    var csrfToken = document.querySelector('input[name="_token"]').value;

                    fetch('{{ url('/admin/chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            pelanggan_id: pelangganId,
                            pesan: pesan
                        })
                    })
                        .then(function (res) {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.json();
                        })
                        .then(function (data) {
                            btnSend.disabled = false;
                            if (data.success) {
                                inputChat.value = '';
                                isUserScrolledUp = false;
                                fetchAdminMessages(true);
                            } else {
                                formChat.submit();
                            }
                        })
                        .catch(function (err) {
                            btnSend.disabled = false;
                            formChat.submit();
                        });
                });

                function fetchAdminMessages(forceScroll) {
                    if (!pelangganId) return;
                    forceScroll = forceScroll || false;
                    fetch('{{ url('/admin/chat/messages') }}?pelanggan_id=' + pelangganId)
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (data.success && data.messages) {
                                var newJson = JSON.stringify(data.messages);
                                if (newJson !== previousMessagesJson || forceScroll) {
                                    previousMessagesJson = newJson;
                                    renderAdminMessages(data.messages);

                                    if (forceScroll || !isUserScrolledUp) {
                                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                                    }
                                }
                            }
                        })
                        .catch(function (err) {
                            console.error('Error polling admin chat:', err);
                        });
                }

                function renderAdminMessages(messages) {
                    if (!messagesContainer) return;

                    if (!messages || messages.length === 0) {
                        return;
                    }

                    var html = '';
                    var lastDate = '';
                    for (var i = 0; i < messages.length; i++) {
                        var m = messages[i];
                        if (m.date !== lastDate) {
                            html += '<div class="flex items-center justify-center my-2"><span class="px-3 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">' + escapeHtml(m.date) + '</span></div>';
                            lastDate = m.date;
                        }

                        if (m.is_me) {
                            html += '<div class="flex items-end justify-end gap-2"><div class="max-w-[75%]"><div class="msg-bubble-out px-3.5 py-2 rounded-2xl rounded-tr-none space-y-1 shadow-2xs"><p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">' + escapeHtml(m.pesan) + '</p></div><div class="flex items-center justify-end gap-1 mt-0.5 pr-1"><span class="text-[9px] text-slate-400 font-semibold">' + escapeHtml(m.time) + '</span></div></div></div>';
                        } else {
                            html += '<div class="flex items-start gap-2"><div class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center text-[9px] shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div><div class="max-w-[75%]"><div class="msg-bubble-in px-3.5 py-2 rounded-2xl rounded-tl-none space-y-1"><span class="text-[10px] font-bold text-slate-700 dark:text-slate-300 block">' + escapeHtml(m.sender_name) + '</span><p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">' + escapeHtml(m.pesan) + '</p></div><span class="text-[9px] text-slate-400 font-medium pl-1 mt-0.5 block">' + escapeHtml(m.time) + '</span></div></div>';
                        }
                    }

                    messagesContainer.innerHTML = html;
                }

                function escapeHtml(str) {
                    if (!str) return '';
                    var div = document.createElement('div');
                    div.appendChild(document.createTextNode(str));
                    return div.innerHTML;
                }

                fetchAdminMessages(true);
                setInterval(function () {
                    fetchAdminMessages(false);
                }, 3000);
            }
        });
    </script>
@endsection