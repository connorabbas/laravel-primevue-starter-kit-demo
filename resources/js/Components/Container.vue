<script setup>
const props = defineProps({
    spacedMobile: {
        type: Boolean,
        default: true,
        required: false,
    },
    fluid: {
        type: Boolean,
        default: false,
        required: false,
    },
    vertical: {
        type: Boolean,
        default: false,
        required: false,
    },
});
</script>

<template>
    <div
        class="md:px-8"
        :class="[
            {
                'max-w-(--breakpoint-2xl) mx-auto': !props.fluid,
                'py-4 md:py-8 space-y-4 md:space-y-8': props.vertical
            },
            props.spacedMobile ? 'px-4' : 'px-0',
        ]"
    >
        <slot />
    </div>
</template>
