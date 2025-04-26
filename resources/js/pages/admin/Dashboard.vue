<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <InertiaHead title="Admin Dashboard" />

        <Card>
            <template #content>
                <p class="m-0">
                    You are logged in as Admin!
                </p>
            </template>
        </Card>
    </AppLayout>
</template>
