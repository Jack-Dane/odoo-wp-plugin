<div class="wrap odoo-conn-message-notification-box">
    <?php $test_result = $test_result ?? null ?>

    <?php if (!is_null($test_result)) { ?>
        <h1>Test Result: <?= $test_result['success'] ? "Success" : "Failure" ?> </h1>

        <?php if (!$test_result['success']) { ?>
            <!-- Display error details on failure -->
            <div>
                <b>Error Message: </b>
                <span><?= esc_html__($test_result['error_string']) ?></span>
            </div>

            <div>
                <b>Error Code: </b>
                <span><?= esc_html__($test_result['error_code']) ?></span>
            </div>
        <?php } ?>
    <?php } ?>
</div>