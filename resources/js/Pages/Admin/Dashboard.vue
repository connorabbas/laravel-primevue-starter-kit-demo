<template>
    <InertiaHead title="Dashboard" />
    <AuthenticatedAdminLayout>
        <Container
            vertical
            fluid
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
