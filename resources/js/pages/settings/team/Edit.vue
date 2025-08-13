<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';

const props = defineProps<{ member: { id: number; name: string; email: string; phone?: string | null; role?: string } }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Settings', href: '/settings' },
    { title: 'Team', href: '/settings/team' },
    { title: `Edit ${props.member.name}`, href: '' },
];

const form = useForm({
    name: props.member.name,
    email: props.member.email,
    phone: props.member.phone ?? '',
    password: '',
    password_confirmation: '',
    avatar: null as File | null,
    send_welcome: false,
});
const submit = () => form.put(route('settings.team.update', props.member.id));
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Edit ${props.member.name}`" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Edit member</h1>
                <Link :href="route('settings.team.index')" class="text-sm underline underline-offset-4">Back</Link>
            </div>
            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" placeholder="Full name" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="form.email" placeholder="Email address" />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="phone">Phone</Label>
                            <Input id="phone" v-model="form.phone" placeholder="(999) 999-9999" />
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="avatar">Profile Picture</Label>
                            <input id="avatar" name="avatar" type="file" accept="image/*" @change="(e:any)=>{ form.avatar = e.target.files?.[0] ?? null }" class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]" />
                            <InputError :message="(form.errors as any).avatar" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input id="password" type="password" v-model="form.password" placeholder="Leave blank to keep current" />
                            <InputError :message="form.errors.password" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirm Password</Label>
                            <Input id="password_confirmation" type="password" v-model="form.password_confirmation" placeholder="Repeat password" />
                            <InputError :message="(form.errors as any).password_confirmation" />
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" v-model="form.send_welcome" />
                        <span>Send Welcome Email</span>
                    </label>

                    <Button :disabled="form.processing">Save changes</Button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>


