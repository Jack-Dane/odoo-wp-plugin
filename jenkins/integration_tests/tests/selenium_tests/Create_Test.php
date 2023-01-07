<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Create_Test extends WordpressTableBase {

	public function test_create () {
		// 1. create the connection
		$this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-connection");

		$this->driver->findElement(WebDriverBy::id("create-data"))->click();
		$this->wait_for_element(WebDriverBy::id("name"));

		$this->driver->findElement(WebDriverBy::id("name"))->sendKeys("test_name");
		$this->driver->findElement(WebDriverBy::id("username"))->sendKeys("test@test.com");
		$this->driver->findElement(WebDriverBy::id("api_key"))->sendKeys("password");
		$this->driver->findElement(WebDriverBy::id("url"))->sendKeys("http://odoo_web:8069");
		$this->driver->findElement(WebDriverBy::id("database_name"))->sendKeys("odoo")
			->submit();

		$text_table_elements = $this->get_table_row_text(0);

		$this->assertContains("test_name", $text_table_elements);
		$this->assertContains("test@test.com", $text_table_elements);
		$this->assertContains("http://odoo_web:8069", $text_table_elements);
		$this->assertContains("odoo", $text_table_elements);

		// 2. create the cf7 form
		$this->driver->get("http://localhost:8000/wp-admin/admin.php?page=wpcf7-new");

		$this->driver->findElement(WebDriverBy::id("title"))->sendKeys("Test Contact Form");
		$this->driver->findElement(WebDriverBy::id("wpcf7-form"))->clear()->sendKeys(
			"<label>Your name[text* your-name]</label><label> Your email[email* your-email]</label>[submit \"Submit\"]"
		);
		$this->driver->findElement(WebDriverBy::cssSelector("input[name='wpcf7-save']"))->submit();

		// 3. create the cf7 plugin form
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

		// 4. create the form mappings
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
		$this->driver->findElement(WebDriverBy::id("cf7_field_name"))->sendKeys("your-email");
		$this->driver->findElement(WebDriverBy::id("odoo_field_name"))->sendKeys("email")->submit();

		$text_table_elements = $this->get_table_row_text(0);

		$this->assertContains("test_form_name", $text_table_elements);
		$this->assertContains("your-email", $text_table_elements);
		$this->assertContains("email", $text_table_elements);

		// test creating a constant value form mapping
		$this->wait_for_element(WebDriverBy::id("create-data"))->click();
		$this->wait_for_element(
			WebDriverBy::xpath("//option[text() = 'test_form_name']")
		)->click();
		$this->driver->findElement(WebDriverBy::id("value_type"))->click();
		$this->driver->findElement(WebDriverBy::id("constant_value"))->sendKeys("http://test.com");
		$this->driver->findElement(WebDriverBy::id("odoo_field_name"))->sendKeys("website")->submit();

		$text_table_elements = $this->get_table_row_text(0);

		$this->assertContains("test_form_name", $text_table_elements);
		$this->assertContains("http://test.com", $text_table_elements);
		$this->assertContains("website", $text_table_elements);		
	}

}

?>