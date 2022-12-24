<?php

use \PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

class SeleniumBase extends TestCase {

	public function setUp (): void {
		$serverUrl = "http://localhost:4444";
		$this->driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
		$this->driver->get("http://localhost:8000/wp-admin");

		$this->driver->findElement(WebDriverBy::id("user_login"))->sendKeys("admin");
		$this->driver->findElement(WebDriverBy::id("user_pass"))->sendKeys("password");
		$this->driver->findElement(WebDriverBy::id("wp-submit"))->click();

		// wait for the login to occur
		$this->wait_for_element("wp-admin-bar-wp-logo");
	}

	public function tearDown (): void {
		$this->driver->quit();
	}

	protected function get_table_row_text ($row_id) {
		$table_elements = $this->driver->findElements(WebDriverBy::cssSelector(".table-row-" . $row_id));
		$text_table_elements = array();
		foreach ($table_elements as $element) {
			array_push($text_table_elements, $element->getText());
		}
		return $text_table_elements;
	}

	protected function wait_for_element ($id) {
		$element = $this->driver->findElement(WebDriverBy::id($id));
		while (!$element) {
			sleep(1);
			$element = $this->driver->findElement(WebDriverBy::id($id));
		}
		return $element;
	}
	
}

?> 