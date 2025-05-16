<script setup>
import { AlertCircle } from 'lucide-vue-next';
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

// DataTable
const {
    processing,
    sorting,
    firstDatasetIndex,
    paginate,
    filter,
    sort,
    hardReset,
} = useLazyDataTable('contacts', {}, props.contacts.per_page);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <InertiaHead :title="pageTitle" />

        <PageTitleSection>
            <template #title>
                {{ pageTitle }}
            </template>
        </PageTitleSection>

        <Card pt:body:class="p-3">
            <template #content>
                <Menu
                    ref="contact-context-menu"
                    class="shadow-sm"
                    :model="contactContextMenuItems"
                    popup
                />
                <DataTable
                    lazy
                    paginator
                    :loading="processing"
                    :value="props.contacts.data"
                    :totalRecords="props.contacts.total"
                    :sortField="sorting.field"
                    :sortOrder="sorting.order"
                    :rows="props.contacts.per_page"
                    :rowsPerPageOptions="[10, 20, 50, 100]"
                    :first="firstDatasetIndex"
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
                        field="name"
                        header="Name"
                    >
                        <template #body="{ data }">
                            {{ `${data.first_name} ${data.last_name}` }}
                        </template>
                    </Column>
                    <Column
                        field="email"
                        header="Email"
                    />
                    <!-- Format date as needed, likely use date-fns -->
                    <Column
                        field="created_at"
                        header="Created"
                    />
                </DataTable>
            </template>
        </Card>
    </AppLayout>
</template>
