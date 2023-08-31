<?php

// register styles & scripts used
add_action("admin_enqueue_scripts", __NAMESPACE__ . "callback_for_setting_up_scripts");

function callback_for_setting_up_scripts()
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

?>