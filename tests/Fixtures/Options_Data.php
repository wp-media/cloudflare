<?php

namespace WP_Rocket\Admin;

// Bail out if this package exists.
if ( class_exists( 'WP_Rocket\Admin\Options_Data' ) ) {
	return;
}

class Options_Data {

	public function get( $key, $default = '' ) {
		return $default;
	}
}
