<script setup>
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { AlertCircle, FilterX, Search } from 'lucide-vue-next';
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

const sortOptions = ref([
    { label: 'Name - Asc', value: { field: 'name', order: 1 } },
    { label: 'Name - Desc', value: { field: 'name', order: 0 } },
    { label: 'Email - Asc', value: { field: 'email', order: 1 } },
    { label: 'Email - Desc', value: { field: 'email', order: 0 } },
    { label: 'Created - Asc', value: { field: 'created_at', order: 1 } },
    { label: 'Created - Desc', value: { field: 'created_at', order: 0 } },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <InertiaHead :title="pageTitle" />

        <PageTitleSection>
            <template #title>
                {{ pageTitle }}
            </template>
            <template #end>
                <Button
                    v-if="filteredOrSorted"
                    severity="secondary"
                    type="button"
                    label="Clear Filters"
                    outlined
                    @click="hardReset"
                >
                    <template #icon>
                        <FilterX />
                    </template>
                </Button>
            </template>
        </PageTitleSection>

        <div class="space-y-4">
            <div class="flex gap-3">
                <InputGroup>
                    <InputText
                        v-model="filters.name.value"
                        placeholder="Search by contact name"
                        @keyup.enter="filter"
                    />
                    <Button @click="filter">
                        <template #icon>
                            <Search />
                        </template>
                    </Button>
                </InputGroup>
                <Select
                    v-model="sorting"
                    :options="sortOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Sort By"
                    @change="filter"
                />
            </div>
            <div
                v-if="contacts.data.length"
                v-auto-animate
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
        </div>
    </AppLayout>
</template>