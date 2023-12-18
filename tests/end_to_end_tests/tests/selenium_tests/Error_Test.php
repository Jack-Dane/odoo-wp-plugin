<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Error_Test extends WordpressTableBase
{

    public function test_broken_connection_alert()
    {
        $this->driver->get(
            "http://localhost:8000/wp-admin/admin.php?page=odoo-connection"
        );

        $edit_button = $this->wait_for_element(
            WebDriverBy::xpath(
                "//tbody[@id='the-list']/tr[1]/td[contains(@class, 'row-actions')]/div[@class='row-actions']/span[@class='edit']/a"
            )
        );

        // edit to an incorrect username
        $this->show_action_buttons_on_table();
        $edit_button->click();
        $username_input = $this->wait_for_element(
            WebDriverBy::id("username")
        );
        $username_input->clear();
        $username_input->sendKeys(
            "wrong_email"
        );
        $save_button = $this->wait_for_element(
            WebDriverBy::name("submit")
        );
        $save_button->click();
        sleep(2);

        $this->show_action_buttons_on_table();
        $test_button = $this->wait_for_element(
            WebDriverBy::xpath(
                "//tbody[@id='the-list']/tr[1]/td[contains(@class, 'row-actions')]/div[@class='row-actions']/span[@class='test']/a"
            )
        );
        $test_button->click();
        sleep(2);

        $expected_error_message = "{\"success\":false,\"error_string\":\"Username or API Key is incorrect\",\"error_code\":0}";
        $elements = $this->wait_for_elements(
            WebDriverBy::xpath(
                "//p[contains(text(), '$expected_error_message')]"
            )
        );
        $this->assertCount(1, $elements);
    }

    public function test_broken_send_data()
    {
        $this->driver->get(
            "http://localhost:8000/wp-admin/edit.php"
        );
        $post_id = $this->wait_for_element(
            WebDriverBy::xpath(
                "//a[@class='row-title'][contains(@aria-label, 'no title')]/../../../th/input"
            )
        )->getAttribute("value");

        $this->driver->get(
            "http://localhost:8000/?p=$post_id"
        );
        $this->wait_for_element(WebDriverBy::name("your-name"))->sendKeys("test_name");
        $this->driver->findElement(
            WebDriverBy::name("your-email")
        )->sendKeys("email@email.com")->submit();
        $this->wait_for_element(
            WebDriverBy::xpath(
                "//div[contains(text(), 'There was an error trying to send your message.')]"
            )
        );

        $this->driver->get(
            "http://localhost:8000/wp-admin/admin.php?page=odoo-submit-errors"
        );
        $error_message = $this->wait_for_element(
            WebDriverBy::xpath(
                "//tbody[@id='the-list']/tr[1]/td[3]"
            ), 2
        )->getText();

        $this->assertEquals(
            "Username or API Key is incorrect",
            $error_message
        );
    }

}
