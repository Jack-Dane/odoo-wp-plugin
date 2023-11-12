<div class="wrap">
    <form method="POST" action="?page=odoo-connection" id="form-data" class="submit-database">
        <input type="hidden" name="id" value="<?= $_REQUEST["id"] ?? "" ?>"/>

        <input type="text" name="name" id="name" placeholder="Name" value="<?= $odoo_conn_data->name ?? "" ?>"/><br/>
        <input type="text" name="username" id="username" placeholder="Username" value="<?= $odoo_conn_data->username ?? "" ?>"/><br/>

        <?php if (!isset($_REQUEST["id"])) { ?>
        <input type="text" name="api_key" id="api_key" placeholder="API Key"/><br/>
        <?php } ?>

        <input type="text" name="url" id="url" placeholder="URL" value="<?= $odoo_conn_data->url ?? "" ?>"/><br/>
        <input type="text" name="database_name" id="database_name" placeholder="Database Name" value="<?= $odoo_conn_data->database_name ?? "" ?>"/><br/>
        <input type="Submit" name="submit" class="button-primary"/>
    </form>
</div>