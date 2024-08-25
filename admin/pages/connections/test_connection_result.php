<?php
$test_result = $test_result ?? null;
if (!is_null($test_result)) {
	$connection_result_class = $test_result['success'] ? 'test-result-ok' : 'test-result-failure'
	?>
    <div class="wrap odoo-conn-message-notification-box <?= $connection_result_class ?>">
        <h1>Test Result: <?= $test_result['success'] ? 'Success' : 'Failure' ?> </h1>

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
    </div>
<?php } ?>