<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import TaskCard from '@/Components/TaskCard.vue';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    tasks: {
        type: Array,
        required: true,
    },
    tags: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({ q: '', status: '', tag: '' }),
    },
});

const emit = defineEmits(['add-task', 'edit-task', 'delete-task', 'open-comments', 'status-changed']);

// Columns definition
const columns = [
    {
        key: 'todo',
        title: 'To Do',
        color: 'border-t-indigo-500',
        badgeBg: 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300',
        dotColor: 'bg-indigo-500',
    },
    {
        key: 'doing',
        title: 'In Progress',
        color: 'border-t-amber-500',
        badgeBg: 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
        dotColor: 'bg-amber-500',
    },
    {
        key: 'done',
        title: 'Completed',
        color: 'border-t-emerald-500',
        badgeBg: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
        dotColor: 'bg-emerald-500',
    },
];

// Computed tasks per column
const tasksByColumn = computed(() => {
    return {
        todo: props.tasks.filter(t => t.status === 'todo'),
        doing: props.tasks.filter(t => t.status === 'doing'),
        done: props.tasks.filter(t => t.status === 'done'),
    };
});

// Search & Filtering with debounce
const searchQuery = ref(props.filters.q || '');
const selectedTag = ref(props.filters.tag || '');
let searchTimeout = null;

const applyFilters = () => {
    router.get(
        route('projects.show', props.project.id),
        {
            q: searchQuery.value || undefined,
            tag: selectedTag.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch(selectedTag, () => {
    applyFilters();
});

const clearFilters = () => {
    searchQuery.value = '';
    selectedTag.value = '';
    applyFilters();
};

// Drag and drop state
const activeDragColumn = ref(null);

const onDragOver = (e, columnKey) => {
    e.preventDefault();
    activeDragColumn.value = columnKey;
};

const onDragLeave = (columnKey) => {
    if (activeDragColumn.value === columnKey) {
        activeDragColumn.value = null;
    }
};

const onDrop = (e, newStatus) => {
    e.preventDefault();
    activeDragColumn.value = null;
    try {
        const raw = e.dataTransfer.getData('text/plain');
        if (!raw) return;
        const data = JSON.parse(raw);
        if (data && data.id && data.currentStatus !== newStatus) {
            const task = props.tasks.find(t => t.id === data.id);
            if (task) {
                emit('status-changed', task, newStatus);
            }
        }
    } catch (err) {
        console.error('Drag drop error:', err);
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- Search & Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/70 dark:bg-gray-800/70 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 backdrop-blur-md shadow-sm">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Search Input -->
                <div class="relative min-w-[220px] flex-1 max-w-sm">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search tasks..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-gray-100 placeholder-gray-400"
                    />
                </div>

                <!-- Tag Filter -->
                <div class="min-w-[150px]">
                    <select
                        v-model="selectedTag"
                        class="w-full py-2 px-3 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 dark:text-gray-200"
                    >
                        <option value="">All Tags</option>
                        <option v-for="tag in tags" :key="tag.id" :value="tag.id">
                            {{ tag.name }}
                        </option>
                    </select>
                </div>

                <!-- Reset Filters Button -->
                <button
                    v-if="searchQuery || selectedTag"
                    type="button"
                    @click="clearFilters"
                    class="text-xs font-semibold text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition underline cursor-pointer"
                >
                    Reset Filters
                </button>
            </div>

            <!-- Add Task Main Button -->
            <button
                type="button"
                @click="emit('add-task', 'todo')"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Task</span>
            </button>
        </div>

        <!-- Kanban 3 Columns Board -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                v-for="col in columns"
                :key="col.key"
                class="flex flex-col bg-gray-50/80 dark:bg-gray-900/60 rounded-2xl border-t-4 border border-gray-200/80 dark:border-gray-800 shadow-sm transition-all duration-200 min-h-[500px]"
                :class="[
                    col.color,
                    activeDragColumn === col.key ? 'ring-2 ring-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/20' : ''
                ]"
                @dragover="onDragOver($event, col.key)"
                @dragleave="onDragLeave(col.key)"
                @drop="onDrop($event, col.key)"
            >
                <!-- Column Header -->
                <div class="p-4 flex items-center justify-between border-b border-gray-200/60 dark:border-gray-800">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full" :class="col.dotColor" />
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm tracking-wide">
                            {{ col.title }}
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold" :class="col.badgeBg">
                            {{ tasksByColumn[col.key]?.length || 0 }}
                        </span>
                    </div>

                    <!-- Column Add Button -->
                    <button
                        type="button"
                        @click="emit('add-task', col.key)"
                        class="p-1 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-white dark:hover:bg-gray-800 transition"
                        title="Add task to this column"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>

                <!-- Column Cards List -->
                <div class="p-3 flex-1 flex flex-col gap-3 overflow-y-auto max-h-[75vh]">
                    <TaskCard
                        v-for="task in tasksByColumn[col.key]"
                        :key="task.id"
                        :task="task"
                        @status-changed="(t, s) => emit('status-changed', t, s)"
                        @edit="(t) => emit('edit-task', t)"
                        @delete="(t) => emit('delete-task', t)"
                        @comments="(t) => emit('open-comments', t)"
                    />

                    <!-- Empty state -->
                    <div
                        v-if="tasksByColumn[col.key]?.length === 0"
                        class="flex-1 flex flex-col items-center justify-center p-8 text-center text-gray-400 dark:text-gray-600 border border-dashed border-gray-200 dark:border-gray-800 rounded-xl m-1"
                    >
                        <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-xs font-medium">No tasks yet</p>
                        <button
                            type="button"
                            @click="emit('add-task', col.key)"
                            class="mt-2 text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
                        >
                            + Create a task
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
