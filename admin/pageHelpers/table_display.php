<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// register styles & scripts used
add_action("admin_enqueue_scripts", __NAMESPACE__ . "odoo_conn_page_scripts_callback");

function odoo_conn_page_scripts_callback()
{
    $root = esc_url_raw(rest_url());
    $nonce = wp_create_nonce("wp_rest");
    wp_register_script(
        "table-display", plugins_url("table_display.js", __FILE__), array("jquery"), "1.0.1", true
    );
    wp_localize_script("table-display", "wpApiSettings", array(
        "root" => $root, "nonce" => $nonce
    ));
    wp_enqueue_script("table-display");

    wp_register_script(
        "form-creator", plugins_url("form_creator_show.js", __FILE__), array("jquery"), "1.0.0", true
    );
    wp_localize_script("table-editor", "wpApiSettings", array(
        "root" => $root, "nonce" => $nonce
    ));
    wp_enqueue_script("form-creator");

    wp_enqueue_style("odoo-page-style", plugins_url("page_style.css", __FILE__));
}


class OdooConnCustomTableDisplay extends WP_List_Table
{

    protected $table_backend;

    private int $per_page = 10;

    public function __construct($table_backend, $args = array())
    {
        parent::__construct($args);

        $this->table_backend = $table_backend;
    }

    private function get_table_data()
    {
        $offset = ($this->get_pagenum() - 1) * $this->per_page;

        $filter_data = [
            "offset" => $offset,
            "limit" => $this->per_page
        ];

        return $this->table_backend->request($filter_data);
    }

    private function total_records() {
        return $this->table_backend->count_records();
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $primary = 'name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        $table_data = $this->get_table_data();
        $total_records = $this->total_records();

        $this->set_pagination_args(array(
            'total_items' => $total_records,
            'per_page'    => $this->per_page,
            'total_pages' => ceil( $total_records / $this->per_page )
        ));

        $this->items = $table_data;
    }

    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    public function column_cb($item)
    {
        return "<input type='checkbox' name='element[]' value='{$item['id']}' />";
    }

}


?>