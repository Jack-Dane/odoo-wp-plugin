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
		$this->wait_for_element_id("wp-admin-bar-wp-logo");
	}

	public function tearDown (): void {
		$this->driver->quit();
	}

	protected function wait_for_element_id ($id) {
		return $this->wait_for_element(WebDriverBy::id($id));
	}

	protected function wait_for_element ($element, $timeout=10) {
		return $this->wait_for_elements($element, $timeout)[0];
	}

	protected function wait_for_elements ($element, $timeout=10) {
		$found_elements = $this->driver->findElements($element);

		$attempts = 0;
		while (!$found_elements) {
			$attempts++;
			if ($attempts > $timeout) {
				throw new \Exception("Could not find element after " . $timeout . " seconds");
			}

			sleep(1);
			$found_elements = $this->driver->findElements($element); 
		}
		return $found_elements;
	}

}


class WordpressTableBase extends SeleniumBase {

	protected function get_table_row_text ($row_id) {
		$table_elements = $this->wait_for_table_row($row_id);
		$text_table_elements = array();
		foreach ($table_elements as $element) {
			array_push($text_table_elements, $element->getText());
		}
		return $text_table_elements;
	}

	private function wait_for_table_row ($row_id) {
		return $this->wait_for_elements(WebDriverBy::cssSelector(".table-row-" . $row_id));
	}
	
}

?> 