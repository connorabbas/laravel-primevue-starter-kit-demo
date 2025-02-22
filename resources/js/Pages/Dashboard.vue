<template>
    <InertiaHead title="Dashboard" />
    <AuthenticatedLayout>
        <Container vertical>
            <Card>
                <template #content>
                    <p class="m-0">You are logged in!</p>
                </template>
            </Card>
        </Container>
    </AuthenticatedLayout>
</template>
