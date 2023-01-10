<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Update_Test extends WordpressTableBase {

	public function test_update_connection () {
		$this->update_row("http://localhost:8000/wp-admin/admin.php?page=odoo-connection", array());
	}

	public function test_update_form () {
		$this->update_row("http://localhost:8000/wp-admin/admin.php?page=odoo-form", array());
	}

	public function test_update_form_mapping_constant_value () {
		$this->update_row(
			"http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping", array(0)
		);
	}

	public function test_update_form_mapping_cf7_field_name () {
		$this->update_row(
			"http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping", array(2), $row=1
		);
	}

	public function test_update_with_both_constant_value_cf7_field_name () {
		$this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping");

		$this->wait_for_element(WebDriverBy::cssSelector(".table-row-edit"))->click();
		sleep(2);

		$input_elements = $this->driver->findElements(
			WebDriverBy::xpath("//input[@class='table-row-0']")
		);

		$input_elements[0]->sendKeys("Broken");

		$this->save_edit();

		$alertText = $this->driver->switchTo()->alert()->getText();
		$this->assertEquals(
			"Both cf7 field name and constant value passed, only one is expected", $alertText
		);
		$this->driver->switchTo()->alert()->accept();

		$input_elements = $this->driver->findElements(
			WebDriverBy::xpath("//input[@class='table-row-0']")
		);
		
		$text_table_elements = $this->get_table_row_text(0);
		foreach ($text_table_elements as $text_table_element) {
			$this->assertNotEquals("broken", $text_table_element);
		}
	}

	private function save_edit ($row=0) {
		$save_button = $this->driver->findElements(WebDriverBy::cssSelector(".table-row-save"))[$row];
		// sometimes not in view
		$this->driver->executeScript("arguments[0].click();", array($save_button));
		sleep(2);
	}

	private function update_row ($edit_endpoint, $ignore_indexs, $row=0) {
		$this->driver->get($edit_endpoint);

		$this->wait_for_elements(WebDriverBy::cssSelector(".table-row-edit"))[$row]->click();
		sleep(2);

		$input_elements = $this->driver->findElements(
			WebDriverBy::xpath("//input[@class='table-row-" . $row . "']")
		);
		$expected_values = array();
		$index = -1;
		foreach ($input_elements as $input_element) {
			$index++;
			if (in_array($index, $ignore_indexs)) {
				continue;
			}

			$expected_value = $input_element->getAttribute("value") . "_edit";
			$input_element->clear();
			$input_element->sendKeys($expected_value);
			array_push($expected_values, $expected_value);
		}
		$this->save_edit($row);

		$text_table_elements = $this->get_table_row_text($row);

		foreach ($expected_values as $expected_value) {
			$this->assertContains($expected_value, $text_table_elements);
		}
	}

}
