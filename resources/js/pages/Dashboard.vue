<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <InertiaHead title="Dashboard" />

        <Container vertical>
            <Card>
                <template #content>
                    <p class="m-0">You are logged in!</p>
                </template>
            </Card>
        </Container>
    </AppLayout>
</template>
