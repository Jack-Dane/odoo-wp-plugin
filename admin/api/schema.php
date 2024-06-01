<?php

namespace odoo_conn\admin\api\endpoints;


function odoo_conn_base_get_request_arguments()
{
    return array(
        "limit" => array(
            "type" => "integer",
            "description" => esc_html__("The total number of Odoo forms returned in the API")
        ),
        "offset" => array(
            "type" => "integer",
            "description" => esc_html__("The offset based on the primary key")
        ),
    );
}

?>