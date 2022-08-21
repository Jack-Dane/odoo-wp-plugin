<?php 

include("menu_items.php");

include(__DIR__ . "/../api/endpoints.php");

add_action("admin_menu", "add_top_menu_page");

?>