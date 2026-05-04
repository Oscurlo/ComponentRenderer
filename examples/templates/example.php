<!-- This is not "blade" -->

<Layout>
    <Container grid="sm">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh">
            <Row class="w-100">
                <div class="{{ isset($users) ? 'col-6' : 'col-12' }}">
                    <Bootstrap::card card-title="Example">
                        <Row>
                            {{ @for ($i = 1; $i <= 6; $i++): }}
                                <Column size="12" class="mb-3">
                                    <InputField label-text="example" type="number" class="form-control"
                                        placeholder="example {{ $i }}" />
                                </Column>
                            {{ @endfor }}
                        </Row>
                    </Bootstrap::card>
                </div>
                {{ @if (isset($users)): }}
                    <div class="col-6">
                        <Bootstrap::card card-title="Users">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{ @foreach ($users as $user): }}
                                        <tr>
                                            <td>{{ $user["id"] }}</td>
                                            <td>{{ $user["name"] }}</td>
                                        </tr>
                                    {{ @endforeach }}
                                </tbody>
                            </table>
                        </Bootstrap::card>
                    </div>
                {{ @endif }}
            </Row>
        </div>
    </Container>
</Layout>
