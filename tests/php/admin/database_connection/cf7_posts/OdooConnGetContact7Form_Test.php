<?php

namespace php\admin\database_connection\cf7_posts;

require_once __DIR__ . '/../../../TestClassBrainMonkey.php';

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use odoo_conn\admin\database_connection\OdooConnGetContact7Form;
use PHPUnit\Framework\TestCase;


class OdooConnGetContact7Form_Test extends TestCase
{

    use MockeryPHPUnitIntegration;

    function setUp(): void
    {
        require_once __DIR__ . '/../../../../../admin/database_connection/main.php';
    }

    public function test_ok()
    {
        $wpdb = \Mockery::mock('WPDB');
        $wpdb->posts = 'wp_posts';
        $wpdb->shouldReceive('prepare')->with(
            'SELECT ID, post_title FROM wp_posts WHERE post_type=%s ORDER BY wp_posts.ID DESC',
            ['wpcf7_contact_form']
        )->once()->andReturn(
            'SELECT ID, post_title FROM wp_posts WHERE post_type="wpcf7_contact_form" ORDER BY wp_posts.ID DESC'
        );
        $wpdb->shouldReceive('get_results')->with(
            'SELECT ID, post_title FROM wp_posts WHERE post_type="wpcf7_contact_form" ORDER BY wp_posts.ID DESC',
            'OBJECT'
        )->once()->andReturn(
            [['ID' => 4, 'post_title' => 'Title']]
        );
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['table_prefix'] = 'wp_';

		$get_contact_7_form = new OdooConnGetContact7Form();
		$response = $get_contact_7_form->request([]);

        $this->assertEquals([['ID' => 4, 'post_title' => 'Title']], $response);
    }
}

?>