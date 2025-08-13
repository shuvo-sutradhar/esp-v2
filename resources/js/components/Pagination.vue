<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

defineProps<Props>();

const decodeLabel = (label: string): string =>
    label
        .replaceAll('&laquo;', '«')
        .replaceAll('&raquo;', '»')
        .replaceAll('&hellip;', '…');
</script>

<template>
    <nav v-if="links.length > 1" class="flex items-center justify-between border-t border-sidebar-border/70 px-3 py-2 text-sm dark:border-sidebar-border">
        <div class="flex flex-wrap items-center gap-1">
            <template v-for="(link, i) in links" :key="i">
                <span v-if="link.url === null" class="select-none rounded-md px-3 py-1.5 text-muted-foreground">{{ decodeLabel(link.label) }}</span>
                <Link v-else :href="link.url" preserve-scroll preserve-state class="rounded-md px-3 py-1.5 hover:bg-accent" :class="{ 'bg-accent text-accent-foreground': link.active }">{{ decodeLabel(link.label) }}</Link>
            </template>
        </div>
    </nav>
</template>


