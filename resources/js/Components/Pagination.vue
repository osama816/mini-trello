<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div v-if="links && links.length > 3" class="flex flex-wrap items-center justify-center gap-1 mt-6">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="px-3.5 py-2 text-sm text-gray-400 dark:text-gray-500 rounded-lg border border-gray-200 dark:border-gray-700 select-none opacity-60"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                preserve-state
                class="px-3.5 py-2 text-sm font-medium rounded-lg border transition-all duration-200"
                :class="[
                    link.active
                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-500/20'
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:border-gray-300'
                ]"
                v-html="link.label"
            />
        </template>
    </div>
</template>
