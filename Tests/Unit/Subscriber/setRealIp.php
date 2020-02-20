<?php

namespace WPMedia\Cloudflare\Tests\Unit\Subscriber;

use Brain\Monkey\Functions;
use Mockery;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\Subscriber;

/**
 * @covers WPMedia\Cloudflare\Subscriber::set_real_ip
 * @group  Cloudflare
 * @group  CloudflareSubscriber
 */
class Test_SetRealIp extends TestCase {

	public function testShouldNotSetIP() {
		$mocks      = $this->getConstructorMocksWithIps();
		$cloudflare = $mocks['cloudflare'];

		$cloudflare->shouldNotReceive( 'get_cloudflare_ips' );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->set_real_ip();
	}

	public function testIPNotInRange() {
		$mocks      = $this->getConstructorMocksWithIps();
		$cloudflare = $mocks['cloudflare'];

		$_SERVER['HTTP_CF_CONNECTING_IP'] = '172.64.0.1';
		$_SERVER['REMOTE_ADDR']           = '172.64.0.15';

		$cloudflare->shouldReceive( 'get_cloudflare_ips' )->andReturn( $mocks['cf_ips'] );

		Functions\when( 'wp_unslash' )->returnArg();
		Functions\when( 'get_rocket_ipv6_full' )->returnArg();
		Functions\when( 'rocket_ipv6_in_range' )->justReturn( false );
		Functions\when( 'rocket_ipv4_in_range' )->justReturn( false );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->set_real_ip();

		$this->assertNotEquals(
			$_SERVER['HTTP_CF_CONNECTING_IP'],
			$_SERVER['REMOTE_ADDR']
		);
	}

	public function testShouldSetRealIP4() {
		$mocks      = $this->getConstructorMocksWithIps();
		$cloudflare = $mocks['cloudflare'];

		$_SERVER['HTTP_CF_CONNECTING_IP'] = '172.64.0.1';
		$_SERVER['REMOTE_ADDR']           = '172.64.0.15';

		$cloudflare->shouldReceive( 'get_cloudflare_ips' )->andReturn( $mocks['cf_ips'] );

		Functions\when( 'wp_unslash' )->returnArg();
		Functions\when( 'get_rocket_ipv6_full' )->returnArg();
		Functions\when( 'rocket_ipv6_in_range' )->justReturn( false );
		Functions\when( 'rocket_ipv4_in_range' )->justReturn( true );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->set_real_ip();

		$this->assertSame(
			$_SERVER['HTTP_CF_CONNECTING_IP'],
			$_SERVER['REMOTE_ADDR']
		);
	}

	public function testShouldSetRealIP6() {
		$mocks      = $this->getConstructorMocksWithIps();
		$cloudflare = $mocks['cloudflare'];

		$_SERVER['HTTP_CF_CONNECTING_IP'] = '2a06:98c0::/29';
		$_SERVER['REMOTE_ADDR']           = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

		$cloudflare->shouldReceive( 'get_cloudflare_ips' )->andReturn( $mocks['cf_ips'] );

		Functions\when( 'wp_unslash' )->returnArg();
		Functions\when( 'get_rocket_ipv6_full' )->returnArg();
		Functions\when( 'rocket_ipv6_in_range' )->justReturn( true );
		Functions\when( 'rocket_ipv4_in_range' )->justReturn( false );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->set_real_ip();

		$this->assertSame(
			$_SERVER['HTTP_CF_CONNECTING_IP'],
			$_SERVER['REMOTE_ADDR']
		);
	}
}
