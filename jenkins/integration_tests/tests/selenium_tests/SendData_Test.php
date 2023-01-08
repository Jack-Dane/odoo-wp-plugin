<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Exception\ElementClickInterceptedException;

class SendData_Test extends SeleniumBase {

	private function odoo_click_on_app ($app_name) {
		$this->wait_for_element(WebDriverBy::cssSelector(".oi.oi-apps"))->click();
		$apps = $this->wait_for_elements(WebDriverBy::cssSelector(".dropdown-item.o_app"));
		foreach ($apps as $app) {
			if (trim($app->getText()) == $app_name) {
				$app->click();
				break;
			}
		}
		sleep(2);
	}

	public function test_send_data_to_odoo () {
		// 1. Odoo
		// 1.1 install the Odoo database
		$this->driver->get("http://localhost:8069");
		$this->wait_for_element(WebDriverBy::name("master_pwd"))->clear()->sendKeys("master_pwd");
		$this->driver->findElement(WebDriverBy::id("dbname"))->sendKeys("odoo");
		$this->driver->findElement(WebDriverBy::id("login"))->sendKeys("test@test.com");
		$this->driver->findElement(WebDriverBy::id("password"))->sendKeys("password")->submit();

		// 1.2 log into Odoo
		$this->wait_for_element(WebDriverBy::xpath("//input[@id='login']"), 60)->sendKeys(
			"test@test.com"
		);
		$this->driver->findElement(WebDriverBy::xpath("//input[@id='password']"))->sendKeys(
			"password"
		)->submit();
		$this->wait_for_element(WebDriverBy::cssSelector(".oi.oi-apps"), 20);

		// 1.3 install the contacts app
		$this->driver->get("http://localhost:8069");

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

		// 2. create a new post for to test the form
		// 2.1 get the short code to display on the post
		$this->driver->get("http://localhost:8000/wp-admin/admin.php?page=wpcf7");
		$contact_form_title = $this->wait_for_element(
			WebDriverBy::xpath("//a[@class='row-title'][text() = 'Test Contact Form']")
		)->click();
		$short_code = $this->wait_for_element(
			WebDriverBy::id("wpcf7-shortcode")
		)->getAttribute("value");

		// 2.2 add the new page with the short code
		$this->driver->get("http://localhost:8000/wp-admin/post-new.php");
		try {
			$this->driver->findElement(
				WebDriverBy::cssSelector(".block-editor-default-block-appender__content")
			)->click();
		} catch (ElementClickInterceptedException $e) {
			$this->driver->findElement(
				WebDriverBy::xpath("//button[@aria-label='Close dialog']")
			)->click();
			sleep(1);
			$this->driver->findElement(
				WebDriverBy::cssSelector(".block-editor-default-block-appender__content")
			)->click();
		}
		$this->driver->getKeyboard()->sendKeys($short_code);
		$this->driver->findElement(
			WebDriverBy::cssSelector(
				".editor-post-publish-panel__toggle.editor-post-publish-button__button"
			)
		)->click();
		$this->driver->findElement(
			WebDriverBy::cssSelector(
				".editor-post-publish-button.editor-post-publish-button__button"
			)
		)->click();

		// 3. open the post page with the form
		$this->wait_for_element(
			WebDriverBy::xpath(
				"//div[@class='components-panel__body post-publish-panel__postpublish-header is-opened']/a"
			)
		)->click();

		// 4. fill in the form
		$this->wait_for_element(WebDriverBy::name("your-name"))->sendKeys("test_name");
		$this->driver->findElement(
			WebDriverBy::name("your-email")
		)->sendKeys("email@email.com")->submit();

		// 5. check the odoo contact exists
		$this->driver->get("http://localhost:8069");
		
		$this->odoo_click_on_app("Contacts");

		$kanban_contact = $this->wait_for_element(
			WebDriverBy::xpath("//span[text() = 'test_name']")
		);
		$kanban_contact->click();
		$contact_name = $this->wait_for_element(
			WebDriverBy::xpath("//span[@class='text-truncate']")
		);
		$contact_email = $this->driver->findElement(
			WebDriverBy::xpath("//a[@href='mailto:email@email.com']")
		);
		$contact_website = $this->driver->findElements(
			WebDriverBy::xpath("//a[@href='http://test.com']")
		);

		$this->assertNotEmpty($contact_website);
		$this->assertNotEmpty($contact_email);
		$this->assertNotEmpty($contact_name);
	}

}

?>