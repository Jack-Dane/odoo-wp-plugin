<?php

namespace odoo_conn\admin\pages;


abstract class OdooConnPageRouter
{

    public function __construct()
    {
        $this->table_display = $this->create_table_display();
    }

    private function valid_capability() {
        // Should be restricted by the menu choices.
        // Added as an extra security precaution.
        return current_user_can("administrator");
    }

    public function request()
    {
        if (!$this->valid_capability()) {
            echo "You are not authorised to view this page";
            return;
        };

        $action = $_REQUEST["page_action"] ?? null;
        $this->handle_route($action);

        if (!in_array(strtolower($action), $this->dont_display_table_actions(), true)) {
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
            check_admin_referer();
            $this->delete($_REQUEST["id"]);
        }
    }

    protected function display_table()
    {
        echo "<div class='wrap'>";
        $this->table_display->check_bulk_action();

        echo "<form method='post'>";
        $this->table_display->prepare_items();
        $this->table_display->display();
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
        parent::__construct();
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
        if ($action === "new") {
            $this->add_form_style();
            $this->display_input_form();
        } else if ($action === "edit") {
            $this->add_form_style();
            $this->display_edit_form($_REQUEST["id"]);
        } else {
            parent::handle_route($action);
        }
    }

    private function add_form_style()
    {
        wp_enqueue_style("odoo-form-page-style", plugins_url("form_style.css", __FILE__));
    }

    private function verify_nonce() {
        $action = ($_REQUEST["action"] ?? "") === "delete_bulk" ? "bulk-" . $this->table_display->_args["plural"] : -1;

        if (!wp_verify_nonce($_REQUEST["_wpnonce"], $action)) {
            die();
        }
    }

    protected function display_table()
    {
        $request_method = $_SERVER["REQUEST_METHOD"];
        $menu_page_slug = menu_page_url($this->menu_slug, false);

        if ($request_method == "POST") {
            $this->verify_nonce();

            if ($_REQUEST["id"]) {
                $this->update_record();
            } else {
                $this->create_new_record();
            }
        }

        $connection_url = esc_url(add_query_arg("page_action", "new", $menu_page_slug));
        echo "<a href='$connection_url' id='create-data' class='create-database-record button-primary'>Create a new record</a>";

        parent::display_table();
    }

    protected abstract function create_new_record();

    protected abstract function update_record();

    protected abstract function display_input_form();

    protected abstract function display_edit_form($id);

}
