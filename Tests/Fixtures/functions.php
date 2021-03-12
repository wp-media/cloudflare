<?php

function domain_mapping_siteurl( $setting ) {
	global $wpdb, $current_blog;

	// To reduce the number of database queries, save the results the first time we encounter each blog ID.
	static $return_url = array();

	$wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';

	if ( !isset( $return_url[ $wpdb->blogid ] ) ) {
		$s = $wpdb->suppress_errors();

		if ( get_site_option( 'dm_no_primary_domain' ) == 1 ) {
			$domain = $wpdb->get_var( "SELECT domain FROM {$wpdb->dmtable} WHERE blog_id = '{$wpdb->blogid}' AND domain = '" . $wpdb->escape( $_SERVER[ 'HTTP_HOST' ] ) . "' LIMIT 1" );
			if ( null == $domain ) {
				$return_url[ $wpdb->blogid ] = untrailingslashit( get_original_url( "siteurl" ) );
				return $return_url[ $wpdb->blogid ];
			}
		} else {
			// get primary domain, if we don't have one then return original url.
			$domain = $wpdb->get_var( "SELECT domain FROM {$wpdb->dmtable} WHERE blog_id = '{$wpdb->blogid}' AND active = 1 LIMIT 1" );
			if ( null == $domain ) {
				$return_url[ $wpdb->blogid ] = untrailingslashit( get_original_url( "siteurl" ) );
				return $return_url[ $wpdb->blogid ];
			}
		}

		$wpdb->suppress_errors( $s );
		if ( false == isset( $_SERVER[ 'HTTPS' ] ) )
			$_SERVER[ 'HTTPS' ] = 'Off';
		$protocol = ( 'on' == strtolower( $_SERVER[ 'HTTPS' ] ) ) ? 'https://' : 'http://';
		if ( $domain ) {
			$return_url[ $wpdb->blogid ] = untrailingslashit( $protocol . $domain  );
			$setting = $return_url[ $wpdb->blogid ];
		} else {
			$return_url[ $wpdb->blogid ] = false;
		}
	} elseif ( $return_url[ $wpdb->blogid ] !== FALSE) {
		$setting = $return_url[ $wpdb->blogid ];
	}

	return $setting;
}

/**
 * Gets the constant is defined.
 *
 * NOTE: This function allows mocking constants when testing.
 *
 * @since 3.5
 *
 * @param string     $constant_name Name of the constant to check.
 * @param mixed|null $default Optional. Default value to return if constant is not defined.
 *
 * @return bool true when constant is defined; else, false.
 */
function rocket_get_constant( $constant_name, $default = null ) {
	if ( ! rocket_has_constant( $constant_name ) ) {
		return $default;
	}

	return constant( $constant_name );
}

/**
 * Get home URL of a specific lang.
 *
 * @since 2.2
 *
 * @param  string $lang The language code. Default is an empty string.
 * @return string $url
 */
function get_rocket_i18n_home_url( $lang = '' ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	$i18n_plugin = rocket_has_i18n();

	if ( ! $i18n_plugin ) {
		return home_url();
	}

	switch ( $i18n_plugin ) {
		// WPML.
		case 'wpml':
			return $GLOBALS['sitepress']->language_url( $lang );
		// qTranslate.
		case 'qtranslate':
			return qtrans_convertURL( home_url(), $lang, true );
		// qTranslate-x.
		case 'qtranslate-x':
			return qtranxf_convertURL( home_url(), $lang, true );
		// Polylang, Polylang Pro.
		case 'polylang':
			$pll = function_exists( 'PLL' ) ? PLL() : $GLOBALS['polylang'];

			if ( ! empty( $pll->options['force_lang'] ) && isset( $pll->links ) ) {
				return pll_home_url( $lang );
			}
	}

	return home_url();
}

// Get the ipv6 full format and return it as a decimal value.
function get_rocket_ipv6_full($ip) {
	$pieces = explode ("/", $ip, 2);
	$left_piece = $pieces[0];
	$right_piece = null;
	if (count($pieces) > 1) $right_piece = $pieces[1];

	// Extract out the main IP pieces
	$ip_pieces = explode("::", $left_piece, 2);
	$main_ip_piece = $ip_pieces[0];
	$last_ip_piece = null;
	if (count($ip_pieces) > 1) $last_ip_piece = $ip_pieces[1];

	// Pad out the shorthand entries.
	$main_ip_pieces = explode(":", $main_ip_piece);
	foreach($main_ip_pieces as $key=>$val) {
		$main_ip_pieces[$key] = str_pad($main_ip_pieces[$key], 4, "0", STR_PAD_LEFT);
	}

	// Check to see if the last IP block (part after ::) is set
	$last_piece = "";
	$size = count($main_ip_pieces);
	if (trim($last_ip_piece) != "") {
		$last_piece = str_pad($last_ip_piece, 4, "0", STR_PAD_LEFT);

		// Build the full form of the IPV6 address considering the last IP block set
		for ($i = $size; $i < 7; $i++) {
			$main_ip_pieces[$i] = "0000";
		}
		$main_ip_pieces[7] = $last_piece;
	}
	else {
		// Build the full form of the IPV6 address
		for ($i = $size; $i < 8; $i++) {
			$main_ip_pieces[$i] = "0000";
		}
	}

	// Rebuild the final long form IPV6 address
	$final_ip = implode(":", $main_ip_pieces);

	return rocket_ip2long6($final_ip);
}

// Determine whether the IPV6 address is within range.
// $ip is the IPV6 address in decimal format to check if its within the IP range created by the cloudflare IPV6 address, $range_ip.
// $ip and $range_ip are converted to full IPV6 format.
// Returns true if the IPV6 address, $ip,  is within the range from $range_ip.  False otherwise.
function rocket_ipv6_in_range($ip, $range_ip) {
	$pieces = explode ("/", $range_ip, 2);
	$left_piece = $pieces[0];
	$right_piece = $pieces[1];

	// Extract out the main IP pieces
	$ip_pieces = explode("::", $left_piece, 2);
	$main_ip_piece = $ip_pieces[0];
	$last_ip_piece = $ip_pieces[1];

	// Pad out the shorthand entries.
	$main_ip_pieces = explode(":", $main_ip_piece);
	foreach($main_ip_pieces as $key=>$val) {
		$main_ip_pieces[$key] = str_pad($main_ip_pieces[$key], 4, "0", STR_PAD_LEFT);
	}

	// Create the first and last pieces that will denote the IPV6 range.
	$first = $main_ip_pieces;
	$last = $main_ip_pieces;

	// Check to see if the last IP block (part after ::) is set
	$last_piece = "";
	$size = count($main_ip_pieces);
	if (trim($last_ip_piece) != "") {
		$last_piece = str_pad($last_ip_piece, 4, "0", STR_PAD_LEFT);

		// Build the full form of the IPV6 address considering the last IP block set
		for ($i = $size; $i < 7; $i++) {
			$first[$i] = "0000";
			$last[$i] = "ffff";
		}
		$main_ip_pieces[7] = $last_piece;
	}
	else {
		// Build the full form of the IPV6 address
		for ($i = $size; $i < 8; $i++) {
			$first[$i] = "0000";
			$last[$i] = "ffff";
		}
	}

	// Rebuild the final long form IPV6 address
	$first = rocket_ip2long6(implode(":", $first));
	$last = rocket_ip2long6(implode(":", $last));
	$in_range = ($ip >= $first && $ip <= $last);

	return $in_range;
}

// This function takes 2 arguments, an IP address and a "range" in several
// different formats.
// Network ranges can be specified as:
// 1. Wildcard format:     1.2.3.*
// 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
// 3. Start-End IP format: 1.2.3.0-1.2.3.255
// The function will return true if the supplied IP is within the range.
// Note little validation is done on the range inputs - it expects you to
// use one of the above 3 formats.
function rocket_ipv4_in_range($ip, $range) {
	if (strpos($range, '/') !== false) {
		// $range is in IP/NETMASK format
		list($range, $netmask) = explode('/', $range, 2);
		if (strpos($netmask, '.') !== false) {
			// $netmask is a 255.255.0.0 format
			$netmask = str_replace('*', '0', $netmask);
			$netmask_dec = ip2long($netmask);
			return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
		} else {
			// $netmask is a CIDR size block
			// fix the range argument
			$x = explode('.', $range);
			while(count($x)<4) $x[] = '0';
			list($a,$b,$c,$d) = $x;
			$range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
			$range_dec = ip2long($range);
			$ip_dec = ip2long($ip);

			# Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
			#$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

			# Strategy 2 - Use math to create it
			$wildcard_dec = pow(2, (32-$netmask)) - 1;
			$netmask_dec = ~ $wildcard_dec;

			return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
		}
	} else {
		// range might be 255.255.*.* or 1.2.3.0-1.2.3.255
		if (strpos($range, '*') !==false) { // a.b.*.* format
			// Just convert to A-B format by setting * to 0 for A and 255 for B
			$lower = str_replace('*', '0', $range);
			$upper = str_replace('*', '255', $range);
			$range = "$lower-$upper";
		}

		if (strpos($range, '-')!==false) { // A-B format
			list($lower, $upper) = explode('-', $range, 2);
			$lower_dec = (float)sprintf("%u",ip2long($lower));
			$upper_dec = (float)sprintf("%u",ip2long($upper));
			$ip_dec = (float)sprintf("%u",ip2long($ip));
			return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
		}
		return false;
	}
}

/**
 * Outputs notice HTML
 *
 * @since 2.11
 * @author Remy Perona
 *
 * @param array $args An array of arguments used to determine the notice output.
 * @return void
 */
function rocket_notice_html( $args ) {
	$defaults = [
		'status'           => 'success',
		'dismissible'      => 'is-dismissible',
		'message'          => '',
		'action'           => '',
		'dismiss_button'   => false,
		'readonly_content' => '',
	];

	$args = wp_parse_args( $args, $defaults );

	switch ( $args['action'] ) {
		case 'clear_cache':
			$args['action'] = '<a class="wp-core-ui button" href="' . wp_nonce_url( admin_url( 'admin-post.php?action=purge_cache&type=all' ), 'purge_cache_all' ) . '">' . __( 'Clear cache', 'rocket' ) . '</a>';
			break;
		case 'stop_preload':
			$args['action'] = '<a class="wp-core-ui button" href="' . wp_nonce_url( admin_url( 'admin-post.php?action=rocket_stop_preload&type=all' ), 'rocket_stop_preload' ) . '">' . __( 'Stop Preload', 'rocket' ) . '</a>';
			break;
		case 'force_deactivation':
			/**
			 * Allow a "force deactivation" link to be printed, use at your own risks
			 *
			 * @since 2.0.0
			 *
			 * @param bool $permit_force_deactivation true will print the link.
			 */
			$permit_force_deactivation = apply_filters( 'rocket_permit_force_deactivation', true );

			// We add a link to permit "force deactivation", use at your own risks.
			if ( $permit_force_deactivation ) {
				global $status, $page, $s;
				$plugin_file  = 'wp-rocket/wp-rocket.php';
				$rocket_nonce = wp_create_nonce( 'force_deactivation' );

				$args['action'] = '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;rocket_nonce=' . $rocket_nonce . '&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $status . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file ) . '">' . __( 'Force deactivation ', 'rocket' ) . '</a>';
			}
			break;
	}

	?>
	<div class="notice notice-<?php echo esc_attr( $args['status'] ); ?> <?php echo esc_attr( $args['dismissible'] ); ?>">
		<?php
			$tag = 0 !== strpos( $args['message'], '<p' ) && 0 !== strpos( $args['message'], '<ul' );

			echo ( $tag ? '<p>' : '' ) . $args['message'] . ( $tag ? '</p>' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dynamic content is properly escaped in the view.
		?>
		<?php if ( ! empty( $args['readonly_content'] ) ) : ?>
		<p><?php esc_html_e( 'The following code should have been written to this file:', 'rocket' ); ?>
			<br><textarea readonly="readonly" id="rules" name="rules" class="large-text readonly" rows="6"><?php echo esc_textarea( $args['readonly_content'] ); ?></textarea>
		</p>
			<?php
		endif;
		if ( $args['action'] || $args['dismiss_button'] ) :
			?>
		<p>
			<?php echo $args['action']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $args['dismiss_button'] ) : ?>
			<a class="rocket-dismiss" href="<?php echo wp_nonce_url( admin_url( 'admin-post.php?action=rocket_ignore&box=' . $args['dismiss_button'] ), 'rocket_ignore_' . $args['dismiss_button'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Dismiss this notice.', 'rocket' ); ?></a>
			<?php endif; ?>
		</p>
		<?php endif; ?>
	</div>
	<?php
}