<script setup>
import { FilterMatchMode } from '@primevue/core/api';
import { AlertCircle, FunnelX } from 'lucide-vue-next';
import { format, parseISO } from 'date-fns';
import { useLazyDataTable } from '@/composables/useLazyDataTable';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    contacts: Object,
    organizations: Array,
    tags: Array,
});

const pageTitle = 'Contacts';
const breadcrumbs = [
    { label: 'Dashboard', route: route('dashboard') },
    { label: pageTitle, route: route('examples.data-table.contacts.index') },
    { label: 'List' },
];

const {
    processing,
    filters,
    sorting,
    firstDatasetIndex,
    filteredOrSorted,
    debounceInputFilter,
    paginate,
    filter,
    sort,
    hardReset,
} = useLazyDataTable('contacts', {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    organization: { value: null, matchMode: FilterMatchMode.EQUALS },
    tags: { value: null, matchMode: FilterMatchMode.IN },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
}, props.contacts.per_page);
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
                        <FunnelX />
                    </template>
                </Button>
            </template>
        </PageTitleSection>

        <Card pt:body:class="p-3">
            <template #content>
                <DataTable
                    ref="dataTable"
                    v-model:filters="filters"
                    lazy
                    paginator
                    removableSort
                    resizableColumns
                    :loading="processing"
                    :value="props.contacts.data"
                    :totalRecords="props.contacts.total"
                    :sortField="sorting.field"
                    :sortOrder="sorting.order"
                    :rows="props.contacts.per_page"
                    :rowsPerPageOptions="[10, 20, 50, 100]"
                    :first="firstDatasetIndex"
                    columnResizeMode="fit"
                    filterDisplay="row"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} records"
                    @filter="filter"
                    @sort="sort"
                    @page="paginate"
                >
                    <template #empty>
                        <div class="flex justify-center">
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
                    </template>
                    <Column
                        header="Name"
                        field="name"
                        sortable
                    >
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText
                                v-model="filterModel.value"
                                type="text"
                                placeholder="Search by name"
                                fluid
                                @input="debounceInputFilter(filterCallback)"
                            />
                        </template>
                    </Column>
                    <Column
                        header="Email"
                        field="email"
                        sortable
                    >
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText
                                v-model="filterModel.value"
                                type="text"
                                placeholder="Search by Email"
                                fluid
                                @input="debounceInputFilter(filterCallback)"
                            />
                        </template>
                        <template #body="{ data }">
                            {{ data.email }}
                        </template>
                    </Column>
                    <Column
                        header="Organization"
                        filterField="organization"
                        :showFilterMenu="false"
                        :showClearButton="true"
                    >
                        <template #filter="{ filterModel, filterCallback }">
                            <Select
                                v-model="filterModel.value"
                                :options="props.organizations"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Filter by Organization"
                                fluid
                                @update:modelValue="filterCallback"
                            />
                        </template>
                        <template #body="{ data }">
                            {{ data.organization.name }}
                        </template>
                    </Column>
                    <Column
                        header="Tags"
                        filterField="tags"
                        :showFilterMenu="false"
                        :showClearButton="true"
                    >
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                :options="props.tags"
                                optionLabel="name"
                                optionValue="id"
                                display="chip"
                                placeholder="Any"
                                pt:label:class="flex flex-wrap"
                                fluid
                                @update:modelValue="filterCallback"
                            />
                        </template>
                        <template #body="{ data }">
                            <Tag
                                v-for="tag in data.tags"
                                :key="tag.id"
                                :value="tag.name"
                                severity="secondary"
                                class="mr-2"
                            />
                        </template>
                    </Column>
                    <Column
                        field="created_at"
                        header="Created"
                        dataType="date"
                        sortable
                    >
                        <template #filter="{ filterModel, filterCallback }">
                            <DatePicker
                                v-model="filterModel.value"
                                dateFormat="mm/dd/yy"
                                placeholder="mm/dd/yyyy"
                                showButtonBar
                                @update:modelValue="filterCallback"
                            />
                        </template>
                        <template #body="{ data }">
                            {{ format(parseISO(data.created_at), 'MM/dd/yyyy') }}
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </AppLayout>
</template>
