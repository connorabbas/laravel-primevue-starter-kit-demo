<script setup lang="ts">
import { Head as InertiaHead } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const breadcrumbs = [{ label: 'Admin Dashboard' }]
</script>

<template>
    <InertiaHead title="Admin Dashboard" />

    <AppLayout :breadcrumbs>
        <Card>
            <template #content>
                <p class="m-0">
                    You are logged in as Admin!
                </p>
            </template>
        </Card>
    </AppLayout>
</template>
