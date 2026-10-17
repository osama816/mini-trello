<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    projects: {
        type: Object,
        required: true,
    },
});

// Create Project Modal & Form
const showCreateModal = ref(false);
const form = useForm({
    name: '',
    description: '',
});

const submitCreate = () => {
    form.post(route('projects.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
    });
};

// Edit Project Modal & Form
const showEditModal = ref(false);
const editingProject = ref(null);
const editForm = useForm({
    name: '',
    description: '',
});

const openEditModal = (project) => {
    editingProject.value = project;
    editForm.name = project.name;
    editForm.description = project.description || '';
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!editingProject.value) return;
    editForm.put(route('projects.update', editingProject.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            editingProject.value = null;
            editForm.reset();
        },
    });
};

// Delete Project Confirmation Modal
const showDeleteModal = ref(false);
const projectToDelete = ref(null);
const deleteForm = useForm({});

const confirmDelete = (project) => {
    projectToDelete.value = project;
    showDeleteModal.value = true;
};

const submitDelete = () => {
    if (!projectToDelete.value) return;
    deleteForm.delete(route('projects.destroy', projectToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            projectToDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="My Projects" />

        <!-- Header Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Project Boards
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage your Kanban boards, tasks, and team assignments in real-time.
                </p>
            </div>

            <!-- Create Project Button -->
            <button
                type="button"
                @click="showCreateModal = true"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Project</span>
            </button>
        </div>

        <!-- Projects Grid -->
        <div v-if="projects.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="project in projects.data"
                :key="project.id"
                class="group relative bg-white/80 dark:bg-gray-800/80 rounded-2xl p-6 border border-gray-200/80 dark:border-gray-700/80 backdrop-blur-md shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-600/60 transition-all duration-300 flex flex-col justify-between"
            >
                <div>
                    <!-- Top row: Status/Actions -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <Link
                            :href="route('projects.show', project.id)"
                            class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition leading-snug"
                        >
                            {{ project.name }}
                        </Link>

                        <!-- Actions Dropdown Buttons -->
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button
                                type="button"
                                @click="openEditModal(project)"
                                title="Edit project"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click="confirmDelete(project)"
                                title="Delete project"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-6">
                        {{ project.description || 'No description provided.' }}
                    </p>
                </div>

                <!-- Bottom Stats Breakdown -->
                <div>
                    <!-- Visual Progress Bar -->
                    <div class="h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden flex mb-3">
                        <div
                            class="bg-indigo-500 transition-all duration-300"
                            :style="{ width: project.tasks_count ? (project.todo_tasks_count / project.tasks_count * 100) + '%' : '0%' }"
                            title="To Do"
                        />
                        <div
                            class="bg-amber-500 transition-all duration-300"
                            :style="{ width: project.tasks_count ? (project.doing_tasks_count / project.tasks_count * 100) + '%' : '0%' }"
                            title="In Progress"
                        />
                        <div
                            class="bg-emerald-500 transition-all duration-300"
                            :style="{ width: project.tasks_count ? (project.done_tasks_count / project.tasks_count * 100) + '%' : '0%' }"
                            title="Completed"
                        />
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-indigo-500" />
                                {{ project.todo_tasks_count }} Todo
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-amber-500" />
                                {{ project.doing_tasks_count }} Doing
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500" />
                                {{ project.done_tasks_count }} Done
                            </span>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">
                            {{ project.tasks_count }} Total
                        </span>
                    </div>

                    <!-- Open Board Link -->
                    <Link
                        :href="route('projects.show', project.id)"
                        class="mt-4 w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition"
                    >
                        <span>Open Board</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center p-12 text-center bg-white/60 dark:bg-gray-800/60 rounded-3xl border border-dashed border-gray-300 dark:border-gray-700 backdrop-blur-sm"
        >
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">No projects yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mt-1 mb-6">
                Create your first project to start organizing tasks with interactive Kanban columns!
            </p>
            <button
                type="button"
                @click="showCreateModal = true"
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-indigo-500/20 transition cursor-pointer"
            >
                + Create Project
            </button>
        </div>

        <!-- Pagination -->
        <Pagination :links="projects.links" />

        <!-- Create Project Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Create New Project
                </h3>

                <div class="space-y-4">
                    <div>
                        <InputLabel for="project-name" value="Project Name *" />
                        <TextInput
                            id="project-name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. Mobile Redesign"
                            required
                            autofocus
                        />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="project-desc" value="Description (Optional)" />
                        <textarea
                            id="project-desc"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Briefly describe what this project is about..."
                        />
                        <InputError :message="form.errors.description" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton type="button" @click="showCreateModal = false">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create Project' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <!-- Edit Project Modal -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <form @submit.prevent="submitEdit" class="p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Edit Project
                </h3>

                <div class="space-y-4">
                    <div>
                        <InputLabel for="edit-name" value="Project Name *" />
                        <TextInput
                            id="edit-name"
                            v-model="editForm.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="editForm.errors.name" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel for="edit-desc" value="Description" />
                        <textarea
                            id="edit-desc"
                            v-model="editForm.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <InputError :message="editForm.errors.description" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton type="button" @click="showEditModal = false">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="editForm.processing">
                        {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <!-- Delete Project Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-2">
                    Delete Project
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                    Are you sure you want to delete <strong class="text-gray-900 dark:text-white">{{ projectToDelete?.name }}</strong>?
                    All associated tasks will also be archived. This action can be restored if soft deletes are configured.
                </p>

                <div class="flex justify-end gap-3">
                    <SecondaryButton type="button" @click="showDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton :disabled="deleteForm.processing" @click="submitDelete">
                        {{ deleteForm.processing ? 'Deleting...' : 'Delete Project' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
