<?php

require_once(__DIR__ . "/SeleniumBase.php");

use Facebook\WebDriver\WebDriverBy;

class Delete_Test extends WordpressTableBase
{

    public function test_delete()
    {
        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-connection");
        sleep(2);

        $rows = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
        $this->assertEquals(1, count($rows));

        $this->driver->findElement(WebDriverBy::cssSelector(".table-row-delete"))->click();
        sleep(2);

        $rows = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
        $this->assertEquals(0, count($rows));

        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form");
        sleep(2);
        $rows = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
        $this->assertEquals(0, count($rows));

        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-form-mapping");
        sleep(2);
        $rows = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
        $this->assertEquals(0, count($rows));

        $this->driver->get("http://localhost:8000/wp-admin/admin.php?page=odoo-submit-errors");
        $delete_button = $this->wait_for_element(
            WebDriverBy::cssSelector(".table-row-delete")
        );
        $edit_buttons = $this->driver->findElements(
            WebDriverBy::cssSelector(".table-row-edit")
        );
        $this->assertEquals(0, count($edit_buttons));
        $delete_button->click();
        sleep(2);
        $rows = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
        $this->assertEquals(0, count($rows));
    }

}
