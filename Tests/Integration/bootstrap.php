<?php

namespace WPMedia\Cloudflare\Tests\Integration;

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
