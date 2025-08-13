<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ListHeader from '@/components/ListHeader.vue';
import Pagination from '@/components/Pagination.vue';
import { type BreadcrumbItem } from '@/types';

interface ServiceListItem {
    id: number;
    name: string;
    type: 'onetime' | 'recurring';
    price: number;
    currency: string;
    status?: string;
    created_at: string;
}

const props = defineProps<{ services: { data: ServiceListItem[]; links: any[] } }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Services', href: '/services' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Services" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <ListHeader title="Services" :show-search="false">
                <template #actions>
                    <Link :href="route('services.create')" class="inline-flex items-center rounded-md bg-black px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200">
                        Create service
                    </Link>
                </template>
            </ListHeader>

            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="overflow-x-auto">
                    <table class="app-table min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                        <thead>
                            <tr class="text-left text-sm text-neutral-500">
                                <th class="px-3 py-2 font-medium">Name</th>
                                <th class="px-3 py-2 font-medium">Type</th>
                                <th class="px-3 py-2 font-medium">Price</th>
                                <th class="px-3 py-2 font-medium">Status</th>
                                <th class="px-3 py-2 font-medium">Created</th>
                                <th class="px-3 py-2 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-900">
                            <tr v-for="s in props.services.data" :key="s.id" class="text-sm">
                                <td class="px-3 py-2">{{ s.name }}</td>
                                <td class="px-3 py-2 capitalize">{{ s.type }}</td>
                                <td class="px-3 py-2">{{ s.currency }} {{ s.price }}</td>
                                <td class="px-3 py-2">{{ s.status ?? '-' }}</td>
                                <td class="px-3 py-2">{{ new Date(s.created_at).toLocaleDateString() }}</td>
                                <td class="px-3 py-2 text-right">
                                    <Link :href="route('services.edit', s.id)" class="text-primary underline underline-offset-4">Edit</Link>
                                </td>
                            </tr>
                            <tr v-if="props.services.data.length === 0">
                                <td colspan="6" class="px-3 py-6 text-center text-sm text-neutral-500">No services found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="props.services.links" />
            </div>
        </div>
    </AppLayout>
</template>


