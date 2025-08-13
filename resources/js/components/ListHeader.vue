<script setup lang="ts">
import { Input } from '@/components/ui/input';

interface Props {
    title: string;
    search?: string;
    searchPlaceholder?: string;
    showSearch?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    search: '',
    searchPlaceholder: 'Search...',
    showSearch: true,
});

const emit = defineEmits<{
    (e: 'update:search', value: string): void;
}>();

const onInput = (value: string | number) => emit('update:search', String(value));
</script>

<template>
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-semibold">{{ props.title }}</h1>
        <div class="flex items-center gap-2">
            <Input
                v-if="props.showSearch"
                :model-value="props.search"
                :placeholder="props.searchPlaceholder"
                class="w-64"
                @update:modelValue="onInput"
            />
            <slot name="actions" />
        </div>
    </div>
    
</template>



