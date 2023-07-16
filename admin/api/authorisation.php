<?php

namespace odoo_conn\admin\api\endpoints;


function odoo_conn_is_authorised_to_request_data()
{
    return current_user_can("administrator");
}

?>