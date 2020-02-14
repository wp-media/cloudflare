<?php

namespace WPMedia\Cloudflare\Tests\Integration;

define( 'WPMEDIA_IS_TESTING', true );

tests_add_filter(
	'muplugins_loaded',
	function() {
		getFactory();
	}
);

function getFactory() {
	static $factory;

	if ( ! $factory ) {
		$factory = new Factory();
	}

	return $factory;
}
