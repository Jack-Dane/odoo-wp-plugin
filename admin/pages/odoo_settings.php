<?php

function odoo_settings()
{

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        odoo_conn\encryption\odoo_conn_refresh_encryption_key();
        ?>
        <p>Refreshed the Encryption Key</p>
        <?php
    }
    ?>
    <form method="POST">
        <p><input id="" type="submit" class="button button-primary" value="Refresh Encryption Key"/></p>
    </form>
    <p>The encryption key is used to secure the api keys in the database. Refresh the Encryption Key if you think it has
        been compromised</p>
    <p><b style="color: red;">Warning, this will remove all your Odoo connection, Odoo forms and field mappings from the
            database. </b></p>
    <?php
}

?>