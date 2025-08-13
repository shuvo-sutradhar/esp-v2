<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { ref, watch } from 'vue';
import { Combobox } from '@/components/ui/combobox';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clients', href: '/clients' },
    { title: 'Create', href: '/clients/create' },
];

/**
 * Client create form state and submission handler.
 */
const props = defineProps<{ countries: { id: number; name: string }[] }>();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    avatar: null as File | null,
    address: '',
    country_id: null as number | null,
    state: '',
    city: '',
    post_code: '',
    company_name: '',
    tax_id: '',
    send_welcome: false,
});

const isCompany = ref(false);
watch(isCompany, (newVal) => {
    if (!newVal) {
        form.company_name = '';
        form.tax_id = '';
    }
});

const submit = () => {
    form.post(route('clients.store'));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create client" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Create client</h1>
                <Link :href="route('clients.index')" class="text-sm underline underline-offset-4">Back to list</Link>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Name <span class="text-red-600">*</span></Label>
                            <Input id="name" v-model="form.name" placeholder="Full name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="email">Email <span class="text-red-600">*</span></Label>
                            <Input id="email" type="email" v-model="form.email" placeholder="Email address" required />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="phone">Phone</Label>
                            <Input id="phone" v-model="form.phone" placeholder="(999) 999-9999" />
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input id="password" type="password" v-model="form.password" placeholder="Min. 8 characters (optional)" />
                            <span class="text-[11px] text-muted-foreground">If left blank, default password will be set to 12345678.</span>
                            <InputError :message="form.errors.password" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="avatar">Profile Picture</Label>
                        <input id="avatar" name="avatar" type="file" accept="image/*" @change="(e:any)=>{ form.avatar = e.target.files?.[0] ?? null }" class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]" />
                        <InputError :message="form.errors.avatar as any" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="md:col-span-2 grid gap-2">
                            <Label for="address">Billing address</Label>
                            <Input id="address" v-model="form.address" placeholder="Address" />
                            <InputError :message="form.errors.address" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="country_id">Country</Label>
                            <Combobox
                                :items="props.countries.map(c=>({ value: c.id, label: c.name }))"
                                v-model="(form.country_id as any)"
                                placeholder="Select a country"
                            />
                            <InputError :message="form.errors.country_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="city">City</Label>
                            <Input id="city" v-model="form.city" placeholder="City" />
                            <InputError :message="form.errors.city" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="state">State / Province / Region</Label>
                            <Input id="state" v-model="form.state" placeholder="State" />
                            <InputError :message="form.errors.state" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="post_code">Postal / ZIP code</Label>
                            <Input id="post_code" v-model="form.post_code" placeholder="ZIP" />
                            <InputError :message="form.errors.post_code" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox v-model="isCompany" />
                        <Label>I'm Purchasing For A Company</Label>
                    </div>
                    <div v-if="isCompany" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="company_name">Company</Label>
                            <Input id="company_name" v-model="form.company_name" placeholder="Company name" />
                            <InputError :message="form.errors.company_name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="tax_id">TaxId</Label>
                            <Input id="tax_id" v-model="form.tax_id" placeholder="Tax id" />
                            <InputError :message="form.errors.tax_id" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox v-model="form.send_welcome" />
                        <Label>Send Welcome Mail</Label>
                    </div>

                    <div class="pt-2">
                        <Button :disabled="form.processing">Create</Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>


