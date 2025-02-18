<!-- This is not "blade" -->

<Layout>
    <Container grid="sm">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh">
            <Bootstrap::card card-title="Example">
                <Row>
                    {{ @for ($i = 1; $i <= 3; $i++): }}
                    <Column size="12" class="mb-3">
                        <InputField label-text="example" type="number" class="form-control"
                            placeholder="example {{ $i }}" />
                    </Column>
                    {{ @endfor }}
                </Row>
            </Bootstrap::card>
        </div>
    </Container>
</Layout>