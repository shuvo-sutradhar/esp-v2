<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { ref, computed, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';

interface ClientListItem {
    id: number;
    name: string;
    email: string;
    created_at: string;
}

const props = defineProps<{ clients: ClientListItem[]; filters?: { search?: string } }>();

/**
 * Search filter state and handler.
 */
const search = ref<string>(props.filters?.search ?? '');
const debouncedSearchVisit = useDebounceFn((q: string) => {
    router.get(route('clients.index'), { search: q }, { preserveState: true, replace: true, preserveScroll: true });
}, 300);

const onSearchChange = (value: string | number) => {
    const q = String(value);
    debouncedSearchVisit(q);
};

/**
 * Local selection state for bulk actions.
 */
const selectedIds = ref<Set<number>>(new Set());
const allSelected = computed(() => props.clients.length > 0 && selectedIds.value.size === props.clients.length);

const toggleSelect = (id: number, checked: boolean) => {
    if (checked) selectedIds.value.add(id);
    else selectedIds.value.delete(id);
};

const allOrSomeSelected = computed(() => (allSelected.value ? true : selectedIds.value.size > 0 ? 'indeterminate' : false));

// Keep selection in sync when the dataset changes (e.g., due to filtering)
watch(
    () => props.clients,
    (list) => {
        const current = new Set<number>();
        for (const c of list) current.add(c.id);
        selectedIds.value = new Set(Array.from(selectedIds.value).filter((id) => current.has(id)));
    },
);

/**
 * Confirm and perform single row delete.
 */
const confirmDelete = (id: number) => {
    if (!confirm('Delete this client?')) return;
    router.delete(route('clients.destroy', id), { preserveScroll: true });
};

/**
 * Confirm and perform bulk delete.
 */
const confirmBulkDelete = () => {
    if (selectedIds.value.size === 0) return;
    if (!confirm(`Delete ${selectedIds.value.size} selected client(s)?`)) return;
    router.delete(route('clients.bulk-destroy'), {
        data: { ids: Array.from(selectedIds.value) },
        preserveScroll: true,
        onSuccess: () => selectedIds.value.clear(),
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clients', href: '/clients' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Clients" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-xl font-semibold">Clients</h1>
                <div class="flex items-center gap-2">
                    <Input
                        v-model="search"
                        placeholder="Search by name or email..."
                        class="w-64"
                        @update:modelValue="onSearchChange"
                    />
                    <Link :href="route('clients.create')" class="inline-flex items-center rounded-md bg-black px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                        Create client
                    </Link>
                </div>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex items-center justify-between p-3">
                    <div class="flex items-center gap-2">
                        <Checkbox :checked="allOrSomeSelected as any" @update:checked="(v: boolean | 'indeterminate') => { if (v) selectedIds.value = new Set(props.clients.map(c => c.id)); else selectedIds.value.clear(); }" aria-label="Select all" />
                        <span class="text-sm text-neutral-600 dark:text-neutral-400">Select all</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button variant="destructive" :disabled="selectedIds.size === 0" @click="confirmBulkDelete">Delete selected</Button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                        <thead>
                            <tr class="text-left text-sm text-neutral-500">
                                <th class="w-10 px-3 py-2"></th>
                                <th class="px-3 py-2 font-medium">Name</th>
                                <th class="px-3 py-2 font-medium">Email</th>
                                <th class="px-3 py-2 font-medium">Joined</th>
                                <th class="px-3 py-2 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-900">
                            <tr v-for="client in props.clients" :key="client.id" class="text-sm">
                                <td class="w-10 px-3 py-2">
                                    <Checkbox :checked="selectedIds.has(client.id)" @update:checked="(val: boolean | 'indeterminate') => toggleSelect(client.id, !!val)" aria-label="Select row" />
                                </td>
                                <td class="px-3 py-2">{{ client.name }}</td>
                                <td class="px-3 py-2">{{ client.email }}</td>
                                <td class="px-3 py-2">{{ new Date(client.created_at).toLocaleDateString() }}</td>
                                <td class="px-3 py-2 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <Link :href="route('clients.edit', client.id)" class="text-primary underline underline-offset-4">
                                            Edit
                                        </Link>
                                        <button class="text-red-600 underline underline-offset-4" @click="confirmDelete(client.id)">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="props.clients.length === 0">
                                <td colspan="5" class="px-3 py-6 text-center text-sm text-neutral-500">No clients found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
    
</template>


