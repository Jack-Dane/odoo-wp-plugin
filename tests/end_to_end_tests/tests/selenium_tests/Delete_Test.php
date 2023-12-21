<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Delete_Test extends WordpressTableBase
{

    public function test_delete()
    {
        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-connection");
        sleep(2);

        $this->show_action_buttons_on_table();
        $rows = $this->driver->findElements(WebDriverBy::xpath("//tbody[@id='the-list']/tr"));
        $this->assertCount(1, $rows);

        $this->wait_for_element(
            WebDriverBy::xpath(
                "//tbody[@id='the-list']/tr[1]/td[contains(@class, 'column-name')]/div[@class='row-actions']/span[@class='delete']/a"
            )
        )->click();
        sleep(2);
        $this->driver->findElement(WebDriverBy::xpath("//td[contains(text(), 'No items found')]"));

        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form");
        sleep(2);
        $this->driver->findElement(WebDriverBy::xpath("//td[contains(text(), 'No items found')]"));

        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping");
        sleep(2);
        $this->driver->findElement(WebDriverBy::xpath("//td[contains(text(), 'No items found')]"));
    }

    public function test_delete_errors() {
        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-submit-errors");

        $rows = $this->driver->findElements(
            WebDriverBy::xpath("//tbody[@id='the-list']/tr")
        );
        $this->assertCount(1, $rows);

        $this->show_action_buttons_on_table();

        sleep(2);
        $edit_button = $this->driver->findElements(
            WebDriverBy::xpath(
                "//tbody[@id='the-list']/tr[1]/td[contains(@class, 'column-contact_7_title')]/div[@class='row-actions']/span[@class='edit']/a"
            )
        );
        $this->assertCount(0, $edit_button);

        $this->wait_for_element(
            WebDriverBy::xpath(
                "//tbody[@id='the-list']/tr[1]/td[contains(@class, 'column-contact_7_title')]/div[@class='row-actions']/span[@class='delete']/a"
            )
        )->click();
        sleep(2);

        $this->driver->findElement(WebDriverBy::xpath("//td[contains(text(), 'No items found')]"));
    }

}
