<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Exception\ElementClickInterceptedException;

class SendData_Test extends SeleniumBase
{

    public function test_send_data_to_odoo()
    {
        // 1. create a new post for to test the form
        // 1.1 get the short code to display on the post
        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=wpcf7");
        $contact_form_title = $this->wait_for_element(
            WebDriverBy::xpath("//a[@class='row-title'][text() = 'Test Contact Form']")
        )->click();
        $short_code = $this->wait_for_element(
            WebDriverBy::id("wpcf7-shortcode")
        )->getAttribute("value");

        // 1.2 add the new page with the short code
        $this->driver->get("http://localhost:8000/wp-admin/post-new.php");
        $count = 0;
        while ($count < 3) {
            $this->wait_for_element(
                WebDriverBy::xpath(
                    "//button[contains(@class, 'components-guide__forward-button')]"
                )
            )->click();
            $count += 1;
        }
        $this->driver->findElement(
            WebDriverBy::xpath(
                "//button[@class='components-button components-guide__finish-button']"
            )
        )->click();
        $this->wait_for_element(
            WebDriverBy::xpath(
                "//p[@aria-label='Add default block']"
            )
        )->click();
        $this->wait_for_element(
            WebDriverBy::xpath(
                "//p[@data-title='Paragraph']"
            )
        )->sendKeys($short_code);
        $this->driver->findElement(
            WebDriverBy::cssSelector(
                ".editor-post-publish-panel__toggle.editor-post-publish-button__button"
            )
        )->click();
        $this->wait_for_element(
            WebDriverBy::cssSelector(
                ".editor-post-publish-button.editor-post-publish-button__button"
            )
        )->click();

        // 2. open the post page with the form
        $this->wait_for_element(
            WebDriverBy::xpath(
                "//div[@class='components-panel__body post-publish-panel__postpublish-header is-opened']/a"
            )
        )->click();

        // 3. fill in the form
        $this->wait_for_element(WebDriverBy::name("your-name"))->sendKeys("test_name");
        $this->driver->findElement(
            WebDriverBy::name("your-email")
        )->sendKeys("email@email.com")->submit();

        // 4. check the odoo contact exists
        $this->driver->get("http://localhost:8069");
        $this->log_into_odoo();
        $this->odoo_click_on_app("Contacts");

        $kanban_contact = $this->wait_for_element(
            WebDriverBy::xpath("//span[text()='test_name']")
        );
        $kanban_contact->click();
        $contact_name = $this->wait_for_element(
            WebDriverBy::xpath("//span[@class='text-truncate']")
        );
        $this->driver->findElement(
            WebDriverBy::xpath("//a[@href='mailto:email@email.com']")
        );
        $this->driver->findElement(
            WebDriverBy::xpath("//a[@href='http://test.com']")
        );

        $this->assertNotEmpty($contact_name);

        $this->driver->findElement(
            WebDriverBy::name("internal_notes")
        )->click();
        $comment = $this->wait_for_element(
            WebDriverBy::xpath("//div[@id='comment']/p")
        );
        $this->assertEquals(
            "choice1", $comment->getText()
        );
    }

}

?>