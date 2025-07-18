<script setup>
import { useTemplateRef, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GuestAuthLayout from '@/layouts/GuestAuthLayout.vue';

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const emailInput = useTemplateRef('email-input');

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    loginForm.post(route('login'), {
        onFinish: () => loginForm.reset('password'),
    });
};

onMounted(() => {
    emailInput.value.$el.focus();
});
</script>

<template>
    <GuestAuthLayout>
        <InertiaHead title="Log in" />

        <template
            v-if="props.status"
            #message
        >
            <Message
                severity="success"
                :closable="false"
                class="shadow-sm"
            >
                {{ props.status }}
            </Message>
        </template>

        <template #title>
            <div class="text-center">
                Log in to your account
            </div>
        </template>

        <template #subtitle>
            <div class="text-center">
                Enter your email and password below to log in
            </div>
        </template>

        <form
            class="space-y-6 sm:space-y-8"
            @submit.prevent="submit"
        >
            <div class="flex flex-col gap-2">
                <label for="email">Email address</label>
                <InputText
                    id="email"
                    ref="email-input"
                    v-model="loginForm.email"
                    :invalid="Boolean(loginForm.errors?.email)"
                    type="email"
                    autocomplete="username"
                    required
                    fluid
                />
                <Message
                    v-if="loginForm.errors?.email"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ loginForm.errors?.email }}
                </Message>
            </div>

            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <label for="password">Password</label>
                    <InertiaLink
                        v-if="props.canResetPassword"
                        :href="route('password.request')"
                    >
                        <Button
                            class="p-0"
                            variant="link"
                            label="Forgot your password?"
                        />
                    </InertiaLink>
                </div>
                <Password
                    v-model="loginForm.password"
                    :invalid="Boolean(loginForm.errors?.password)"
                    :feedback="false"
                    autocomplete="current-password"
                    inputId="password"
                    toggleMask
                    required
                    fluid
                />
                <Message
                    v-if="loginForm.errors?.password"
                    severity="error"
                    variant="simple"
                    size="small"
                >
                    {{ loginForm.errors?.password }}
                </Message>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <Checkbox
                            v-model="loginForm.remember"
                            class="mr-2"
                            inputId="remember"
                            :binary="true"
                        />
                        <label for="remember">Remember me</label>
                    </div>
                </div>
            </div>

            <div>
                <Button
                    :loading="loginForm.processing"
                    type="submit"
                    label="Log in"
                    fluid
                />
            </div>

            <div class="text-center">
                <span class="text-muted-color mr-1">Don't have an account?</span>
                <InertiaLink :href="route('register')">
                    <Button
                        class="p-0"
                        variant="link"
                        label="Sign up"
                    />
                </InertiaLink>
            </div>
        </form>
    </GuestAuthLayout>
</template>
