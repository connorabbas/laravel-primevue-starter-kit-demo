<script>
import AuthenticatedAdminLayout from '@/layouts/admin/AuthenticatedLayout.vue';

const pageTitle = 'Profile';
const breadcrumbs = [
    { label: 'Dashboard', route: route('admin.dashboard') },
    { label: pageTitle, route: route('admin.profile.edit') },
    { label: 'Edit' },
];

export default {
    layout: (h, page) => h(AuthenticatedAdminLayout, { breadcrumbs }, () => page)
}
</script>

<script setup>
import DeleteUserForm from './partials/DeleteUserForm.vue';
import UpdatePasswordForm from './partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './partials/UpdateProfileInformationForm.vue';

defineProps({
    auth: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <InertiaHead :title="pageTitle" />

    <Container
        vertical
        fluid
    >
        <PageTitleSection>
            <template #title>
                {{ pageTitle }}
            </template>
        </PageTitleSection>

        <Card
            pt:body:class="max-w-2xl space-y-3"
            pt:caption:class="space-y-1"
        >
            <template #title>Profile Information</template>
            <template #subtitle>
                Update your account's profile information and email
                address.
            </template>
            <template #content>
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </template>
        </Card>

        <Card
            pt:body:class="max-w-2xl space-y-3"
            pt:caption:class="space-y-1"
        >
            <template #title>Update Password</template>
            <template #subtitle>
                Ensure your account is using a long, random password to
                stay secure.
            </template>
            <template #content>
                <UpdatePasswordForm />
            </template>
        </Card>

        <Card
            pt:body:class="max-w-2xl space-y-3"
            pt:caption:class="space-y-1"
        >
            <template #title>Delete Account</template>
            <template #subtitle>
                Once your account is deleted, all of its resources and
                data will be permanently deleted. Before deleting your
                account, please download any data or information that
                you wish to retain.
            </template>
            <template #content>
                <DeleteUserForm />
            </template>
        </Card>
    </Container>
</template>
