<template>
    <InertiaHead title="Dashboard" />
    <AuthenticatedAdminLayout>
        <Container
            fluid
            class="py-4 md:py-8"
        >
            <div>
                <Card>
                    <template #content>
                        <p class="m-0">You are logged in as an Admin!</p>
                    </template>
                </Card>
            </div>
        </Container>
    </AuthenticatedAdminLayout>
</template>
