<script setup lang="ts">
import { AlertCircle } from 'lucide-vue-next'
import { format, parseISO } from 'date-fns'
import { usePaginatedDataTable } from '@/composables/usePaginatedDataTable'
import SidebarLayout from '@/layouts/app/SidebarLayout.vue'

const props = defineProps({
    contacts: Object,
})

const pageTitle = 'Contacts'
const breadcrumbs = [
    { label: 'Dashboard', route: route('dashboard') },
    { label: pageTitle, route: route('examples.data-table.contacts.index') },
    { label: 'List' },
]

const {
    processing,
    sorting,
    firstDatasetIndex,
    paginate,
} = usePaginatedDataTable('contacts', {}, props.contacts.per_page)
</script>

<template>
    <SidebarLayout :breadcrumbs="breadcrumbs">
        <InertiaHead :title="pageTitle" />

        <PageTitleSection>
            <template #title>
                {{ pageTitle }}
            </template>
        </PageTitleSection>

        <Card pt:body:class="p-3">
            <template #content>
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
                    />
                    <Column
                        field="email"
                        header="Email"
                    />
                    <Column
                        header="Organization"
                        field="organization"
                    >
                        <template #body="{ data }">
                            {{ data.organization.name }}
                        </template>
                    </Column>
                    <Column
                        header="Tags"
                        field="tags"
                    >
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
                        header="Created"
                        field="created_at"
                    >
                        <template #body="{ data }">
                            {{ format(parseISO(data.created_at), 'MM/dd/yyyy') }}
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </SidebarLayout>
</template>
