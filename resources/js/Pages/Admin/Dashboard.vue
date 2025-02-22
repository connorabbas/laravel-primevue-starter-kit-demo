<script setup>
import AuthenticatedAdminLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedAdminLayout });
</script>

<template>
    <InertiaHead title="Dashboard" />
    <Container
        vertical
        fluid
    >
        <div>
            <Card>
                <template #content>
                    <p class="m-0">You are logged in as an Admin!</p>
                </template>
            </Card>
        </div>
    </Container>
</template>
