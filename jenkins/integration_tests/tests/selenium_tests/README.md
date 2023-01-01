# Running Selenium Tests

These tests to ensure that creating different table rows for connections, forms and form_mappings. They also test that data is actually sent from WordPress and received by Odoo.  

The Selenium tests should be run in the following order for them to work: 

1. Create_Test.php
2. SendData_Test.php
3. Update_Test.php
4. Delete_Test.php

Otherwise the update tests won't have data to update etc. 