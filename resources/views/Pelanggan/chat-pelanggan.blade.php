@extends('layouts.dashboard')

@section('title', 'Chat')
@section('page_title', 'Layanan Chat')

@section('content')
    <style>
        .plg-chat-container {
            height: calc(100vh - 280px);
            min-height: 420px;
        }

        .plg-chat-messages::-webkit-scrollbar {
            width: 5px;
        }

        .plg-chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .plg-chat-messages::-webkit-scrollbar-track {
            background: transparent;
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

    {{-- Main Chat Container --}}
    <div
        class="plg-chat-container bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden flex flex-col mb-4 transition-colors">

        {{-- Chat Header --}}
        <div
            class="px-5 py-3.5 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between shrink-0 h-[76px] z-10">
            <div class="flex items-center gap-3.5">
                {{-- Avatar Solid Black with White User Silhouette --}}
                <div class="w-9 h-9 rounded-full bg-black text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white leading-tight">Admin Support</h3>
                </div>
            </div>
        </div>

        {{-- Messages Stream Area --}}
        <div id="pelanggan-chat-messages"
            class="flex-grow p-4 sm:p-5 overflow-y-auto space-y-3 bg-white dark:bg-slate-800 plg-chat-messages min-h-0">
            @if(isset($chats) && count($chats) > 0)
                @php $lastDate = ''; @endphp
                @foreach($chats as $c)
                    @php
                        $msgDate = \Carbon\Carbon::parse($c->created_at)->format('d M Y');
                        $msgTime = \Carbon\Carbon::parse($c->created_at)->format('H:i');
                        $isMe = ($c->user_id == getUserId());
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
                        {{-- Outgoing Message (Pelanggan - Kanan) --}}
                        <div class="flex items-end justify-end gap-2">
                            <div class="max-w-[75%]">
                                <div class="msg-bubble-out px-3.5 py-2 rounded-2xl rounded-tr-none space-y-1 shadow-2xs">
                                    <p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">{{ $c->pesan }}</p>
                                </div>
                                <div class="flex items-center justify-end gap-1 mt-0.5 pr-1">
                                    <span class="text-[9px] text-slate-400 font-semibold">{{ $msgTime }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Incoming Message (Admin Support - Kiri) --}}
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
                                        class="text-[10px] font-bold text-slate-700 dark:text-slate-300 block">{{ $c->user ? $c->user->name : 'Admin Support' }}</span>
                                    <p class="text-xs font-medium leading-relaxed whitespace-pre-wrap">{{ $c->pesan }}</p>
                                </div>
                                <span class="text-[9px] text-slate-400 font-medium pl-1 mt-0.5 block">{{ $msgTime }}</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="h-full flex items-center justify-center text-center p-6">
                    <p class="text-sm font-bold text-slate-900 dark:text-white">Isi chat pelanggan dan admin.</p>
                </div>
            @endif
        </div>

        {{-- Chat Input Form Footer --}}
        <div class="p-2.5 sm:p-3 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 shrink-0 z-10">
            <form id="form-pelanggan-chat" action="{{ url('/pelanggan/chat') }}" method="POST"
                class="flex items-center gap-2.5">
                @csrf
                <input type="text" id="pelanggan-chat-input" name="pesan" required placeholder="Ketik pesan..."
                    autocomplete="off"
                    class="flex-grow px-3.5 py-2 text-xs sm:text-sm rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 placeholder:text-slate-400">

                <button type="submit" id="btn-send-pelanggan-chat"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs sm:text-sm rounded-lg transition-colors inline-flex items-center justify-center gap-1.5 shrink-0 shadow-xs disabled:opacity-50 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>Kirim</span>
                </button>
            </form>
        </div>
    </div>

    {{-- SCRIPT CHAT PELANGGAN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var messagesContainer = document.getElementById('pelanggan-chat-messages');
            var formChat = document.getElementById('form-pelanggan-chat');
            var inputChat = document.getElementById('pelanggan-chat-input');
            var btnSend = document.getElementById('btn-send-pelanggan-chat');

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

            if (formChat) {
                formChat.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var pesan = inputChat.value.trim();
                    if (!pesan) return;

                    btnSend.disabled = true;
                    var csrfToken = document.querySelector('input[name="_token"]').value;

                    fetch('{{ url('/pelanggan/chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ pesan: pesan })
                    })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            btnSend.disabled = false;
                            if (data.success) {
                                inputChat.value = '';
                                isUserScrolledUp = false;
                                fetchMessages(true);
                            } else {
                                formChat.submit();
                            }
                        })
                        .catch(function () {
                            btnSend.disabled = false;
                            formChat.submit();
                        });
                });
            }

            function fetchMessages(forceScroll) {
                fetch('{{ url('/pelanggan/chat/messages') }}')
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success && data.messages) {
                            var newJson = JSON.stringify(data.messages);
                            if (newJson !== previousMessagesJson || forceScroll) {
                                previousMessagesJson = newJson;
                                renderMessages(data.messages);

                                if (forceScroll || !isUserScrolledUp) {
                                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                                }
                            }
                        }
                    })
                    .catch(function (err) {
                        console.error('Polling error:', err);
                    });
            }

            function renderMessages(messages) {
                if (!messagesContainer) return;

                if (!messages || messages.length === 0) {
                    messagesContainer.innerHTML = '<div class="h-full flex items-center justify-center text-center p-6"><p class="text-sm font-bold text-slate-900 dark:text-white">Isi chat pelanggan dan admin.</p></div>';
                    return;
                }

                var html = '';
                var lastDate = '';
                for (var i = 0; i < messages.length; i++) {
                    var m = messages[i];
                    if (m.date && m.date !== lastDate) {
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

            setInterval(function () {
                fetchMessages(false);
            }, 3000);
        });
    </script>
@endsection