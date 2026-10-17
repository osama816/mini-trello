<script setup>
const selectedTags = defineModel({
    type: Array,
    default: () => [],
});

const props = defineProps({
    tags: {
        type: Array,
        required: true,
    },
});

const toggleTag = (tagId) => {
    const current = [...selectedTags.value];
    const index = current.indexOf(tagId);
    if (index > -1) {
        current.splice(index, 1);
    } else {
        current.push(tagId);
    }
    selectedTags.value = current;
};

const isSelected = (tagId) => selectedTags.value.includes(tagId);
</script>

<template>
    <div>
        <div class="flex flex-wrap gap-2">
            <button
                v-for="tag in tags"
                :key="tag.id"
                type="button"
                @click="toggleTag(tag.id)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-all duration-200 cursor-pointer"
                :class="[
                    isSelected(tag.id)
                        ? 'ring-2 ring-offset-1 dark:ring-offset-gray-900 shadow-sm'
                        : 'opacity-65 hover:opacity-100 hover:scale-105'
                ]"
                :style="{
                    backgroundColor: isSelected(tag.id) ? tag.color + '25' : 'transparent',
                    borderColor: tag.color,
                    color: isSelected(tag.id) ? tag.color : 'inherit',
                }"
            >
                <span
                    class="w-2 h-2 rounded-full"
                    :style="{ backgroundColor: tag.color }"
                />
                {{ tag.name }}
                <svg
                    v-if="isSelected(tag.id)"
                    class="w-3.5 h-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </div>
        <p v-if="tags.length === 0" class="text-xs text-gray-500 italic mt-1">No tags available.</p>
    </div>
</template>
