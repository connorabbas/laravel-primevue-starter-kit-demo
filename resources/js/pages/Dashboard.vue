<script setup>
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
</script>

<template>
    <InertiaHead title="Dashboard" />

    <div>
        <Card>
            <template #content>
                <p class="m-0">You are logged in!</p>
            </template>
        </Card>
    </div>
</template>
