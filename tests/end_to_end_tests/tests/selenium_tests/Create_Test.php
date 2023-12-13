<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

class Create_Test extends WordpressTableBase
{

    public function test_create()
    {
        // 1. Odoo
        // 1.1 install the Odoo database
        $this->driver->get("http://localhost:8069");
        $this->wait_for_element(WebDriverBy::name("master_pwd"))->clear()->sendKeys("master_pwd");
        $this->driver->findElement(WebDriverBy::id("dbname"))->sendKeys("odoo");
        $this->driver->findElement(WebDriverBy::id("login"))->sendKeys("test@test.com");
        $this->driver->findElement(WebDriverBy::id("password"))->sendKeys("password")->submit();
        $this->log_into_odoo();
        $this->wait_for_element(WebDriverBy::cssSelector(".oi.oi-apps"), 20);

        // 1.2 generate an API key
        $this->driver->findElement(
            WebDriverBy::xpath("//span[@class='oe_topbar_name d-none d-lg-block ms-1']")
        )->click();
        $this->wait_for_element(WebDriverBy::xpath("//span[@data-menu='settings']"))->click();
        $this->wait_for_element(WebDriverBy::xpath("//a[text()='Account Security']"))->click();
        $this->wait_for_element(WebDriverBy::name("api_key_wizard"))->click();

        $this->wait_for_element(WebDriverBy::id("password"))->sendKeys("password");
        $this->driver->findElement(WebDriverBy::name("run_check"))->click();

        $this->wait_for_element(WebDriverBy::id("name"))->sendKeys("WordPress");
        $this->wait_for_element(WebDriverBy::name("make_key"))->click();

        $api_key = $this->wait_for_element(WebDriverBy::xpath("//div[@name='key']/span"))->getText();
        $this->driver->findElement(WebDriverBy::xpath("//button[@class='btn-close']"))->click();

        // 1.3 install the contacts app
        $this->odoo_click_on_app("Apps");

        $this->wait_for_element(
            WebDriverBy::cssSelector(".o_searchview_input")
        )->sendKeys("contacts")->sendKeys(WebDriverKeys::ENTER);
        sleep(2);
        $this->wait_for_element(
            WebDriverBy::name("button_immediate_install")
        )->click();
        // wait for the contacts module to install
        $this->wait_for_element(
            WebDriverBy::xpath("//a[text()='Discuss']"), 60
        );

        // 2. create the connection
        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-connection");

        $this->driver->findElement(WebDriverBy::id("create-data"))->click();
        $this->wait_for_element(WebDriverBy::id("name"));

        $this->driver->findElement(WebDriverBy::id("name"))->sendKeys("test_name");
        $this->driver->findElement(WebDriverBy::id("username"))->sendKeys("test@test.com");
        $this->driver->findElement(WebDriverBy::id("api_key"))->sendKeys($api_key);
        $this->driver->findElement(WebDriverBy::id("url"))->sendKeys("http://odoo_web:8069");
        $this->driver->findElement(WebDriverBy::id("database_name"))->sendKeys("odoo")
            ->submit();

        $text_table_elements = $this->get_table_row_text(0);

        $this->assertContains("test_name", $text_table_elements);
        $this->assertContains("test@test.com", $text_table_elements);
        $this->assertContains("http://odoo_web:8069", $text_table_elements);
        $this->assertContains("odoo", $text_table_elements);

        // 3. create the cf7 form
        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=wpcf7-new");

        $this->driver->findElement(WebDriverBy::id("title"))->sendKeys("Test Contact Form");
        $this->driver->findElement(WebDriverBy::id("wpcf7-form"))->clear()->sendKeys(
            "<label>Your name[text* your-name]</label><label> Your email[email* your-email]</label><label> Multi Choice [select multi \"choice1\"]</label>[submit \"Submit\"]"
        );
        $this->driver->findElement(WebDriverBy::cssSelector("input[name='wpcf7-save']"))->submit();

        // 4. create the cf7 plugin form
        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form");

        $this->driver->findElement(WebDriverBy::id("create-data"))->click();
        // wait for the foreign keys to load
        sleep(2);

        $this->wait_for_element(
            WebDriverBy::xpath("//option[text() = 'test_name']")
        )->click();
        $this->driver->findElement(WebDriverBy::id("odoo_model"))->sendKeys("res.partner");
        $this->driver->findElement(WebDriverBy::id("name"))->sendKeys("test_form_name");
        $this->wait_for_element(
            WebDriverBy::xpath("//option[text() = 'Test Contact Form']")
        )->click()->submit();

        $text_table_elements = $this->get_table_row_text(0);

        $this->assertContains("res.partner", $text_table_elements);
        $this->assertContains("test_name", $text_table_elements);
        $this->assertContains("test_form_name", $text_table_elements);
        $this->assertContains("Test Contact Form", $text_table_elements);

        // 5. create the form mappings
        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping");

        // test creating dynamic form mapping
        $this->wait_for_element(WebDriverBy::id("create-data"))->click();
        $this->wait_for_element(
            WebDriverBy::xpath("//option[text() = 'test_form_name']")
        )->click();
        $this->driver->findElement(WebDriverBy::id("cf7_field_name"))->sendKeys("your-name");
        $this->driver->findElement(WebDriverBy::id("odoo_field_name"))->sendKeys("name")->submit();

        $text_table_elements = $this->get_table_row_text(0);

        $this->assertContains("test_form_name", $text_table_elements);
        $this->assertContains("your-name", $text_table_elements);
        $this->assertContains("name", $text_table_elements);

        $this->wait_for_element(WebDriverBy::id("create-data"))->click();
        $this->wait_for_element(
            WebDriverBy::xpath("//option[text() = 'test_form_name']")
        )->click();
        $this->driver->findElement(WebDriverBy::id("cf7_field_name"))->clear()->sendKeys("your-email");
        $this->driver->findElement(WebDriverBy::id("odoo_field_name"))->clear()->sendKeys("email")->submit();

        // wait for the next row to be created
        $this->get_table_row_text(1);
        $text_table_elements = $this->get_table_row_text(0);

        $this->assertContains("test_form_name", $text_table_elements);
        $this->assertContains("your-email", $text_table_elements);
        $this->assertContains("email", $text_table_elements);

        $this->wait_for_element(WebDriverBy::id("create-data"))->click();
        $this->wait_for_element(
            WebDriverBy::xpath("//option[text() = 'test_form_name']")
        )->click();
        $this->driver->findElement(WebDriverBy::id("cf7_field_name"))->clear()->sendKeys("multi");
        $this->driver->findElement(WebDriverBy::id("odoo_field_name"))->clear()->sendKeys("comment")->submit();

        // wait for the next row to be created
        $this->get_table_row_text(2);
        $text_table_elements = $this->get_table_row_text(0);

        $this->assertContains("test_form_name", $text_table_elements);
        $this->assertContains("multi", $text_table_elements);
        $this->assertContains("comment", $text_table_elements);

        // test creating a constant value form mapping
        $this->wait_for_element(WebDriverBy::id("create-data"))->click();
        $this->wait_for_element(
            WebDriverBy::xpath("//option[text() = 'test_form_name']")
        )->click();
        $this->driver->findElement(WebDriverBy::id("value_type"))->click();
        $this->driver->findElement(WebDriverBy::id("constant_value"))->clear()->sendKeys("http://test.com");
        $this->driver->findElement(WebDriverBy::id("odoo_field_name"))->clear()->sendKeys("website")->submit();

        // wait for the next row to be created
        $this->get_table_row_text(3);
        $text_table_elements = $this->get_table_row_text(0);

        $this->assertContains("test_form_name", $text_table_elements);
        $this->assertContains("http://test.com", $text_table_elements);
        $this->assertContains("website", $text_table_elements);
    }

}

?>