<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import ListHeader from '@/components/ListHeader.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Button } from '@/components/ui/button';
import Pagination from '@/components/Pagination.vue';
import { ref, computed } from 'vue';

interface Member { id: number; name: string; email: string; phone?: string | null; role?: string }
const props = defineProps<{ members: { data: Member[]; links: any[] }; filters?: { search?: string } }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Settings', href: '/settings' },
    { title: 'Team', href: '/settings/team' },
];

const removeOne = (id: number) => {
    if (!confirm('Remove this member?')) return;
    router.delete(route('settings.team.destroy', id));
};

// Local selection state for bulk actions
const selectedIds = ref<Set<number>>(new Set());
const allSelected = computed(() => props.members.data.length > 0 && selectedIds.value.size === props.members.data.length);
const toggleSelect = (id: number, checked: boolean) => {
    const next = new Set<number>(selectedIds.value);
    if (checked) next.add(id);
    else next.delete(id);
    selectedIds.value = next;
};
const allCheckedModel = computed<boolean>({
    get: () => allSelected.value,
    set: (val: boolean) => {
        if (val) selectedIds.value = new Set(props.members.data.map((m) => m.id));
        else selectedIds.value = new Set();
    },
});
const rowCheckedModel = (id: number) =>
    computed<boolean>({
        get: () => selectedIds.value.has(id),
        set: (val: boolean) => toggleSelect(id, val),
    });

const confirmBulkDelete = () => {
    if (selectedIds.value.size === 0) return;
    if (!confirm(`Delete ${selectedIds.value.size} selected member(s)?`)) return;
    router.delete(route('settings.team.bulk-destroy'), {
        data: { ids: Array.from(selectedIds.value) },
        preserveScroll: true,
        onSuccess: () => selectedIds.value.clear(),
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Team" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <ListHeader
                title="Team"
                :search="props.filters?.search ?? ''"
                search-placeholder="Search by name, email or phone..."
                @update:search="(val)=>router.get(route('settings.team.index'), { search: val }, { preserveState: true, replace: true, preserveScroll: true })"
            >
                <template #actions>
                    <Link :href="route('settings.team.create')" class="inline-flex items-center rounded-md bg-black px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">Add member</Link>
                </template>
            </ListHeader>

            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex items-center justify-between p-3">
                    <div class="flex items-center gap-2">
                        <Checkbox v-model:checked="allCheckedModel" aria-label="Select all" />
                        <span class="text-sm text-neutral-600 dark:text-neutral-400">Select all</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button variant="destructive" :disabled="selectedIds.size === 0" @click="confirmBulkDelete">Delete selected</Button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="app-table min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                        <thead>
                            <tr class="text-left text-sm text-neutral-500">
                                <th class="w-10 px-3 py-2"></th>
                                <th class="px-3 py-2 font-medium">Name</th>
                                <th class="px-3 py-2 font-medium">Email</th>
                                <th class="px-3 py-2 font-medium">Phone</th>
                                <th class="px-3 py-2 font-medium">Role</th>
                                <th class="px-3 py-2 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-900">
                            <tr v-for="m in props.members.data" :key="m.id" class="text-sm">
                                <td class="w-10 px-3 py-2">
                                    <Checkbox :checked="rowCheckedModel(m.id).value" @update:checked="(val:boolean | 'indeterminate') => { if (val === 'indeterminate') return; rowCheckedModel(m.id).value = val; }" aria-label="Select row" />
                                </td>
                                <td class="px-3 py-2">{{ m.name }}</td>
                                <td class="px-3 py-2">{{ m.email }}</td>
                                <td class="px-3 py-2">{{ m.phone || '-' }}</td>
                                <td class="px-3 py-2">{{ m.role || '-' }}</td>
                                <td class="px-3 py-2 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <Link :href="route('settings.team.edit', m.id)" class="text-primary underline underline-offset-4">Edit</Link>
                                        <button class="text-red-600 underline underline-offset-4" @click="removeOne(m.id)">Remove</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="props.members.data.length===0">
                                <td colspan="4" class="px-3 py-6 text-center text-sm text-neutral-500">No members found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="props.members.links" />
            </div>
        </div>
    </AppLayout>
</template>


