<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::purge
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_Purge extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $cache ) = $this->getMocksWithDep( 'cache', false );

		$cache->shouldReceive( 'purge' )
		          ->once()
		          ->with( null, true )
		          ->andReturnUsing( function() {
			          throw new AuthenticationException( 'Authentication information must be provided' );
		          } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->purge();
	}

	public function testShouldPurgeCacheWhenZoneIdIsSet() {
		list( $cf, $cache ) = $this->getMocksWithDep( 'cache' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$cache->shouldReceive( 'purge' )
		          ->once()
		          ->with( 'zone1234', true )
		          ->andReturnUsing( function() {
			          return (object) [
				          'result'  => [],
				          'success' => true,
				          'errors'  => [],
			          ];
		          } );

		$response = $cf->purge();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
