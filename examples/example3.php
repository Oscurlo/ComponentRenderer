<?php

/**
 * You can capture the content with buffer output and process the components.
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\ComponentRenderer;

include_once "../vendor/autoload.php";
include_once "./register/components.php";

$render = new ComponentRenderer();

?>

<?php $render->start() ?>
<Layout>
    <Container grid="sm">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh">
            <Bootstrap::card card-title="Example 3">
                <Row>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <Column size="12" class="col-lg-6 mb-3">
                            <InputField label-text="Example <?= $i ?>" type="number" class="form-control"
                                placeholder="Example <?= $i ?>" />
                        </Column>
                    <?php endfor ?>

                    <Column size="12">
                        <TextareaField label-text="Textarea" placeholder="Textarea" class="form-control">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Praesentium deleniti, a
                            dolorum, nam ipsa dolore ipsum vel nesciunt reprehenderit quo voluptates tenetur
                            sapiente, similique inventore? Non inventore perspiciatis ullam repellat.
                        </TextareaField>
                    </Column>
                </Row>
            </Bootstrap::card>
        </div>
    </Container>
</Layout>
<?php $render->end() ?>