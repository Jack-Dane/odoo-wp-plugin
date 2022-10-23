<?php 

define("ENCRYPTION_KEY_PATH", "odoo_conn.key");


function get_encryption_key () {
	$encryption_file = fopen( ENCRYPTION_KEY_PATH, "r" );
	if (!$encryption_file) {
		$encryption_file = fopen( ENCRYPTION_KEY_PATH, "w" );
		$encryption_key = sodium_crypto_secretbox_keygen();
		fwrite( $encryption_file, $encryption_key );
	} else {
		$encryption_key = fread( $encryption_file, filesize( ENCRYPTION_KEY_PATH ) );
	}
	fclose($encryption_file);
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

?>
