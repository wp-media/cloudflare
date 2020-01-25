<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::ips
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_Ips extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $ips ) = $this->getMocksWithDep( 'ips', false );

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
		list( $cf, $ips ) = $this->getMocksWithDep( 'ips' );

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
			    ];
		    } );

		$response = $cf->ips();
		$this->assertTrue( $response->success );
		$this->assertContains( '173.245.48.0/20', $response->result->ipv4_cidrs );
		$this->assertContains( '2400:cb00::/32', $response->result->ipv6_cidrs );
	}
}
