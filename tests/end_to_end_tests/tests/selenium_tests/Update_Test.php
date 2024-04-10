<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Update_Test extends WordpressTableBase
{

    public function test_update_connection()
    {
        $input_names = [
            "name", "username", "url", "database_name"
        ];

        $this->update_row(
            "http://localhost:8000/wp-admin/admin.php?page=odoo-connection",
            $input_names,
            "name"
        );
    }

    public function test_update_form()
    {
        $input_names = [
            "odoo_model", "name"
        ];

        $this->update_row(
            "http://localhost:8000/wp-admin/admin.php?page=odoo-form",
            $input_names,
            "name"
        );
    }

    public function test_update_form_mapping_constant_value()
    {
        $input_names = [
            "constant_value", "odoo_field_name"
        ];

        $this->update_row(
            "http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping",
            $input_names,
            "odoo_form_name",
            1
        );
    }

    public function test_update_form_mapping_cf7_field_name()
    {
        $input_names = [
            "cf7_field_name", "odoo_field_name"
        ];

        $this->update_row(
            "http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping",
            $input_names,
            "odoo_form_name"
        );
    }

    private function update_row($edit_endpoint, $input_names, $column_data_name, $row = 0)
    {
        $this->driver->get($edit_endpoint);
        $row_xpath_index = $row + 1;

        $this->wait_for_element(
            WebDriverBy::xpath("//tbody[@id='the-list']/tr[$row_xpath_index]")
        );

        $this->show_action_buttons_on_table($row_xpath_index);
        $edit_button = $this->wait_for_element(
            WebDriverBy::xpath(
                "//tbody[@id='the-list']/tr[$row_xpath_index]/td[contains(@class, '$column_data_name')]/div[@class='row-actions']/span[@class='edit']/a"
            )
        );
        $edit_button->click();

        foreach ($input_names as $input_name) {
            $input_element = $this->wait_for_element(
                WebDriverBy::name($input_name)
            );

            $input_element->clear();
            $input_element->sendKeys($input_name . "_edit");
        }

        $this->driver->findElement(
            WebDriverBy::name("submit")
        )->click();

        foreach ($input_names as $input_name) {
            $display_element = $this->wait_for_element(
                WebDriverBy::xpath(
                    "//tbody[@id='the-list']/tr[$row_xpath_index]/td[contains(@class, 'column-${input_name}')]"
                )
            );

            if ($input_name === "url") {
                // url field appears to automatically prefix with 'http://'
                $input_name = "http://{$input_name}";
            }

            $this->assertEquals(
                "${input_name}_edit", $display_element->getText()
            );
        }
    }

}
