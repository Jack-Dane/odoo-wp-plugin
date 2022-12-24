<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Create_Test extends SeleniumBase {

	public function test_create () {
		// 1. create the connection
		$this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-connection");

		$this->driver->findElement(WebDriverBy::id("create-data"))->click();
		$this->wait_for_element("name");
		
		$this->driver->findElement(WebDriverBy::id("name"))->sendKeys("test_name");
		$this->driver->findElement(WebDriverBy::id("username"))->sendKeys("test_username");
		$this->driver->findElement(WebDriverBy::id("api_key"))->sendKeys("test_api_key");
		$this->driver->findElement(WebDriverBy::id("url"))->sendKeys("test_url");
		$this->driver->findElement(WebDriverBy::id("database_name"))->sendKeys("test_database_name")
			->submit();

		$text_table_elements = $this->get_table_row_text(0);

		$this->assertContains("test_name", $text_table_elements);
		$this->assertContains("test_username", $text_table_elements);
		$this->assertContains("test_url", $text_table_elements);
		$this->assertContains("test_database_name", $text_table_elements);

		// 2. create the cf7 form
		$this->driver->get("http://localhost:8000/wp-admin/admin.php?page=wpcf7-new");

		$this->driver->findElement(WebDriverBy::id("title"))->sendKeys("Test Contact Form");
		$this->driver->findElement(WebDriverBy::cssSelector("input[name='wpcf7-save']"))->submit();

		// 3. create the cf7 plugin form
		$this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form");

		$this->driver->findElement(WebDriverBy::id("create-data"))->click();
		// wait for the foreign keys to load
		sleep(2);

		$this->driver->findElement(WebDriverBy::id("odoo_connection_id"))
			->findElement(WebDriverBy::xpath("//option[text() = 'test_name']"))
			->click();
		$this->driver->findElement(WebDriverBy::id("odoo_model"))->sendKeys("test_odoo_model");
		$this->driver->findElement(WebDriverBy::id("name"))->sendKeys("test_form_name");
		$this->driver->findElement(WebDriverBy::id("contact_7_id"))
			->findElement(WebDriverBy::xpath("//option[text() = 'Test Contact Form']"))
			->click()
			->submit();

		$text_table_elements = $this->get_table_row_text(0);

		$this->assertContains("test_odoo_model", $text_table_elements);
		$this->assertContains("test_name", $text_table_elements);
		$this->assertContains("test_form_name", $text_table_elements);
		$this->assertContains("Test Contact Form", $text_table_elements);

		// 4. create the form mappings
		$this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping");

		$this->driver->findElement(WebDriverBy::id("create-data"))->click();
		// wait for the foreign keys to load
		sleep(2);

		// test creating dynamic value
		$this->driver->findElement(WebDriverBy::id("odoo_form_id"))
			->findElement(WebDriverBy::xpath("//option[text() = 'test_form_name']"))
			->click();
		$this->driver->findElement(WebDriverBy::id("cf7_field_name"))->sendKeys("your-name");
		$this->driver->findElement(WebDriverBy::id("odoo_field_name"))->sendKeys("name")->submit();

		$text_table_elements = $this->get_table_row_text(0);

		$this->assertContains("test_form_name", $text_table_elements);
		$this->assertContains("your-name", $text_table_elements);
		$this->assertContains("name", $text_table_elements);

		// test creating a constant value form mapping
		$this->wait_for_element("create-data");

		$this->driver->findElement(WebDriverBy::id("create-data"))->click();
		// wait for the foreign keys to load
		sleep(2);

		$this->driver->findElement(WebDriverBy::id("odoo_form_id"))
			->findElement(WebDriverBy::xpath("//option[text() = 'test_form_name']"))
			->click();
		$this->driver->findElement(WebDriverBy::id("value_type"))->click();
		$this->driver->findElement(WebDriverBy::id("constant_value"))->sendKeys("constant_value_test");
		$this->driver->findElement(WebDriverBy::id("odoo_field_name"))->sendKeys("name")->submit();

		$text_table_elements = $this->get_table_row_text(1);

		$this->assertContains("test_form_name", $text_table_elements);
		$this->assertContains("constant_value_test", $text_table_elements);
		$this->assertContains("name", $text_table_elements);		
	}

}

?>