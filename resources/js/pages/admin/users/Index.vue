<script setup lang="ts">
import { ref, useTemplateRef } from 'vue'
import { Head as InertiaHead } from '@inertiajs/vue3'
import { FilterMatchMode } from '@primevue/core/api'
import { AlertCircle, EllipsisVertical, FunnelX, Pencil } from 'lucide-vue-next'
import { formatInTimeZone } from 'date-fns-tz'
import { parseISO } from 'date-fns'
import { usePaginatedDataTable } from '@/composables/usePaginatedDataTable'
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitleSection from '@/components/PageTitleSection.vue'
import ClientOnly from '@/components/ClientOnly.vue'
import Menu from '@/components/router-link-menus/Menu.vue'
import { LengthAwarePaginator } from '@/types/paginiation'
import { MenuItem, User } from '@/types'

const props = defineProps<{
    users: LengthAwarePaginator<User>
}>()

const pageTitle = 'Users'
const breadcrumbs = [
    { label: 'Admin Dashboard', route: route('admin.dashboard') },
    { label: pageTitle, route: route('admin.users.index') },
    { label: 'List' },
]

// User context menu
const userContextMenu = useTemplateRef<typeof Menu>('user-context-menu')
const userContextMenuItems = ref<MenuItem[]>([])
function toggleUserContextMenu(event: Event, userData: User) {
    // Populate menu items based on row data
    userContextMenuItems.value = [
        {
            label: 'Manage User',
            lucideIcon: Pencil,
            command: () => {
                alert('User Data: ' + JSON.stringify(userData))
            },
        },
    ]
    // Show the menu
    if (userContextMenu.value && userContextMenu.value?.$el) {
        userContextMenu.value.$el.toggle(event)
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
} = usePaginatedDataTable('users', {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
}, props.users.per_page)
</script>

<template>
    <InertiaHead :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
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
                    @click="hardReset()"
                >
                    <template #icon>
                        <FunnelX />
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
                <ClientOnly>
                    <DataTable
                        ref="dataTable"
                        v-model:filters="filters"
                        lazy
                        paginator
                        removableSort
                        resizableColumns
                        :loading="processing"
                        :value="props.users.data"
                        :totalRecords="props.users.total"
                        :sortField="sorting.field"
                        :sortOrder="sorting.order"
                        :rows="props.users.per_page"
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
                                    No Users found.
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
                            <template #body="{ data }">
                                {{ data.name }}
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
                            header="Created"
                            field="created_at"
                            dataType="date"
                            sortable
                        >
                            <template #filter="{ filterModel, filterCallback }">
                                <DatePicker
                                    v-model="filterModel.value"
                                    dateFormat="mm/dd/yy"
                                    placeholder="mm/dd/yyyy"
                                    showButtonBar
                                    fluid
                                    @update:modelValue="filterCallback"
                                />
                            </template>
                            <template #body="{ data }">
                                {{ formatInTimeZone(parseISO(data.created_at), 'UTC', 'MM/dd/yyyy') }}
                            </template>
                        </Column>
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
                </ClientOnly>
            </template>
        </Card>
    </AppLayout>
</template>
