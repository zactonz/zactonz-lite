<?php
/**
 * Plugin Name: Zactonz Lite benchmark fixture
 *
 * Stands in for the plugins a real site runs. Almost no site is as bare as a fresh
 * install, and a theme that only looks light next to an empty page is not telling
 * anyone anything useful. Enqueues jQuery, which most plugins still pull in, and one
 * external script of its own, so every theme is measured carrying the same load.
 *
 * Installed and removed by tools/benchmark.php when ZL_WITH_PLUGIN=1.
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script(
			'zl-benchmark-fixture',
			includes_url( 'js/wp-util.min.js' ),
			array( 'jquery' ),
			'1.0.0',
			true
		);
	}
);
