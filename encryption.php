<?php 

namespace odoo_conn\encryption;

define("ENCRYPTION_KEY_PATH", ABSPATH . "odoo_conn.key");


function generate_encryption_key () {
	$encryption_file = fopen( ENCRYPTION_KEY_PATH, "w" );

	$timeout = 10;  // seconds
	$attempts  = 0;
	$lock_timedout = false;
	while ( !flock($encryption_file, LOCK_EX | LOCK_NB, $would_block ) ) {
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
		$encryption_key = sodium_crypto_secretbox_keygen();
		fwrite( $encryption_file, $encryption_key );
		fclose( $encryption_file );
	} catch (\Exception $e) {
		return false;
	} finally {
		flock($encryption_file, LOCK_UN);
	}
	return $encryption_key;
}

function get_encryption_key () {
	$encryption_key_exists = file_exists( ENCRYPTION_KEY_PATH );

	if (!$encryption_key_exists) {
		$encryption_key = generate_encryption_key();
	} else {
		$encryption_file = fopen( ENCRYPTION_KEY_PATH, "r" );
		$encryption_key = fread( $encryption_file, filesize( ENCRYPTION_KEY_PATH ) );
		fclose( $encryption_file );
	}

	return $encryption_key;
}

function encrypt_data ($data) {
	$encryption_key = get_encryption_key();

	$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$encrypted_data = sodium_crypto_secretbox( $data, $nonce, $encryption_key );

	return base64_encode( $nonce . $encrypted_data );
}

function decrypt_data ($encrypted_data) {
	$encryption_key = get_encryption_key();
	
	$decoded_data = base64_decode( $encrypted_data );
	$nonce = mb_substr( $decoded_data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, "8bit" );
	$encrypted_result = mb_substr( $decoded_data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, "8bit" );
	
	$decrypted = sodium_crypto_secretbox_open( $encrypted_result, $nonce, $encryption_key );
	error_log($decrypted);
	return $decrypted;
}

function refresh_encryption_key () {
	global $wpdb, $table_prefix;

	generate_encryption_key();

	// remove all connections as the api_key can no longer be decrypted
	// keys should only be refreshed when we think it has been leaked
	// so the api keys in Odoo should be changed making this data redundant
	$wpdb->query("DELETE FROM {$table_prefix}odoo_conn_connection");
}

?>
