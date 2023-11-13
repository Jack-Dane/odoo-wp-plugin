<?php

abstract class OdooConnPageRouter
{

    public function request()
    {
        $action = $_REQUEST["page_action"] ?? null;
        $this->handle_route($action);

        if (!in_array($action, $this->dont_display_table_actions())) {
            $this->display_table();
        }
    }

    protected function dont_display_table_actions()
    {
        return [];
    }

    protected function handle_route($action)
    {
        if ($action === "delete") {
            $this->delete($_REQUEST["id"]);
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

    protected abstract function delete($id);

}


abstract class OdooConnPageRouterCreate extends OdooConnPageRouter
{

    private string $menu_slug;

    public function __construct($menu_slug)
    {
        $this->menu_slug = $menu_slug;
    }

    protected function dont_display_table_actions()
    {
        return array_merge(
            parent::dont_display_table_actions(),
            [
                "edit", "new"
            ]
        );
    }

    protected function handle_route($action)
    {
        parent::handle_route($action);

        if ($action === "new") {
            $this->add_form_style();
            $this->display_input_form();
        } else if ($action === "edit") {
            $this->add_form_style();
            $this->display_edit_form($_REQUEST["id"]);
        }
    }

    private function add_form_style()
    {
        wp_enqueue_style("odoo-form-page-style", plugins_url("form_style.css", __FILE__));
    }

    protected function display_table()
    {
        $request_method = $_SERVER["REQUEST_METHOD"];
        $menu_page_slug = menu_page_url($this->menu_slug, false);

        if ($request_method == "POST") {
            if ($_REQUEST["id"]) {
                $this->update_record();
            } else {
                $this->create_new_record();
            }
        }

        $connection_url = add_query_arg("page_action", "new", $menu_page_slug);
        echo "<a href='$connection_url' id='create-data' class='create-database-record button-primary'>Create a new record</a>";

        parent::display_table();
    }

    protected abstract function create_new_record();

    protected abstract function update_record();

    protected abstract function display_input_form();

    protected abstract function display_edit_form($id);

}
