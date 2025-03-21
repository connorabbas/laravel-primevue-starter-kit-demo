<script setup>
import { useTemplateRef, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminGuestLayout from '@/layouts/admin/GuestLayout.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const emailInput = useTemplateRef('email-input');

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('admin.login'), {
        onFinish: () => form.reset('password'),
    });
};

onMounted(() => {
    emailInput.value.$el.focus();
});
</script>

<template>
    <AdminGuestLayout>
        <InertiaHead title="Log in" />

        <template
            v-if="status"
            #message
        >
            <Message
                severity="success"
                :closable="false"
                class="shadow-sm"
            >
                {{ status }}
            </Message>
        </template>

        <form
            class="space-y-6"
            @submit.prevent="submit"
        >
            <div class="flex flex-col gap-2">
                <label for="email">Email</label>
                <InputText
                    id="email"
                    ref="email-input"
                    v-model="form.email"
                    type="email"
                    required
                    fluid
                    :invalid="Boolean(form.errors.email)"
                    autocomplete="username"
                />
                <Message
                    v-if="form.errors?.email"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ form.errors?.email }}
                </Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="password">Password</label>
                <InputText
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    fluid
                    :invalid="Boolean(form.errors.password)"
                    autocomplete="current-password"
                />
                <Message
                    v-if="form.errors?.password"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ form.errors?.password }}
                </Message>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <Checkbox
                            id="remember"
                            v-model="form.remember"
                            class="mr-2"
                            :binary="true"
                        ></Checkbox>
                        <label for="remember">Remember me</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center pt-2">
                <InertiaLink
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="mr-4 underline text-muted-color hover:text-color"
                >
                    Forgot your password?
                </InertiaLink>
                <Button
                    :loading="form.processing"
                    type="submit"
                    label="Log In"
                />
            </div>
        </form>
    </AdminGuestLayout>
</template>
