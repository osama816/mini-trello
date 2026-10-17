<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const showFlash = ref(false);
const flashMessage = ref('');
const flashType = ref('success');

watch([flashSuccess, flashError], ([newSuccess, newError]) => {
    if (newSuccess) {
        flashMessage.value = newSuccess;
        flashType.value = 'success';
        showFlash.value = true;
        setTimeout(() => { showFlash.value = false; }, 4000);
    } else if (newError) {
        flashMessage.value = newError;
        flashType.value = 'error';
        showFlash.value = true;
        setTimeout(() => { showFlash.value = false; }, 4000);
    }
}, { immediate: true });
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-indigo-50/20 dark:from-gray-950 dark:via-gray-900 dark:to-indigo-950/20 text-gray-900 dark:text-gray-100 antialiased font-sans">
        <!-- Floating Flash Toast -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showFlash && flashMessage"
                class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border backdrop-blur-lg"
                :class="[
                    flashType === 'success'
                        ? 'bg-emerald-500/90 text-white border-emerald-400 shadow-emerald-500/20'
                        : 'bg-red-500/90 text-white border-red-400 shadow-red-500/20'
                ]"
            >
                <svg v-if="flashType === 'success'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ flashMessage }}</span>
                <button @click="showFlash = false" class="ml-2 hover:opacity-75 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </transition>

        <!-- Top Navigation -->
        <nav class="sticky top-0 z-40 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200/80 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Left: Brand Logo & Links -->
                    <div class="flex items-center gap-6">
                        <Link :href="route('projects.index')" class="flex items-center gap-2.5 group">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                                </svg>
                            </div>
                            <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">
                                Mini Trello
                            </span>
                        </Link>

                        <!-- Nav items -->
                        <div class="hidden sm:flex items-center gap-1">
                            <Link
                                :href="route('projects.index')"
                                class="px-3 py-1.5 rounded-lg text-sm font-semibold transition"
                                :class="[
                                    route().current('projects.*')
                                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300'
                                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'
                                ]"
                            >
                                Projects
                            </Link>
                        </div>
                    </div>

                    <!-- Right: User Dropdown -->
                    <div class="flex items-center gap-3">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750 transition shadow-sm cursor-pointer"
                                >
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold text-xs flex items-center justify-center">
                                        {{ $page.props.auth.user.name[0]?.toUpperCase() }}
                                    </div>
                                    <span class="hidden sm:inline">{{ $page.props.auth.user.name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
                                    Signed in as <strong class="text-gray-800 dark:text-gray-200">{{ $page.props.auth.user.email }}</strong>
                                </div>
                                <DropdownLink :href="route('profile.edit')"> Profile Settings </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Slot Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
