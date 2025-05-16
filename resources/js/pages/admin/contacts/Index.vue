<script setup>
import { ref, useTemplateRef } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { AlertCircle, EllipsisVertical, FilterX, Pencil } from 'lucide-vue-next';
import { useLazyDataTable } from '@/composables/useLazyDataTable';
import AppLayout from '@/layouts/AppLayout.vue';
import Menu from '@/components/primevue/menu/Menu.vue';

const props = defineProps({
    contacts: Object,
});

const pageTitle = 'Contacts';
const breadcrumbs = [
    { label: 'Admin Dashboard', route: route('admin.dashboard') },
    { label: pageTitle, route: route('admin.contacts.index') },
    { label: 'List' },
];

// User context menu
const userContextMenu = useTemplateRef('user-context-menu');
const userContextMenuItems = ref([]);
function toggleUserContextMenu(event, userData) {
    // Populate menu items based on row data
    userContextMenuItems.value = [
        {
            label: 'Manage User',
            lucideIcon: Pencil,
            command: () => {
                alert('User Data: ' + JSON.stringify(userData));
            },
        },
    ];
    // Show the menu
    if (userContextMenu.value && userContextMenu.value?.el) {
        userContextMenu.value.el.toggle(event);
    }
}

// DataTable
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
                        <FilterX />
                    </template>
                </Button>
            </template>
        </PageTitleSection>

        <Card pt:body:class="p-3">
            <template #content>
                <Menu
                    ref="user-context-menu"
                    class="shadow-sm"
                    :model="userContextMenuItems"
                    popup
                />
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
                        filterField="name"
                        header="Name"
                        sortable
                        :showFilterMenu="false"
                        :showClearButton="true"
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
                        <template #body="{ data }">
                            {{ `${data.first_name} ${data.last_name}` }}
                        </template>
                    </Column>
                    <Column
                        field="email"
                        header="Email"
                        sortable
                        :showFilterMenu="false"
                        :showClearButton="true"
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
                    <!-- Format date as needed, likely use date-fns -->
                    <Column
                        field="created_at"
                        header="Created"
                        sortable
                    />
                    <Column header="Action">
                        <template #body="{ data }">
                            <Button
                                v-tooltip.top="'Show User Actions'"
                                type="button"
                                severity="secondary"
                                rounded
                                text
                                @click="toggleUserContextMenu($event, data)"
                            >
                                <template #icon>
                                    <EllipsisVertical class="size-5!" />
                                </template>
                            </Button>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </AppLayout>
</template>
