<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Users, Tags, Shield, Settings } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Settings', href: '/settings' },
];

const cards = [
    {
        title: 'Team',
        description: 'Manage team members, roles and access.',
        icon: Users,
        href: '/settings/team',
        cta: 'View Details',
    },
    {
        title: 'Tags',
        description: 'Create and manage tags to organize your work.',
        icon: Tags,
        href: '/settings/tags',
        cta: 'View Details',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Settings" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <h1 class="text-xl font-semibold">Services</h1>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div v-for="c in cards" :key="c.title" class="rounded-xl border border-neutral-200 p-5 transition-colors hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900">
                    <div class="flex items-start gap-3">
                        <div class="rounded-full bg-primary/10 p-2 text-primary">
                            <component :is="c.icon" class="h-5 w-5" />
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium">{{ c.title }}</div>
                            <p class="mt-1 text-sm text-neutral-500">{{ c.description }}</p>
                            <Link :href="c.href" class="mt-3 inline-flex items-center text-sm text-primary underline underline-offset-4">{{ c.cta }}</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>


