<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head as InertiaHead } from '@inertiajs/vue3'
import { FilterMatchMode } from '@primevue/core/api'
import { AlertCircle, Funnel, RotateCcw, Search } from 'lucide-vue-next'
import { formatInTimeZone } from 'date-fns-tz'
import { parseISO } from 'date-fns'
import { usePaginatedData } from '@/composables/usePaginatedData'
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitleSection from '@/components/PageTitleSection.vue'
import type { LengthAwarePaginator } from '@/types/paginiation'
import type { ContactWithRelations, Organization, Tag } from '@/types'

const props = defineProps<{
    contacts: LengthAwarePaginator<ContactWithRelations>,
    organizations: Organization[],
    tags: Tag[],
}>()

const pageTitle = 'Contacts'
const breadcrumbs = [
    { label: 'Dashboard', route: route('dashboard') },
    { label: pageTitle, route: route('examples.paginator.contacts.index') },
    { label: 'List' },
]

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
}, props.contacts.per_page)

sorting.value = { field: 'created_at', order: 0 }

const showFilters = ref(false)

const sortOptions = ref([
    { label: 'Name - Asc', value: { field: 'name', order: 1 } },
    { label: 'Name - Desc', value: { field: 'name', order: 0 } },
    { label: 'Email - Asc', value: { field: 'email', order: 1 } },
    { label: 'Email - Desc', value: { field: 'email', order: 0 } },
    { label: 'Created - Oldest', value: { field: 'created_at', order: 1 } },
    { label: 'Created - Latest', value: { field: 'created_at', order: 0 } },
])

const textInputMatchModes = [
    { label: 'Starts with', value: FilterMatchMode.STARTS_WITH },
    { label: 'Contains', value: FilterMatchMode.CONTAINS },
    { label: 'Not contains', value: FilterMatchMode.NOT_CONTAINS },
    { label: 'Ends with', value: FilterMatchMode.ENDS_WITH },
    { label: 'Equals', value: FilterMatchMode.EQUALS },
    { label: 'Not equals', value: FilterMatchMode.NOT_EQUALS },
]
const dateInputMatchModes = [
    { label: 'Date is', value: FilterMatchMode.DATE_IS },
    { label: 'Date is not', value: FilterMatchMode.DATE_IS_NOT },
    { label: 'Date is before', value: FilterMatchMode.DATE_BEFORE },
    { label: 'Date is after', value: FilterMatchMode.DATE_AFTER },
]

const appliedFiltersCount = computed(() => {
    return Object.values(filters.value)
        .filter(f => f.value !== null && f.value !== '')
        .length
})
</script>

<template>
    <InertiaHead :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <PageTitleSection>
            <template #title>
                {{ pageTitle }}
            </template>
            <template #end>
                <div class="relative">
                    <Button
                        :disabled="processing"
                        type="button"
                        label="Sort & Filter"
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
            header="Sort & Filter"
            position="right"
            class="w-full! sm:w-[35rem]!"
            blockScroll
        >
            <div class="flex flex-col gap-6 sm:gap-8">
                <div class="flex flex-col gap-2">
                    <label for="sort-by">Sort by</label>
                    <Select
                        v-model="sorting"
                        :options="sortOptions"
                        inputId="sort-by"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Sort by"
                        fluid
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="name-filter">Name</label>
                    <InputGroup>
                        <InputText
                            id="name-filter"
                            v-model="filters.name.value"
                            class="flex-1"
                            placeholder="Filter by first or last name"
                            fluid
                        />
                        <Select
                            v-model="filters.name.matchMode"
                            class="flex-none w-auto"
                            :options="textInputMatchModes"
                            inputId="name-match-mode"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Match mode"
                            fluid
                        />
                    </InputGroup>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="email-filter">Email</label>
                    <InputGroup>
                        <InputText
                            id="email-filter"
                            v-model="filters.email.value"
                            class="flex-1"
                            placeholder="Filter by email"
                            fluid
                        />
                        <Select
                            v-model="filters.email.matchMode"
                            class="flex-none w-auto"
                            :options="textInputMatchModes"
                            inputId="email-match-mode"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Match mode"
                            fluid
                        />
                    </InputGroup>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="organization-filter">Organization</label>
                    <Select
                        v-model="filters.organization.value"
                        :options="props.organizations"
                        inputId="organization-filter"
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
                        v-model="filters.tags.value"
                        :options="props.tags"
                        inputId="tags-filter"
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
                    <InputGroup>
                        <DatePicker
                            v-model="filters.created_at.value"
                            class="flex-1"
                            inputId="created-at-filter"
                            dateFormat="mm/dd/yy"
                            placeholder="mm/dd/yyyy"
                            showButtonBar
                        />
                        <Select
                            v-model="filters.created_at.matchMode"
                            class="flex-none w-auto"
                            :options="dateInputMatchModes"
                            inputId="created-at-match-mode"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Match mode"
                            fluid
                        />
                    </InputGroup>
                </div>
            </div>
            <template #footer>
                <div class="flex items-center gap-4">
                    <Button
                        :disabled="!filteredOrSorted || processing"
                        severity="secondary"
                        type="button"
                        label="Reset"
                        class="flex-auto"
                        outlined
                        @click="() => hardReset({ onFinish: () => showFilters = false })"
                    >
                        <template #icon>
                            <RotateCcw />
                        </template>
                    </Button>
                    <Button
                        label="Apply"
                        class="flex-auto"
                        :loading="processing"
                        @click="() => filter({ onFinish: () => showFilters = false })"
                    >
                        <template #icon>
                            <Search />
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
                                />
                            </div>
                        </template>
                        <template #title>
                            {{ contact.name }}
                        </template>
                        <template
                            v-if="contact.organization"
                            #subtitle
                        >
                            {{ contact.organization.name }}
                        </template>
                        <template #content>
                            <div class="flex flex-col gap-3">
                                <div class="text-muted-color">
                                    {{ contact.email }}
                                </div>
                                <div class="text-muted-color text-xs">
                                    Created: {{ formatInTimeZone(parseISO(contact.created_at), 'UTC', 'MM/dd/yyyy') }}
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <Tag
                                        v-for="tag in contact.tags"
                                        :key="tag.id"
                                        :value="tag.name"
                                        severity="secondary"
                                    />
                                </div>
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
                />
            </div>
        </BlockUI>
    </AppLayout>
</template>
