<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { ref } from 'vue';
import { Info, DollarSign, Images, ClipboardList, Settings, Check } from 'lucide-vue-next';

interface Props {
    service: any;
    employees: { id: number; name: string }[];
    tags: { id: number; name: string }[];
    currencies: string[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Services', href: '/services' },
    { title: `Edit ${props.service.name}`, href: '' },
];

const steps = [
    { key: 'details', title: 'Service Details', description: 'Basics and team assignment.', icon: Info },
    { key: 'pricing', title: 'Service Pricing', description: 'One-time or recurring pricing.', icon: DollarSign },
    { key: 'gallery', title: 'Gallery', description: 'Upload up to 3 images.', icon: Images },
    { key: 'requirements', title: 'Requirements', description: 'What you need from the buyer.', icon: ClipboardList },
    { key: 'settings', title: 'Service Settings', description: 'Visibility and workflow options.', icon: Settings },
] as const;
const step = ref(0);
const goNext = () => (step.value = Math.min(step.value + 1, steps.length - 1));
const goPrev = () => (step.value = Math.max(step.value - 1, 0));
const goTo = (idx: number) => (step.value = idx);

const form = useForm({
    name: props.service.name,
    description: props.service.description ?? '',
    employee_id: null as number | null,
    tag_id: null as number | null,
    type: props.service.type as 'onetime' | 'recurring',
    price: props.service.price ?? 0,
    currency: props.service.currency ?? 'USD',
    interval_count: props.service.interval_count ?? 1,
    interval_unit: props.service.interval_unit ?? 'month',
    trial_or_setup: false,
    recurring_action: 'nothing' as 'nothing' | 'reopen' | 'new_order',
    gallery: [] as File[],
    requirements: [] as { type: 'text' | 'choice' | 'file'; label: string; options?: string[] }[],
    allow_review: props.service.settings?.allow_review ?? false,
    group_quantities: props.service.settings?.group_quantities ?? false,
    set_deadline: props.service.settings?.set_deadline ?? false,
    show_in_services_page: props.service.settings?.show_in_services_page ?? true,
});

const submit = () => {
    form.put(route('services.update', props.service.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Edit ${props.service.name}`" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Edit service</h1>
                <Link :href="route('services.index')" class="text-sm underline underline-offset-4">Back to list</Link>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex flex-col md:flex-row">
                    <aside class="border-b border-sidebar-border/70 p-3 md:w-64 md:shrink-0 md:border-b-0 md:border-r md:sticky md:top-4 dark:border-sidebar-border" role="tablist" aria-orientation="vertical">
                        <ol class="space-y-1">
                            <li v-for="(s,i) in steps" :key="s.key">
                                <button type="button" @click="goTo(i)" :aria-selected="i===step" role="tab"
                                        class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm transition-colors"
                                        :class="i===step ? 'bg-neutral-100 dark:bg-neutral-900' : 'hover:bg-neutral-100 dark:hover:bg-neutral-900'">
                                    <component :is="i < step ? Check : s.icon" class="h-4 w-4" />
                                    <span class="truncate" :class="i===step ? 'font-medium' : ''">{{ s.title }}</span>
                                </button>
                            </li>
                        </ol>
                    </aside>

                    <section class="flex-1 p-4 min-h-[420px]">
                        <div class="mb-4 border-b border-neutral-200 pb-3 dark:border-neutral-800">
                            <div class="flex items-center gap-2">
                                <component :is="steps[step].icon" class="h-4 w-4 text-neutral-500" />
                                <h2 class="text-base font-semibold">{{ steps[step].title }}</h2>
                            </div>
                            <p class="mt-1 text-sm text-neutral-500">{{ steps[step].description }}</p>
                        </div>
                        <div class="space-y-6">
                        <div v-if="step===0" class="space-y-6">
                            <div class="grid gap-2">
                                <Label for="name">Name <span class="text-red-600">*</span></Label>
                                <Input id="name" v-model="form.name" placeholder="Service name" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="description">Description</Label>
                                <textarea id="description" v-model="form.description" class="border-input dark:bg-input/30 min-h-32 w-full rounded-md border bg-transparent px-3 py-2 text-sm outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"></textarea>
                            </div>
                        </div>

                        <div v-else-if="step===1" class="space-y-6">
                            <div class="flex items-center gap-3">
                                <button type="button" @click="form.type='onetime'" :class="['rounded-md px-3 py-2 text-sm', form.type==='onetime' ? 'bg-primary text-primary-foreground' : 'bg-neutral-200 dark:bg-neutral-800']">Onetime Service</button>
                                <button type="button" @click="form.type='recurring'" :class="['rounded-md px-3 py-2 text-sm', form.type==='recurring' ? 'bg-primary text-primary-foreground' : 'bg-neutral-200 dark:bg-neutral-800']">Recurring Service</button>
                            </div>

                            <div class="grid grid-cols-[1fr,140px] gap-3">
                                <div class="grid gap-2">
                                    <Label>Price <span class="text-red-600">*</span></Label>
                                    <Input type="number" min="0" step="0.01" v-model.number="form.price" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Currency</Label>
                                    <select v-model="form.currency" class="border-input dark:bg-input/30 rounded-md border bg-transparent px-3 py-2 text-sm outline-none">
                                        <option v-for="c in props.currencies" :key="c" :value="c">{{ c }}</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="form.type==='recurring'" class="grid grid-cols-[160px,1fr] gap-3">
                                <div class="grid gap-2">
                                    <Label>Every</Label>
                                    <Input type="number" min="1" v-model.number="form.interval_count" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>&nbsp;</Label>
                                    <select v-model="form.interval_unit" class="border-input dark:bg-input/30 rounded-md border bg-transparent px-3 py-2 text-sm outline-none">
                                        <option value="day">Day</option>
                                        <option value="week">Week</option>
                                        <option value="month">Month</option>
                                        <option value="year">Year</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="step===2" class="space-y-2">
                            <div class="rounded-md border border-dashed p-8 text-center">
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">Drag image<br /><span class="text-primary">(or) Select</span></div>
                            </div>
                            <p class="text-xs text-neutral-500">You can't upload more then 3 images.</p>
                        </div>

                        <div v-else-if="step===3" class="flex items-center justify-center py-20">
                            <div class="text-center">
                                <div class="text-xl font-semibold">Tell your buyer what you need to get started</div>
                                <p class="text-neutral-500">Structure your Buyer Instructions as free text, a multiple choice question or file upload</p>
                                <button type="button" class="mt-4 rounded-md bg-primary px-4 py-2 text-sm text-primary-foreground">Click here</button>
                            </div>
                        </div>

                        <div v-else-if="step===4" class="space-y-4">
                            <label class="flex items-center gap-2 text-sm">
                                <Checkbox v-model="form.allow_review" />
                                <span>
                                    <span class="block font-medium">Allow Client To Review</span>
                                    <span class="text-neutral-500">Client can review this service after finish the order of this service.</span>
                                </span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <Checkbox v-model="form.group_quantities" />
                                <span>
                                    <span class="block font-medium">Group Multiple Quantities Of This Service Into One Order</span>
                                    <span class="text-neutral-500">By default purchases of multiple quantities are added as separate orders. Different services are always added separately.</span>
                                </span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <Checkbox v-model="form.set_deadline" />
                                <span>
                                    <span class="block font-medium">Set A Deadline</span>
                                    <span class="text-neutral-500">Helps your team see orders that are due soon, not visible to clients.</span>
                                </span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <Checkbox v-model="form.show_in_services_page" />
                                <span>
                                    <span class="block font-medium">Show In Services Page</span>
                                    <span class="text-neutral-500">Choose whether to list this service in your Client Portal's services page. Service can still be used in order forms.</span>
                                </span>
                            </label>
                        </div>
                    </section>
                </div>
                <div class="flex items-center justify-between border-t border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <Button variant="secondary" :disabled="step===0" @click="goPrev">Previous</Button>
                    <div class="flex items-center gap-2">
                        <Button v-if="step<steps.length-1" @click="goNext">Next</Button>
                        <Button v-else :disabled="form.processing" @click="submit">Save</Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>


