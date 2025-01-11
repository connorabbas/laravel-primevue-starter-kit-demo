<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const model = defineModel<boolean>(false);
const page = usePage();
</script>

<template>
    <Drawer
        v-model:visible="model"
        :header="page.props.app.name"
        position="left"
    >
        <slot />
    </Drawer>
</template>
