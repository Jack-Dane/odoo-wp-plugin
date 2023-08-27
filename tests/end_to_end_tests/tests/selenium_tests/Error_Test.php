<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Error_Test extends WordpressTableBase
{

    public function test_broken_connection_alert() {
        $this->driver->get(
            "http://localhost:8000/wp-admin/admin.php?page=odoo-connection"
        );
        $edit_button = $this->wait_for_element(
            WebDriverBy::cssSelector(
                ".table-row-edit"
            )
        );

        // edit to an incorrect username
        $edit_button->click();
        $username_input = $this->wait_for_element(
            WebDriverBy::xpath(
                "//table[@class='database-table']/tbody/tr/td[4]/input"
            )
        );
        $username_input->sendKeys(
            "wrong_email"
        );
        $save_button = $this->wait_for_element(
            WebDriverBy::cssSelector(
                ".table-row-save"
            )
        );
        $save_button->click();
        sleep(2);

        $test_button = $this->wait_for_element(
            WebDriverBy::cssSelector(
                ".table-row-test"
            )
        );
        $test_button->click();
        sleep(2);

        $expected_error_message = "{\"success\":false,\"error_string\":\"Username or API Key is incorrect\",\"error_code\":0}";
        $alert_text = $this->wait_for_alert();
        $this->assertEquals(
            $expected_error_message, $alert_text
        );
        $this->driver->switchTo()->alert()->accept();
    }

}
