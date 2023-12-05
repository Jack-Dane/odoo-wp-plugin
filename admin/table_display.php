<?php

namespace odoo_conn\admin\table_display;


use WP_List_Table;


if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class OdooConnCustomTableDeletableDisplay extends WP_List_Table {

    protected $get_backend;
    protected $delete_backend;
    private int $per_page = 10;

    public function __construct($get_backend, $delete_backend, $args = array())
    {
        parent::__construct($args);

        $this->get_backend = $get_backend;
        $this->delete_backend = $delete_backend;
    }

    protected function row_action_buttons($item)
    {
        return array(
            "delete" => "<a href='?page=${_REQUEST["page"]}&id=${item["id"]}&page_action=delete'>Delete</a>"
        );
    }

    public function get_bulk_actions()
    {
        return array(
            "delete_bulk" => "Delete"
        );
    }

    public function check_bulk_action()
    {
        $current_action = $this->current_action();
        if ($current_action === "delete_bulk") {
            $ids = $_REQUEST["element"];

            foreach ($ids as $id) {
                $this->delete_backend->request(
                    array(
                        "id" => $id
                    )
                );
            }
        }
    }

    private function get_table_data()
    {
        $offset = ($this->get_pagenum() - 1) * $this->per_page;

        $filter_data = [
            "offset" => $offset,
            "limit" => $this->per_page
        ];

        return $this->get_backend->request($filter_data);
    }

    private function total_records()
    {
        return $this->get_backend->count_records();
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
            'per_page' => $this->per_page,
            'total_pages' => ceil($total_records / $this->per_page)
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


class OdooConnCustomTableEditableDisplay extends OdooConnCustomTableDeletableDisplay
{

    protected function row_action_buttons($item)
    {
        return array_merge(
            parent::row_action_buttons($item),
            array(
                "edit" => "<a href='?page=${_REQUEST["page"]}&id=${item["id"]}&page_action=edit'>Edit</a>"
            )
        );
    }

}


?>