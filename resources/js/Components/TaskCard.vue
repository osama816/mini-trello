<script setup>
import { computed } from 'vue';

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['status-changed', 'edit', 'delete', 'comments']);

// Overdue check
const isOverdue = computed(() => {
    if (!props.task.due_date || props.task.status === 'done') return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const dueDate = new Date(props.task.due_date);
    return dueDate < today;
});

const formattedDueDate = computed(() => {
    if (!props.task.due_date) return null;
    const d = new Date(props.task.due_date);
    return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
});

const userInitials = computed(() => {
    if (!props.task.assigned_user?.name) return '?';
    return props.task.assigned_user.name
        .split(' ')
        .map(n => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
});

const onDragStart = (event) => {
    event.dataTransfer.setData('text/plain', JSON.stringify({ id: props.task.id, currentStatus: props.task.status }));
    event.dataTransfer.effectAllowed = 'move';
};
</script>

<template>
    <div
        draggable="true"
        @dragstart="onDragStart"
        class="group relative bg-white dark:bg-gray-800/90 rounded-xl p-4 shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700/70 transition-all duration-200 hover:-translate-y-0.5 cursor-grab active:cursor-grabbing backdrop-blur-sm"
    >
        <!-- Top Tags & Actions Row -->
        <div class="flex items-start justify-between gap-2 mb-2">
            <!-- Tags -->
            <div class="flex flex-wrap gap-1.5 flex-1">
                <span
                    v-for="tag in task.tags"
                    :key="tag.id"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold tracking-wide"
                    :style="{
                        backgroundColor: tag.color + '20',
                        color: tag.color,
                        border: '1px solid ' + tag.color + '40',
                    }"
                >
                    {{ tag.name }}
                </span>
            </div>

            <!-- Quick Action Menu -->
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                <button
                    type="button"
                    title="Edit task"
                    @click.stop="emit('edit', task)"
                    class="p-1 rounded-md text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
                <button
                    type="button"
                    title="Delete task"
                    @click.stop="emit('delete', task)"
                    class="p-1 rounded-md text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Task Title -->
        <h4
            @click="emit('edit', task)"
            class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug hover:text-indigo-600 dark:hover:text-indigo-400 transition cursor-pointer"
        >
            {{ task.title }}
        </h4>

        <!-- Task Description Excerpt -->
        <p
            v-if="task.description"
            class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3 font-normal"
        >
            {{ task.description }}
        </p>

        <!-- Footer: Due Date, Comments, and Assignee -->
        <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700/60 text-xs">
            <!-- Left Info: Due date & comments -->
            <div class="flex items-center gap-3">
                <!-- Due Date -->
                <span
                    v-if="formattedDueDate"
                    class="inline-flex items-center gap-1 font-medium"
                    :class="[
                        isOverdue
                            ? 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 px-1.5 py-0.5 rounded'
                            : 'text-gray-400 dark:text-gray-500'
                    ]"
                    :title="isOverdue ? 'Overdue!' : 'Due date'"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ formattedDueDate }}
                </span>

                <!-- Comments Button -->
                <button
                    type="button"
                    @click.stop="emit('comments', task)"
                    class="inline-flex items-center gap-1 text-gray-400 dark:text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                    title="View comments"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>{{ task.comments?.length || 0 }}</span>
                </button>
            </div>

            <!-- Right: Assignee Avatar & Quick Status Dropdown -->
            <div class="flex items-center gap-2">
                <!-- Status quick-switch buttons for mobile / quick click -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-700/80 rounded-lg p-0.5">
                    <button
                        v-if="task.status !== 'todo'"
                        type="button"
                        @click.stop="emit('status-changed', task, task.status === 'done' ? 'doing' : 'todo')"
                        class="p-1 rounded text-gray-500 hover:text-indigo-600 hover:bg-white dark:hover:bg-gray-600 transition"
                        title="Move left"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        v-if="task.status !== 'done'"
                        type="button"
                        @click.stop="emit('status-changed', task, task.status === 'todo' ? 'doing' : 'done')"
                        class="p-1 rounded text-gray-500 hover:text-indigo-600 hover:bg-white dark:hover:bg-gray-600 transition"
                        title="Move right"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Assignee Avatar -->
                <div
                    v-if="task.assigned_user"
                    class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center text-[10px] ring-1 ring-white dark:ring-gray-800 shadow-sm"
                    :title="task.assigned_user.name"
                >
                    {{ userInitials }}
                </div>
                <div
                    v-else
                    class="w-6 h-6 rounded-full border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 text-[10px]"
                    title="Unassigned"
                >
                    ∅
                </div>
            </div>
        </div>
    </div>
</template>
