<script setup>
import LinksPanelMenu from '@/Components/PrimeVue/LinksPanelMenu.vue';

// Nav Items
const currentRoute = route().current();
const homeMenuItems = [
    {
        label: 'Welcome',
        icon: 'pi pi-home',
        route: route('welcome'),
        active: currentRoute == 'welcome',
    },
    {
        label: 'Dashboard',
        icon: 'pi pi-th-large',
        route: route('admin.dashboard'),
        active: currentRoute == 'admin.dashboard',
    },
];
const analyticsMenuItems = [
    {
        label: 'Users',
        icon: 'pi pi-user',
        route: route('admin.users.index'),
        active: currentRoute == 'admin.users.index',
    },
];
const exampleNestedMenuItems = [
    {
        label: 'Files',
        icon: 'pi pi-file',
        items: [
            {
                label: 'Images',
                icon: 'pi pi-image',
                items: [
                    {
                        label: 'Logos',
                        icon: 'pi pi-image',
                    },
                ],
            },
        ],
    },
    {
        label: 'Cloud',
        icon: 'pi pi-cloud',
        items: [
            {
                label: 'Upload',
                icon: 'pi pi-cloud-upload',
                command: () => {
                    alert('Example using programmatic functionality');
                },
            },
            {
                label: 'Download',
                icon: 'pi pi-cloud-download',
            },
            {
                label: 'Sync',
                icon: 'pi pi-refresh',
            },
        ],
    },
];
</script>

<template>
    <div class="h-full space-y-5">
        <div class="space-y-2">
            <p class="text-muted-color font-bold uppercase text-sm">Home</p>
            <LinksPanelMenu :model="homeMenuItems" class="w-full" />
        </div>
        <div class="space-y-2">
            <p class="text-muted-color font-bold uppercase text-sm">
                Analytics
            </p>
            <LinksPanelMenu :model="analyticsMenuItems" class="w-full" />
        </div>
        <div class="space-y-2">
            <p class="text-muted-color font-bold uppercase text-sm">
                Example Nested
            </p>
            <LinksPanelMenu :model="exampleNestedMenuItems" class="w-full" />
        </div>
    </div>
</template>
