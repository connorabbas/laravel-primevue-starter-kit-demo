<script setup>
import { useTemplateRef, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

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
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

onMounted(() => {
    emailInput.value.$el.focus();
});
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <template v-if="status" #message>
            <Message severity="success" :closable="false" class="shadow">
                {{ status }}
            </Message>
        </template>

        <form class="space-y-6" @submit.prevent="submit">
            <div class="space-y-2">
                <label for="email">Email</label>
                <InputText
                    ref="email-input"
                    v-model="form.email"
                    id="email"
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

            <div class="space-y-2">
                <label for="password">Password</label>
                <InputText
                    v-model="form.password"
                    id="password"
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
                            v-model="form.remember"
                            :binary="true"
                            id="remember"
                            class="mr-2"
                        ></Checkbox>
                        <label for="remember">Remember me</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center pt-2">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="mr-4 underline text-muted-color hover:text-color"
                >
                    Forgot your password?
                </Link>
                <Button
                    :loading="form.processing"
                    raised
                    type="submit"
                    label="Log In"
                    severity="contrast"
                />
            </div>
        </form>
    </GuestLayout>
</template>
