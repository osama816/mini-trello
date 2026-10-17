<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome to Mini Trello" />

    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-indigo-500 selection:text-white relative overflow-hidden font-sans">
        <!-- Ambient Glowing Background Blobs -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none" />
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-purple-600/25 rounded-full blur-3xl pointer-events-none" />
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none" />

        <!-- Header Navigation -->
        <header class="relative z-10 max-w-7xl w-full mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                </div>
                <span class="text-xl font-black tracking-tight bg-gradient-to-r from-white via-indigo-200 to-purple-300 bg-clip-text text-transparent">
                    Mini Trello
                </span>
            </div>

            <nav v-if="canLogin" class="flex items-center gap-3">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('projects.index')"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-md shadow-indigo-500/20 transition"
                >
                    Go to Projects &rarr;
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm shadow-md shadow-indigo-500/25 transition"
                    >
                        Get Started Free
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Main Hero Section -->
        <main class="relative z-10 max-w-5xl mx-auto px-6 py-16 text-center my-auto">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-semibold uppercase tracking-wider mb-8 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse" />
                Laravel 11 + Vue 3 + Inertia.js Kanban
            </div>

            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-tight sm:leading-none mb-6">
                Organize projects.<br />
                <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Ship tasks faster.
                </span>
            </h1>

            <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-400 mb-10 leading-relaxed">
                A clean, ultra-responsive Kanban project management system built with real-time drag-and-drop, rich tags, task assignments, and seamless reactivity.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('projects.index')"
                    class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:opacity-95 text-white font-bold text-base shadow-xl shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition duration-200"
                >
                    Open Kanban Boards &rarr;
                </Link>
                <Link
                    v-else
                    :href="route('register')"
                    class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:opacity-95 text-white font-bold text-base shadow-xl shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition duration-200"
                >
                    Start Managing for Free
                </Link>

                <Link
                    v-if="!$page.props.auth.user"
                    :href="route('login')"
                    class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-slate-900/80 hover:bg-slate-800 border border-slate-800 text-slate-200 font-semibold text-base transition"
                >
                    Demo Login (test@example.com)
                </Link>
            </div>

            <!-- Feature Badges -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-left max-w-4xl mx-auto">
                <div class="p-5 rounded-2xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-md">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-sm text-slate-200 mb-1">Kanban Workflow</h3>
                    <p class="text-xs text-slate-400">Todo, Doing, and Done columns with instantaneous status syncing.</p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-md">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-sm text-slate-200 mb-1">Tags & Filters</h3>
                    <p class="text-xs text-slate-400">Color-coded tag chips, search queries with debounce, and quick filters.</p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-md">
                    <div class="w-8 h-8 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-sm text-slate-200 mb-1">Secure & Scalable</h3>
                    <p class="text-xs text-slate-400">Strict policy authorization, eager loading (zero N+1), and clean Service Layer.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 max-w-7xl w-full mx-auto px-6 py-6 text-center text-xs text-slate-500">
            &copy; 2026 Mini Trello. Built with Laravel 11, Vue 3, Inertia.js, and Docker.
        </footer>
    </div>
</template>
