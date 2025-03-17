<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const visible = defineModel<boolean>({
    default: false,
});
const page = usePage<{
    app: {
        name: string;
    };
}>();
</script>

<template>
    <Drawer
        v-model:visible="visible"
        :header="page.props.app.name"
        position="left"
    >
        <slot />
    </Drawer>
</template>
