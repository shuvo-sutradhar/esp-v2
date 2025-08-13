<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem, type User } from '@/types';

interface Props {
    client: User;
}

const breadcrumbs = (clientName: string): BreadcrumbItem[] => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clients', href: '/clients' },
    { title: `Edit ${clientName}`, href: '' },
];

/**
 * Client edit form state and submission handler.
 */
const props = defineProps<Props>();
const form = useForm({
    name: props.client.name,
    email: props.client.email,
    password: '',
});

const submit = () => {
    form.put(route('clients.update', props.client.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs(client.name)">
        <Head :title="`Edit ${client.name}`" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Edit client</h1>
                <Link :href="route('clients.index')" class="text-sm underline underline-offset-4">Back to list</Link>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="form.name" placeholder="Full name" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" type="email" v-model="form.email" placeholder="Email address" required />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <Input id="password" type="password" v-model="form.password" placeholder="Leave blank to keep current" />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="pt-2">
                        <Button :disabled="form.processing">Save changes</Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>


