<?php

// register styles & scripts used
add_action( "admin_enqueue_scripts", "callback_for_setting_up_scripts" );
function callback_for_setting_up_scripts() {
    wp_register_script(
    	"table-display", plugins_url("/odoo-conn/admin/php/pageHelpers/table_display.js"), array("jquery"), "1.0.0", true
    );
    wp_enqueue_script( "table-display" );

    wp_register_script( 
    	"table-editor", plugins_url("/odoo-conn/admin/php/pageHelpers/table_editor.js"), array("jquery"), "1.0.0", true 
    );
    wp_enqueue_script( "table-editor" );

    wp_register_script( 
    	"form-creator", plugins_url("/odoo-conn/admin/php/pageHelpers/form_creator_show.js"), array("jquery"), "1.0.0", true 
    );
    wp_enqueue_script( "form-creator" );

    wp_enqueue_style( "table-style", plugins_url("/odoo-conn/admin/php/pageHelpers/table_style.css") );
}

?>