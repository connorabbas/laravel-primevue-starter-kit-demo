<script setup>
import { computed } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    homeRoute: String,
    status: Number
});

const title = computed(() => {
    return {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden',
    }[props.status];
});

const description = computed(() => {
    return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[props.status];
});
</script>

<template>
    <InertiaHead title="Error" />
    <Container fluid>
        <main>
            <div class="h-screen flex items-center justify-center">
                <Card class="p-4 py-6 sm:p-12">
                    <template #content>
                        <div class="text-center">
                            <section>
                                <h2 class="mb-8 font-extrabold text-5xl md:text-8xl">
                                    {{ title }}
                                </h2>
                                <p class="mb-8 text-2xl font-semibold md:text-3xl text-muted-color">
                                    {{ description }}
                                </p>
                                <InertiaLink :href="props.homeRoute">
                                    <Button
                                        label="Back to homepage"
                                        raised
                                    >
                                        <template #icon>
                                            <ArrowLeft />
                                        </template>
                                    </Button>
                                </InertiaLink>
                            </section>
                        </div>
                    </template>
                </Card>
            </div>
        </main>
    </Container>
</template>