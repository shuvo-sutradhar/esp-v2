<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref, watch, type HTMLAttributes } from 'vue';

interface ItemOption {
    value: string | number | null;
    label: string;
}

interface Props {
    modelValue?: string | number | null;
    items: ItemOption[];
    placeholder?: string;
    class?: HTMLAttributes['class'];
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Select an option',
    disabled: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number | null): void;
}>();

const open = ref(false);
const search = ref('');
const container = ref<HTMLElement | null>(null);
const highlightedIndex = ref<number>(-1);

const selected = computed(() => props.items.find(i => i.value === props.modelValue) || null);

watch(
    () => props.modelValue,
    () => {
        // keep search synced with selected label when closed
        if (!open.value) {
            search.value = selected.value?.label ?? '';
        }
    },
    { immediate: true },
);

const filteredItems = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (q === '') return props.items;
    return props.items.filter(i => i.label.toLowerCase().includes(q));
});

const selectItem = (option: ItemOption) => {
    emit('update:modelValue', option.value);
    search.value = option.label;
    open.value = false;
};

const onKeydown = (e: KeyboardEvent) => {
    if (!open.value && (e.key === 'ArrowDown' || e.key === 'Enter')) {
        open.value = true;
        e.preventDefault();
        return;
    }
    if (!open.value) return;
    switch (e.key) {
        case 'ArrowDown':
            highlightedIndex.value = Math.min(highlightedIndex.value + 1, filteredItems.value.length - 1);
            e.preventDefault();
            break;
        case 'ArrowUp':
            highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0);
            e.preventDefault();
            break;
        case 'Enter':
            if (filteredItems.value[highlightedIndex.value]) {
                selectItem(filteredItems.value[highlightedIndex.value]);
            }
            e.preventDefault();
            break;
        case 'Escape':
            open.value = false;
            break;
    }
};

const onDocumentClick = (e: MouseEvent) => {
    if (!container.value) return;
    if (!(e.target instanceof Node)) return;
    if (!container.value.contains(e.target)) {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('click', onDocumentClick));
onBeforeUnmount(() => document.removeEventListener('click', onDocumentClick));
</script>

<template>
    <div ref="container" class="relative" :class="props.class">
        <input
            :data-slot="'input'"
            :placeholder="selected?.label ?? placeholder"
            :disabled="disabled"
            class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"
            @focus="open = true"
            @keydown="onKeydown"
            v-model="search"
        />

        <div v-if="open" class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md border border-input bg-popover p-1 text-sm shadow-md">
            <button
                v-for="(opt, idx) in filteredItems"
                :key="String(opt.value) + '-' + idx"
                class="flex w-full cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-left hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': idx === highlightedIndex }"
                @click.prevent="selectItem(opt)"
            >
                {{ opt.label }}
            </button>
            <div v-if="filteredItems.length === 0" class="px-2 py-1.5 text-muted-foreground">No results</div>
        </div>
    </div>
</template>


