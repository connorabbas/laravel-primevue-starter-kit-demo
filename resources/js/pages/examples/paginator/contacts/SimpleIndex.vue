<script setup lang="ts">
import { computed } from 'vue'
import { Head as InertiaHead } from '@inertiajs/vue3'
import { AlertCircle } from '@lucide/vue'
import { usePaginatedData } from '@/composables/usePaginatedData'
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitleSection from '@/components/PageTitleSection.vue'
import type { LengthAwarePaginator } from '@/types'
import { route } from '@/utils/route'

const props = defineProps<{
    contacts: LengthAwarePaginator<App.Data.ContactData>,
}>()

const pageTitle = 'Contacts'
const breadcrumbs = [
    { label: 'Dashboard', route: route('dashboard') },
    { label: pageTitle, route: route('examples.paginator.contacts.index') },
    { label: 'List' },
]

const {
    processing,
    pagination,
    firstDatasetIndex,
    paginate,
} = usePaginatedData('contacts', {}, props.contacts.per_page)

const skeletonCards = computed<number[]>(() => Array.from(
    { length: pagination.value.rows },
    (_, index) => index,
))
</script>

<template>
    <InertiaHead :title="pageTitle" />

    <AppLayout
        :title="pageTitle"
        :breadcrumbs="breadcrumbs"
    >
        <PageTitleSection>
            <template #title>
                {{ pageTitle }}
            </template>
        </PageTitleSection>

        <div class="space-y-4">
            <div
                v-if="processing"
                class="grid grid-cols-1 sm:grid-cols-12 gap-4"
                aria-busy="true"
            >
                <div
                    v-for="skeletonIndex in skeletonCards"
                    :key="`contact-skeleton-${skeletonIndex}`"
                    class="sm:col-span-6 lg:col-span-3"
                >
                    <Card
                        class="h-full"
                        pt:content:class="flex flex-col gap-5"
                    >
                        <template #title>
                            <Skeleton
                                :pt="{ root: { class: 'w-2/3! h-5!' } }"
                            />
                        </template>
                        <template #subtitle>
                            <Skeleton
                                :pt="{ root: { class: 'w-1/2! h-4!' } }"
                            />
                        </template>
                        <template #content>
                            <div class="flex flex-wrap gap-2">
                                <Skeleton
                                    :pt="{ root: { class: 'w-20! h-6! [border-radius:var(--p-card-border-radius)]!' } }"
                                />
                                <Skeleton
                                    :pt="{ root: { class: 'w-16! h-6! [border-radius:var(--p-card-border-radius)]!' } }"
                                />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
            <div
                v-else-if="contacts.data.length"
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
                class="flex justify-center"
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
        </div>
    </AppLayout>
</template>
