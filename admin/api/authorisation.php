<?php

function is_authorised_to_request_data () {
	return current_user_can( "administrator" );
}

?>