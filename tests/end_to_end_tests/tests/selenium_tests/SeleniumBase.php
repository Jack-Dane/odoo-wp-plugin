<?php

use \PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Exception\NoSuchAlertException;

class SeleniumBase extends TestCase
{

    public function setUp(): void
    {
        $serverUrl = "http://localhost:4444";
        $this->driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
        $this->driver->get("http://localhost:8000/wp-admin");

        $this->driver->findElement(WebDriverBy::id("user_login"))->sendKeys("admin");
        $this->driver->findElement(WebDriverBy::id("user_pass"))->sendKeys("password");
        $this->driver->findElement(WebDriverBy::id("wp-submit"))->click();

        // wait for the login to occur
        $this->wait_for_element_id("wp-admin-bar-wp-logo");
    }

    public function tearDown(): void
    {
        $this->driver->quit();
    }

    protected function wait_for_element_id($id)
    {
        return $this->wait_for_element(WebDriverBy::id($id));
    }

    protected function wait_for_element($element, $timeout = 10)
    {
        return $this->wait_for_elements($element, $timeout)[0];
    }

    protected function wait_for_elements($element, $timeout = 10)
    {
        $found_elements = $this->driver->findElements($element);

        $attempts = 0;
        while (!$found_elements) {
            $attempts++;
            if ($attempts > $timeout) {
                throw new \Exception("Could not find element after $timeout seconds");
            }

            sleep(1);
            $found_elements = $this->driver->findElements($element);
        }
        return $found_elements;
    }

    protected function log_into_odoo()
    {
        $this->wait_for_element(WebDriverBy::xpath("//input[@id='login']"), 60)->sendKeys(
            "test@test.com"
        );
        $this->driver->findElement(WebDriverBy::xpath("//input[@id='password']"))->sendKeys(
            "password"
        )->submit();
    }

    protected function odoo_click_on_app($app_name)
    {
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

    protected function wait_for_alert()
    {
        $tries = 0;
        do {
            try {
                return $this->driver->switchTo()->alert()->getText();
            } catch (NoSuchAlertException $e) {
                $tries += 1;
            }
        } while ($tries < 10);
        throw $e;
    }

}


class WordpressTableBase extends SeleniumBase
{

    protected function get_table_row_text($row_id)
    {
        $table_elements = $this->wait_for_table_row($row_id);
        $text_table_elements = array();
        foreach ($table_elements as $element) {
            array_push($text_table_elements, $element->getText());
        }
        return $text_table_elements;
    }

    private function wait_for_table_row($row_id)
    {
        $row_id += 1;  // xpath indices start at 1
        $elements = $this->wait_for_elements(
            WebDriverBy::xpath("//tbody[@id='the-list']/tr[$row_id]/td")
        );
        // ignore the checkbox
//        array_shift($elements);
        return $elements;
    }

}

?> 