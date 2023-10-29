<?php


abstract class OdooConnPageRouter
{

    public function request()
    {
        $action = $_REQUEST["page_action"];

        if (!isset($action) || $action == "list") {
            $this->display_table();
        } else if ($action == "new") {
            $this->display_input_form();
        } else if ($action == "edit") {
            echo "edit";
        } else {
            // TODO - 404
        }
    }

    protected function display_table()
    {
        echo "<div class='wrap'>";
        $table_display = $this->create_table_display();
        $table_display->check_bulk_action();

        echo "<form method='post'>";
        $table_display->prepare_items();
        $table_display->display();
        echo "</form></div>";
    }

    protected abstract function create_table_display();

}


abstract class OdooConnPageRouterCreate extends OdooConnPageRouter
{

    private string $menu_slug;

    public function __construct($menu_slug)
    {
        $this->menu_slug = $menu_slug;
    }

    protected function display_table()
    {
        $request_method = $_SERVER["REQUEST_METHOD"];
        $menu_page_slug = menu_page_url($this->menu_slug, false);

        if ($request_method == "POST") {
            // new record create request
            $this->create_new_record();
        }

        $connection_url = add_query_arg("page_action", "new", $menu_page_slug);
        echo "<a href='$connection_url' id='create-data' class='create-database-record button-primary'>Create a new record</a>";

        parent::display_table();
    }

    protected abstract function create_new_record();

    protected abstract function display_input_form();

}
