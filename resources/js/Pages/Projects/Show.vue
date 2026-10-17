<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TaskBoard from '@/Components/TaskBoard.vue';
import Modal from '@/Components/Modal.vue';
import TagSelect from '@/Components/TagSelect.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    tasks: {
        type: Array,
        required: true,
    },
    users: {
        type: Array,
        default: () => [],
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

// Quick Status Changed (Drag & Drop or buttons)
const handleStatusChanged = (task, newStatus) => {
    router.patch(
        route('tasks.update', task.id),
        { status: newStatus },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

// --- Task Create Modal & Form ---
const showTaskCreateModal = ref(false);
const taskForm = useForm({
    project_id: props.project.id,
    title: '',
    description: '',
    status: 'todo',
    due_date: '',
    assigned_to: '',
    tags: [],
});

const openTaskCreateModal = (status = 'todo') => {
    taskForm.reset();
    taskForm.project_id = props.project.id;
    taskForm.status = status;
    taskForm.tags = [];
    showTaskCreateModal.value = true;
};

const submitCreateTask = () => {
    taskForm.post(route('tasks.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showTaskCreateModal.value = false;
            taskForm.reset();
        },
    });
};

// --- Task Edit Modal & Form ---
const showTaskEditModal = ref(false);
const editingTaskId = ref(null);
const taskEditForm = useForm({
    title: '',
    description: '',
    status: 'todo',
    due_date: '',
    assigned_to: '',
    tags: [],
});

const openTaskEditModal = (task) => {
    editingTaskId.value = task.id;
    taskEditForm.title = task.title;
    taskEditForm.description = task.description || '';
    taskEditForm.status = task.status;
    taskEditForm.due_date = task.due_date ? task.due_date.substring(0, 10) : '';
    taskEditForm.assigned_to = task.assigned_to || '';
    taskEditForm.tags = task.tags ? task.tags.map(t => t.id) : [];
    showTaskEditModal.value = true;
};

const submitEditTask = () => {
    if (!editingTaskId.value) return;
    taskEditForm.patch(route('tasks.update', editingTaskId.value), {
        preserveScroll: true,
        onSuccess: () => {
            showTaskEditModal.value = false;
            editingTaskId.value = null;
            taskEditForm.reset();
        },
    });
};

// --- Task Delete Modal ---
const showTaskDeleteModal = ref(false);
const taskToDelete = ref(null);
const deleteTaskForm = useForm({});

const openTaskDeleteModal = (task) => {
    taskToDelete.value = task;
    showTaskDeleteModal.value = true;
};

const submitDeleteTask = () => {
    if (!taskToDelete.value) return;
    deleteTaskForm.delete(route('tasks.destroy', taskToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showTaskDeleteModal.value = false;
            taskToDelete.value = null;
        },
    });
};

// --- Task Comments Modal ---
const showCommentsModal = ref(false);
const activeCommentTask = ref(null);
const commentForm = useForm({
    body: '',
});

const openCommentsModal = (task) => {
    activeCommentTask.value = task;
    commentForm.reset();
    showCommentsModal.value = true;
};

const submitComment = () => {
    if (!activeCommentTask.value) return;
    commentForm.post(route('tasks.comments.store', activeCommentTask.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Update comments in local modal view
            const updatedTask = props.tasks.find(t => t.id === activeCommentTask.value.id);
            if (updatedTask) {
                activeCommentTask.value = updatedTask;
            }
            commentForm.reset();
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head :title="project.name + ' - Board'" />

        <!-- Breadcrumb & Project Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                <Link :href="route('projects.index')" class="hover:text-indigo-600 transition">
                    Projects
                </Link>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200">{{ project.name }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                        {{ project.name }}
                    </h1>
                    <p v-if="project.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-2xl">
                        {{ project.description }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        :href="route('projects.index')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>All Projects</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Interactive Kanban Board -->
        <TaskBoard
            :project="project"
            :tasks="tasks"
            :tags="tags"
            :filters="filters"
            @add-task="openTaskCreateModal"
            @edit-task="openTaskEditModal"
            @delete-task="openTaskDeleteModal"
            @open-comments="openCommentsModal"
            @status-changed="handleStatusChanged"
        />

        <!-- Task Create Modal -->
        <Modal :show="showTaskCreateModal" @close="showTaskCreateModal = false">
            <form @submit.prevent="submitCreateTask" class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Add New Task
                </h3>

                <div class="space-y-4">
                    <!-- Title -->
                    <div>
                        <InputLabel for="task-title" value="Task Title *" />
                        <TextInput
                            id="task-title"
                            v-model="taskForm.title"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. Implement OAuth login"
                            required
                            autofocus
                        />
                        <InputError :message="taskForm.errors.title" class="mt-1" />
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel for="task-desc" value="Description" />
                        <textarea
                            id="task-desc"
                            v-model="taskForm.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Details or acceptance criteria..."
                        />
                        <InputError :message="taskForm.errors.description" class="mt-1" />
                    </div>

                    <!-- Column / Status & Due Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="task-status" value="Column / Status *" />
                            <select
                                id="task-status"
                                v-model="taskForm.status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="todo">To Do</option>
                                <option value="doing">In Progress</option>
                                <option value="done">Completed</option>
                            </select>
                            <InputError :message="taskForm.errors.status" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="task-due" value="Due Date" />
                            <TextInput
                                id="task-due"
                                v-model="taskForm.due_date"
                                type="date"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="taskForm.errors.due_date" class="mt-1" />
                        </div>
                    </div>

                    <!-- Assignee -->
                    <div>
                        <InputLabel for="task-assign" value="Assign To" />
                        <select
                            id="task-assign"
                            v-model="taskForm.assigned_to"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Unassigned</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <InputError :message="taskForm.errors.assigned_to" class="mt-1" />
                    </div>

                    <!-- Tags (TagSelect Component) -->
                    <div>
                        <InputLabel value="Tags" class="mb-1.5" />
                        <TagSelect v-model="taskForm.tags" :tags="tags" />
                        <InputError :message="taskForm.errors.tags" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton type="button" @click="showTaskCreateModal = false">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="taskForm.processing">
                        {{ taskForm.processing ? 'Adding...' : 'Add Task' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <!-- Task Edit Modal -->
        <Modal :show="showTaskEditModal" @close="showTaskEditModal = false">
            <form @submit.prevent="submitEditTask" class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Edit Task
                </h3>

                <div class="space-y-4">
                    <div>
                        <InputLabel for="edit-task-title" value="Task Title *" />
                        <TextInput
                            id="edit-task-title"
                            v-model="taskEditForm.title"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="taskEditForm.errors.title" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="edit-task-desc" value="Description" />
                        <textarea
                            id="edit-task-desc"
                            v-model="taskEditForm.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <InputError :message="taskEditForm.errors.description" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="edit-task-status" value="Column / Status *" />
                            <select
                                id="edit-task-status"
                                v-model="taskEditForm.status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="todo">To Do</option>
                                <option value="doing">In Progress</option>
                                <option value="done">Completed</option>
                            </select>
                            <InputError :message="taskEditForm.errors.status" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="edit-task-due" value="Due Date" />
                            <TextInput
                                id="edit-task-due"
                                v-model="taskEditForm.due_date"
                                type="date"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="taskEditForm.errors.due_date" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="edit-task-assign" value="Assign To" />
                        <select
                            id="edit-task-assign"
                            v-model="taskEditForm.assigned_to"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Unassigned</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <InputError :message="taskEditForm.errors.assigned_to" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Tags" class="mb-1.5" />
                        <TagSelect v-model="taskEditForm.tags" :tags="tags" />
                        <InputError :message="taskEditForm.errors.tags" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton type="button" @click="showTaskEditModal = false">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="taskEditForm.processing">
                        {{ taskEditForm.processing ? 'Saving...' : 'Save Changes' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <!-- Task Delete Confirmation Modal -->
        <Modal :show="showTaskDeleteModal" @close="showTaskDeleteModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-2">
                    Delete Task
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                    Are you sure you want to delete task <strong class="text-gray-900 dark:text-white">{{ taskToDelete?.title }}</strong>?
                </p>

                <div class="flex justify-end gap-3">
                    <SecondaryButton type="button" @click="showTaskDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton :disabled="deleteTaskForm.processing" @click="submitDeleteTask">
                        {{ deleteTaskForm.processing ? 'Deleting...' : 'Delete Task' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Task Comments Modal -->
        <Modal :show="showCommentsModal" @close="showCommentsModal = false">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4 mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Task Comments
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ activeCommentTask?.title }}
                        </p>
                    </div>
                    <button @click="showCommentsModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Existing Comments List -->
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1 mb-4">
                    <div
                        v-for="comment in activeCommentTask?.comments"
                        :key="comment.id"
                        class="p-3 bg-gray-50 dark:bg-gray-800/80 rounded-xl border border-gray-100 dark:border-gray-700 text-xs"
                    >
                        <div class="flex items-center justify-between mb-1.5">
                            <strong class="text-gray-800 dark:text-gray-200">
                                {{ comment.user?.name || 'User' }}
                            </strong>
                            <span class="text-gray-400 text-[10px]">
                                {{ new Date(comment.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ comment.body }}
                        </p>
                    </div>

                    <div
                        v-if="!activeCommentTask?.comments || activeCommentTask?.comments.length === 0"
                        class="text-center py-6 text-xs text-gray-400 italic"
                    >
                        No comments yet on this task. Be the first to comment!
                    </div>
                </div>

                <!-- Add Comment Form -->
                <form @submit.prevent="submitComment" class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    <textarea
                        v-model="commentForm.body"
                        rows="2"
                        placeholder="Write a comment..."
                        class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    />
                    <InputError :message="commentForm.errors.body" class="mt-1" />

                    <div class="mt-3 flex justify-end">
                        <PrimaryButton :disabled="commentForm.processing">
                            {{ commentForm.processing ? 'Posting...' : 'Post Comment' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
