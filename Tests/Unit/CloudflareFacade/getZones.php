<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Api;
use Cloudflare\Exception\AuthenticationException;
use Mockery;
use WPMedia\Cloudflare\CloudflareFacade;
use WPMedia\Cloudflare\Tests\Unit\TestCase;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::get_zones
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_GetZones extends TestCase {

	public function testShouldFailWhenZoneIdInvalid() {
		$api_mock = Mockery::mock( Api::class );
		$cf       = new CloudflareFacade( $api_mock );

		$api_mock->shouldReceive( 'get' )
		         ->once()
		         ->with( 'zones/' )
		         ->andReturnUsing( function() {
			         throw new AuthenticationException( 'Authentication information must be provided' );
		         } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->get_zones();
	}

	public function testShouldSucceedWhenZoneExists() {
		$api_mock = Mockery::mock( Api::class );
		$cf       = new CloudflareFacade( $api_mock );

		$api_mock->shouldReceive( 'get' )
		         ->once()
		         ->with( 'zones/' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'   => (object) [],
				         'success'  => true,
				         'errors'   => [],
				         'messages' => [],
			         ];
		         } );
		$response = $cf->get_zones();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
