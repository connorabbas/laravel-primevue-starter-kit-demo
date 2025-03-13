<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PageTitleSection from '@/components/PageTitleSection.vue';

const page = usePage();
const currentRoute = computed(() => {
    // Access page.url to trigger re-computation on navigation.
    /* eslint-disable @typescript-eslint/no-unused-vars */
    const url = page.url;
    /* eslint-enable @typescript-eslint/no-unused-vars */
    return route().current();
});

const sidebarNavItems = [
    {
        title: 'Profile',
        href: route('profile.edit'),
        active: currentRoute.value == 'profile.edit',
    },
    {
        title: 'Password',
        href: route('password.edit'),
        active: currentRoute.value == 'password.edit',
    },
    {
        title: 'Appearance',
        href: route('appearance'),
        active: currentRoute.value == 'appearance',
    },
];
</script>

<template>
    <Container vertical>
        <PageTitleSection>
            <template #title>
                Settings
            </template>
            <template #subTitle>
                Manage your profile and account settings
            </template>
        </PageTitleSection>

        <Divider />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-x-0 space-y-1">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        pt:root:class="flex items-center justify-start no-underline"
                        :severity="item.active ? 'primary' : ''"
                        :variant="item.active ? '' : 'text'"
                        :href="item.href"
                        as="InertiaLink"
                    >
                        {{ item.title }}
                    </Button>
                </nav>
            </aside>

            <section class="flex-1 md:max-w-2xl">
                <slot />
            </section>
        </div>
    </Container>
</template>
