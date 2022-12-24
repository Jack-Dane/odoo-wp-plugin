<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Update_Test extends SeleniumBase {

	public function test_update_connection () {
		$this->update_row("http://localhost:8000/wp-admin/admin.php?page=odoo-connection", array());
	}

	public function test_update_form () {
		$this->update_row("http://localhost:8000/wp-admin/admin.php?page=odoo-form", array());
	}

	public function test_update_form_mapping () {
		$this->update_row(
			"http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping", 
			array(2)
		);
	}

	private function save_edit () {
		$save_button = $this->driver->findElement(WebDriverBy::cssSelector(".table-row-save"));
		// sometimes not in view
		$this->driver->executeScript("arguments[0].click();", array($save_button));
		sleep(2);
	}

	private function update_row ($edit_endpoint, $ignore_indexs) {
		$this->driver->get($edit_endpoint);
		sleep(2);

		$this->driver->findElement(WebDriverBy::cssSelector(".table-row-edit"))->click();
		sleep(2);

		$input_elements = $this->driver->findElements(
			WebDriverBy::xpath("//input[@class = 'table-row-0']")
		);
		$expected_values = array();
		$index = 0;
		foreach ($input_elements as $input_element) {
			if (in_array($index, $ignore_indexs)) {
				continue;
			}

			$expected_value = $input_element->getAttribute("value") . "_edit";
			$input_element->clear();
			$input_element->sendKeys($expected_value);
			array_push($expected_values, $expected_value);
			$index++;
		}
		$this->save_edit();

		$text_table_elements = $this->get_table_row_text(0);

		foreach ($expected_values as $expected_value) {
			$this->assertContains($expected_value, $text_table_elements);
		}
	}

}
