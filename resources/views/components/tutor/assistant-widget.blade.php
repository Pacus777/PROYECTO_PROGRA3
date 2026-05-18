@props([
    'context' => 'landing',
])

@php
    $isTutor = $context === 'tutor';
    $title = $isTutor ? 'Asistente tutor' : 'Asistente virtual';
    $subtitle = $isTutor ? 'Guía dentro del panel' : 'Admisión escolar';
@endphp

<div
    class="tutor-assistant-root"
    x-data="tutorAssistantWidget(@js(['context' => $context, 'chatUrl' => route('asistente.chat')]))"
    x-cloak
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-24 right-4 z-[80] flex h-[min(32rem,calc(100vh-7rem))] w-[min(100vw-2rem,24rem)] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:right-6"
        role="dialog"
        aria-label="{{ $title }}"
    >
        <header class="flex items-center gap-3 border-b border-slate-100 bg-gradient-to-r from-teal-600 to-emerald-600 px-4 py-3 text-white">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"/>
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-bold">{{ $title }}</p>
                <p class="truncate text-xs text-teal-100">{{ $subtitle }}</p>
            </div>
            <button
                type="button"
                @click="open = false"
                class="rounded-lg p-1.5 text-white/90 transition hover:bg-white/20"
                aria-label="Cerrar chat"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </header>

        <div
            x-ref="messages"
            class="custom-scrollbar flex-1 space-y-3 overflow-y-auto bg-slate-50/80 p-4"
        >
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div
                        class="max-w-[90%] rounded-2xl px-3.5 py-2.5 text-sm leading-relaxed shadow-sm"
                        :class="msg.role === 'user'
                            ? 'rounded-br-md bg-teal-600 text-white'
                            : 'rounded-bl-md border border-slate-100 bg-white text-slate-700'"
                    >
                        <p x-text="msg.text"></p>
                        <template x-if="msg.links && msg.links.length">
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <template x-for="link in msg.links" :key="link.url">
                                    <a
                                        :href="link.url"
                                        class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold transition"
                                        :class="msg.role === 'user'
                                            ? 'bg-white/20 text-white hover:bg-white/30'
                                            : 'bg-teal-50 text-teal-800 hover:bg-teal-100'"
                                        x-text="link.label"
                                        @click="open = false"
                                    ></a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start">
                <div class="rounded-2xl rounded-bl-md border border-slate-100 bg-white px-4 py-3 text-sm text-slate-500 shadow-sm">
                    <span class="inline-flex gap-1">
                        <span class="h-2 w-2 animate-bounce rounded-full bg-teal-400" style="animation-delay: 0ms"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-teal-400" style="animation-delay: 150ms"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-teal-400" style="animation-delay: 300ms"></span>
                    </span>
                </div>
            </div>
        </div>

        <div x-show="suggestions.length" class="border-t border-slate-100 bg-white px-3 py-2">
            <div class="flex gap-2 overflow-x-auto pb-1">
                <template x-for="suggestion in suggestions" :key="suggestion">
                    <button
                        type="button"
                        @click="send(suggestion)"
                        class="flex-shrink-0 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800 transition hover:bg-teal-100"
                        x-text="suggestion"
                    ></button>
                </template>
            </div>
        </div>

        <form @submit.prevent="send(input)" class="border-t border-slate-100 bg-white p-3">
            <div class="flex gap-2">
                <input
                    type="text"
                    x-model="input"
                    :disabled="loading"
                    placeholder="Escribe tu pregunta…"
                    class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-teal-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-200 disabled:opacity-60"
                    autocomplete="off"
                >
                <button
                    type="submit"
                    :disabled="loading || !input.trim()"
                    class="flex-shrink-0 rounded-xl bg-teal-600 px-3 py-2.5 text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50"
                    aria-label="Enviar"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <button
        type="button"
        @click="toggle()"
        class="fixed bottom-4 right-4 z-[80] flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-teal-500 to-emerald-600 text-white shadow-lg shadow-teal-500/30 transition hover:scale-105 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-2 sm:right-6"
        :aria-expanded="open"
        aria-label="Abrir asistente virtual"
    >
        <svg x-show="!open" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"/>
        </svg>
        <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('tutorAssistantWidget', (config) => ({
                    context: config.context || 'landing',
                    chatUrl: config.chatUrl,
                    open: false,
                    loading: false,
                    input: '',
                    messages: [],
                    suggestions: [],

                    toggle() {
                        this.open = !this.open;
                        if (this.open && this.messages.length === 0) {
                            this.fetchReply('__welcome__', false);
                        }
                        this.$nextTick(() => this.scrollDown());
                    },

                    async send(text) {
                        const message = (text ?? this.input).trim();
                        if (!message || this.loading) return;
                        this.input = '';
                        this.messages.push({ role: 'user', text: message, links: [] });
                        this.suggestions = [];
                        this.scrollDown();
                        await this.fetchReply(message, true);
                    },

                    async fetchReply(message, showUserWait) {
                        this.loading = true;
                        try {
                            const res = await fetch(this.chatUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                },
                                credentials: 'same-origin',
                                body: JSON.stringify({ message, context: this.context }),
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                throw new Error(data.message || 'Error al responder');
                            }
                            this.messages.push({
                                role: 'assistant',
                                text: data.message,
                                links: data.links || [],
                            });
                            this.suggestions = data.suggestions || [];
                        } catch (e) {
                            this.messages.push({
                                role: 'assistant',
                                text: 'No pude conectar con el asistente. Intenta de nuevo en un momento.',
                                links: [],
                            });
                        } finally {
                            this.loading = false;
                            this.scrollDown();
                        }
                    },

                    scrollDown() {
                        this.$nextTick(() => {
                            const el = this.$refs.messages;
                            if (el) el.scrollTop = el.scrollHeight;
                        });
                    },
                }));
            });
        </script>
    @endpush
@endonce
