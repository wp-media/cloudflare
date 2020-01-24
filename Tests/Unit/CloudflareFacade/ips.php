<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Api;
use Cloudflare\Exception\AuthenticationException;
use Cloudflare\IPs;
use Mockery;
use WPMedia\Cloudflare\Tests\Unit\TestCase;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::ips
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_Ips extends TestCase {

	private function getMocks( $setApiExpects = true ) {
		if ( $setApiExpects ) {
			$api = Mockery::mock( Api::class, [
				'setEmail'      => null,
				'setAuthKey'    => null,
				'setCurlOption' => null,
			] );
		} else {
			$api = Mockery::mock( Api::class );
		}

		$cf       = $this->getFacade( $api );
		$ips = Mockery::mock( IPs::class, [ $api ] );
		$this->set_reflective_property( $ips, 'ips', $cf );

		return [ $cf, $ips ];
	}

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $ips ) = $this->getMocks( false );

		$ips->shouldReceive( 'ips' )
		    ->once()
		    ->andReturnUsing( function() {
			    throw new AuthenticationException( 'Authentication information must be provided' );
		    } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->ips();
	}

	public function testShouldReturnIps() {
		list( $cf, $ips ) = $this->getMocks();

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$ips->shouldReceive( 'ips' )
		    ->once()
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'   => (object) [
					    'ipv4_cidrs' => [ '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22' ],
					    'ipv6_cidrs' => [ '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32' ],
				    ],
				    'success'  => true,
				    'errors'   => [],
				    'messages' => [],
			    ];
		    } );

		$response = $cf->ips();
		$this->assertTrue( $response->success );
		$this->assertContains( '173.245.48.0/20', $response->result->ipv4_cidrs );
		$this->assertContains( '2400:cb00::/32', $response->result->ipv6_cidrs );
	}
}
