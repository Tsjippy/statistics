<?php
namespace SIM\STATISTICS;
use SIM;

add_action('init', __NAMESPACE__.'\blockInit');
function blockInit() {
	register_block_type(
		__DIR__ . '/statistics/build',
		array(
			'render_callback' => __NAMESPACE__.'\statisticsWidget',
		)
	);
}