<div class="wrap">
    <form method="POST" action="?page=odoo-connection" id="form-data" class="submit-database">
        <?php wp_nonce_field(); ?>
        <input type="hidden" name="id" value="<?= $_REQUEST["id"] ?? "" ?>"/>

        <label for="name">Connection Name</label>
        <input type="text" name="name" id="name" value="<?= $odoo_conn_data->name ?? "" ?>"/><br/>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= $odoo_conn_data->username ?? "" ?>"/><br/>

        <?php if (!isset($_REQUEST["id"])) { ?>
        <label for="api_key">API Key</label>
        <input type="text" name="api_key" id="api_key"/><br/>
        <?php } ?>

        <label for="url">Odoo URL</label>
        <input type="text" name="url" id="url" value="<?= $odoo_conn_data->url ?? "" ?>"/><br/>
        <label for="database_name">Database Name</label>
        <input type="text" name="database_name" id="database_name" value="<?= $odoo_conn_data->database_name ?? "" ?>"/><br/>

        <div id="submit-wrapper">
            <input type="Submit" name="submit" class="button-primary"/>
        </div>
    </form>
</div>