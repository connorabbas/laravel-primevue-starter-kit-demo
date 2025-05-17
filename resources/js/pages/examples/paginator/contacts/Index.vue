<script setup>
import { ref, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { AlertCircle, Funnel, FunnelX } from 'lucide-vue-next';
import { usePaginatedData } from '@/composables/usePaginatedData';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    contacts: Object,
    organizations: Array,
    tags: Array,
});

const pageTitle = 'Contacts';
const breadcrumbs = [
    { label: 'Dashboard', route: route('dashboard') },
    { label: pageTitle, route: route('examples.paginator.contacts.index') },
    { label: 'List' },
];

const {
    processing,
    filters,
    sorting,
    firstDatasetIndex,
    filteredOrSorted,
    paginate,
    filter,
    hardReset,
} = usePaginatedData('contacts', {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    organization: { value: null, matchMode: FilterMatchMode.EQUALS },
    tags: { value: null, matchMode: FilterMatchMode.IN },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
}, props.contacts.per_page);

sorting.value = { field: 'created_at', order: 0 };

const showFilters = ref(false);

const sortOptions = ref([
    { label: 'Name - Asc', value: { field: 'name', order: 1 } },
    { label: 'Name - Desc', value: { field: 'name', order: 0 } },
    { label: 'Email - Asc', value: { field: 'email', order: 1 } },
    { label: 'Email - Desc', value: { field: 'email', order: 0 } },
    { label: 'Created - Asc', value: { field: 'created_at', order: 1 } },
    { label: 'Created - Desc', value: { field: 'created_at', order: 0 } },
]);

const appliedFiltersCount = computed(() => {
    return Object.values(filters.value)
        .filter(f => f.value !== null)
        .length;
})
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <InertiaHead :title="pageTitle" />

        <PageTitleSection>
            <template #title>
                {{ pageTitle }}
            </template>
            <template #end>
                <div class="relative">
                    <Button
                        :disabled="processing"
                        severity="secondary"
                        type="button"
                        label="Filter & Sort"
                        outlined
                        @click="showFilters = true"
                    >
                        <template #icon>
                            <Funnel />
                        </template>
                    </Button>
                    <Badge
                        v-if="appliedFiltersCount > 0"
                        class="absolute top-0 right-0 -mt-2 -mr-2"
                        :value="appliedFiltersCount"
                        size="small"
                    />
                </div>
            </template>
        </PageTitleSection>

        <Drawer
            v-model:visible="showFilters"
            header="Filter & Sort"
            position="right"
            class="w-full! sm:w-[30rem]!"
            blockScroll
        >
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <label for="sort-by">Sort By</label>
                    <Select
                        id="sort-by"
                        v-model="sorting"
                        :options="sortOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Sort By"
                        fluid
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="name-filter">Name</label>
                    <InputText
                        id="name-filter"
                        v-model="filters.name.value"
                        placeholder="First or last name"
                        fluid
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="organization-filter">Organization</label>
                    <Select
                        id="organization-filter"
                        v-model="filters.organization.value"
                        :options="props.organizations"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Any"
                        showClear
                        fluid
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="tags-filter">Tags</label>
                    <MultiSelect
                        id="tags-filter"
                        v-model="filters.tags.value"
                        :options="props.tags"
                        optionLabel="name"
                        optionValue="id"
                        display="chip"
                        placeholder="Any"
                        pt:label:class="flex flex-wrap"
                        showClear
                        fluid
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="created-at-filter">Created</label>
                    <DatePicker
                        id="created-at-filter"
                        v-model="filters.created_at.value"
                        dateFormat="mm/dd/yy"
                        placeholder="mm/dd/yyyy"
                        showButtonBar
                    />
                </div>
            </div>
            <template #footer>
                <div class="flex items-center gap-2">
                    <Button
                        :disabled="!filteredOrSorted || processing"
                        severity="secondary"
                        type="button"
                        label="Clear Filters"
                        class="flex-auto"
                        outlined
                        @click="() => hardReset({ onFinish: () => showFilters = false })"
                    >
                        <template #icon>
                            <FunnelX />
                        </template>
                    </Button>
                    <Button
                        label="Apply"
                        class="flex-auto"
                        :loading="processing"
                        @click="() => filter({ onFinish: () => showFilters = false })"
                    >
                        <template #icon>
                            <Funnel />
                        </template>
                    </Button>
                </div>
            </template>
        </Drawer>

        <BlockUI
            class="space-y-4 z-[999]"
            :blocked="processing"
        >
            <div
                v-if="contacts.data.length"
                class="grid grid-cols-1 sm:grid-cols-12 gap-4"
            >
                <div
                    v-for="contact in props.contacts.data"
                    :key="contact.id"
                    class="sm:col-span-6 lg:col-span-3"
                >
                    <Card
                        class="h-full"
                        pt:content:class="flex flex-col gap-5"
                    >
                        <template #header>
                            <div class="p-4 pb-0 flex justify-center">
                                <!-- Fake profile img -->
                                <Skeleton
                                    width="10rem"
                                    height="7rem"
                                ></Skeleton>
                            </div>
                        </template>
                        <template #title>{{ contact.name }}</template>
                        <template #subtitle>{{ contact.organization.name }}</template>
                        <template #content>
                            <div class="flex flex-wrap gap-2">
                                <Tag
                                    v-for="tag in contact.tags"
                                    :key="tag.id"
                                    :value="tag.name"
                                    severity="secondary"
                                />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
            <div
                v-else
                class="flex justify - center"
            >
                <Message
                    severity="warn"
                    class="grow text-center flex justify-center"
                >
                    <template #icon>
                        <AlertCircle />
                    </template>
                    No Contacts found.
                </Message>
            </div>
            <div>
                <Paginator
                    :rows="contacts.per_page"
                    :first="firstDatasetIndex"
                    :totalRecords="contacts.total"
                    :rowsPerPageOptions="[20, 50, 100]"
                    template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
                    @page="paginate"
                >
                </Paginator>
            </div>
        </BlockUI>
    </AppLayout>
</template>