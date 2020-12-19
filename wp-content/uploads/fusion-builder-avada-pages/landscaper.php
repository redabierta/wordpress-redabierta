<?php

		// Exit if accessed directly
		if ( ! defined( 'ABSPATH' ) ) {
			exit;
		}

		function fusion_builder_add_landscaper_demo( $demos ) {

		$demos['landscaper'] = array (
  'category' => 'Avada Landscaper',
  'pages' => 
  array (
  ),
);

			return $demos;
		}
		add_filter( 'fusion_builder_get_demo_pages', 'fusion_builder_add_landscaper_demo' );