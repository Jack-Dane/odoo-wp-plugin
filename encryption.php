<?php

namespace odoo_conn\encryption;


class OdooConnEncryptionFileHandler
{

    public function __construct()
    {
        $this->encryption_key_path = plugin_dir_path(__FILE__) . "odoo_conn.key";
    }

    public function write_to_file($api_key)
    {
        $encryption_file = fopen($this->encryption_key_path, "w");

        $timeout = 10;  // seconds
        $attempts = 0;
        while (!flock($encryption_file, LOCK_EX | LOCK_NB, $would_block)) {
            if ($attempts < $timeout) {
                $attempts++;
                sleep(1);
            } else {
                if ($would_block) {
                    // this should never happen, but it is better to raise an
                    // exception in Wordpress, rather than timeout the process
                    throw new \Exception("Timed out waiting to write to the key file");
                } else {
                    break;
                }
            }
        }

        try {
            fwrite($encryption_file, $api_key);
            fclose($encryption_file);
        } finally {
            // https://github.com/Jack-Dane/odoo-wp-plugin/issues/2
            // Sometimes (Integration test) the encryption_file isn't seen as a resource to flock
            // add this check to avoid 500 errors when initially creating data in WordPress
            // until a better solution is found, this will do.
            if (is_resource($encryption_file)) {
                flock($encryption_file, LOCK_UN);
            }
        }
    }

    public function read_from_file()
    {
        $encryption_key_exists = file_exists($this->encryption_key_path);

        if (!$encryption_key_exists) {
            $encryption_key = null;
        } else {
            $encryption_file = fopen($this->encryption_key_path, "r");
            $encryption_key = fread($encryption_file, filesize($this->encryption_key_path));
            fclose($encryption_file);
        }

        return $encryption_key;
    }

}


class OdooConnEncryptionHandler
{

    public function __construct($file_handler)
    {
        $this->file_handler = $file_handler;
    }

    public function refresh_key()
    {
        $encryption_key = $this->generate_new_key();
        $this->file_handler->write_to_file($encryption_key);
    }

    public function encrypt($data)
    {
        $encryption_key = $this->get_encryption_key();

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted_data = sodium_crypto_secretbox($data, $nonce, $encryption_key);

        return base64_encode($nonce . $encrypted_data);
    }

    public function decrypt($encrypted_data)
    {
        $encryption_key = $this->get_encryption_key();

        $decoded_data = base64_decode($encrypted_data);
        $nonce = mb_substr($decoded_data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, "8bit");
        $encrypted_result = mb_substr(
            $decoded_data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, "8bit"
        );

        $decrypted = sodium_crypto_secretbox_open(
            $encrypted_result, $nonce, $encryption_key
        );
        return $decrypted;
    }

    private function generate_new_key()
    {
        return sodium_crypto_secretbox_keygen();
    }

    public function get_encryption_key()
    {
        $encryption_key = $this->file_handler->read_from_file();

        if (is_null($encryption_key)) {
            $encryption_key = $this->generate_new_key();
            $this->file_handler->write_to_file($encryption_key);
        }

        return $encryption_key;
    }

}


function odoo_conn_refresh_encryption_key()
{
    global $wpdb, $table_prefix;

    $odoo_conn_file_hanlder = new OdooConnEncryptionFileHandler();
    $odoo_conn_encryption_handler = new OdooConnEncryptionHandler($odoo_conn_file_hanlder);
    $odoo_conn_encryption_handler->refresh_key();

    // remove all connections as the api_key can no longer be decrypted
    // keys should only be refreshed when we think it has been leaked
    // so the api keys in Odoo should be changed making this data redundant
    $wpdb->query("DELETE FROM {$table_prefix}odoo_conn_connection");
}

?>
