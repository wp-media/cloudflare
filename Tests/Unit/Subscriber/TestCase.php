<?php

namespace WPMedia\Cloudflare\Tests\Unit\Subscriber;

use Mockery;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\Tests\Unit\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

	/**
	 * Get the mocks required by Cloudflare’s constructor.
	 *
	 * @since  1.0
	 *
	 * @param integer $do_cloudflare - Value to return for $options->get( 'do_cloudflare' ).
	 * @param string  $email         - Value to return for $options->get( 'cloudflare_email' ).
	 * @param string  $api_key       - Value to return for $options->get( 'cloudflare_api_key' ).
	 * @param string  $zone_id       - Value to return for $options->get( 'cloudflare_zone_id' ).
	 *
	 * @return [] array of mocks.
	 */
	protected function getConstructorMocks( $do_cloudflare = 1, $email = '', $api_key = '', $zone_id = '' ) {
		$map          = [
			[ 'do_cloudflare', '', $do_cloudflare ],
			[ 'cloudflare_email', null, $email ],
			[ 'cloudflare_api_key', null, $api_key ],
			[ 'cloudflare_zone_id', null, $zone_id ],
		];
		$options      = $this->createMock( 'WP_Rocket\Admin\Options' );
		$options_data = $this->createMock( 'WP_Rocket\Admin\Options_Data' );

		$options_data->method( 'get' )->will( $this->returnValueMap( $map ) );

		return [
			'options_data' => $options_data,
			'options'      => $options,
			'cloudflare'   => Mockery::mock( Cloudflare::class ),
			'wp_error'     => Mockery::mock( 'WP_Error' ),
		];
	}


	/**
	 * Get the mocks required by Cloudflare’s constructor.
	 *
	 * @since  1.0
	 *
	 * @param integer $do_cloudflare - Value to return for $options->get( 'do_cloudflare' ).
	 * @param string  $email         - Value to return for $options->get( 'cloudflare_email' ).
	 * @param string  $api_key       - Value to return for $options->get( 'cloudflare_api_key' ).
	 * @param string  $zone_id       - Value to return for $options->get( 'cloudflare_zone_id' ).
	 *
	 * @return [] array of mocks.
	 */
	protected function getConstructorMocksWithIps( $do_cloudflare = 1, $email = '', $api_key = '', $zone_id = '' ) {
		$cf_ips = (object) [
			'result'   => (object) [],
			'success'  => true,
			'errors'   => [],
			'messages' => [],
		];

		$cf_ips->result->ipv4_cidrs = [
			'173.245.48.0/20',
			'103.21.244.0/22',
			'103.22.200.0/22',
			'103.31.4.0/22',
			'141.101.64.0/18',
			'108.162.192.0/18',
			'190.93.240.0/20',
			'188.114.96.0/20',
			'197.234.240.0/22',
			'198.41.128.0/17',
			'162.158.0.0/15',
			'104.16.0.0/12',
			'172.64.0.0/13',
			'131.0.72.0/22',
		];

		$cf_ips->result->ipv6_cidrs = [
			'2400:cb00::/32',
			'2606:4700::/32',
			'2803:f800::/32',
			'2405:b500::/32',
			'2405:8100::/32',
			'2a06:98c0::/29',
			'2c0f:f248::/32',
		];

		$mocks           = $this->getConstructorMocks( $do_cloudflare, $email, $api_key, $zone_id );
		$mocks['cf_ips'] = $cf_ips;

		return $mocks;
	}
}
