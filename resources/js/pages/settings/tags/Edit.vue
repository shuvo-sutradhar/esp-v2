<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';

const props = defineProps<{ tag: { id: number; name: string; color?: string } }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Settings', href: '/settings' },
    { title: 'Tags', href: '/settings/tags' },
    { title: `Edit ${props.tag.name}`, href: '' },
];

const form = useForm({ name: props.tag.name, color: props.tag.color || '#8b5cf6' });
const submit = () => form.put(route('settings.tags.update', props.tag.id));
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Edit ${props.tag.name}`" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Edit tag</h1>
                <Link :href="route('settings.tags.index')" class="text-sm underline underline-offset-4">Back</Link>
            </div>
            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="form.name" placeholder="Tag name" />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="color">Color</Label>
                        <Input id="color" type="color" v-model="(form.color as any)" />
                        <InputError :message="form.errors.color" />
                    </div>
                    <Button :disabled="form.processing">Save changes</Button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>


