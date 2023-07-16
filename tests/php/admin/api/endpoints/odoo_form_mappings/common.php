<?php

namespace {

    class WP_Error
    {

        public function __construct($error_id, $error_message, $error_status)
        {
            $this->error_id = $error_id;
            $this->error_message = $error_message;
            $this->error_status = $error_status;
        }

    }

}

?>