<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::purge_files
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_PurgeFiles extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $cache ) = $this->getMocksWithDep( 'cache', false );

		$cache->shouldReceive( 'purge_files' )
		      ->once()
		      ->with( null, [ '/purge-url' ] )
		      ->andReturnUsing( function() {
			      throw new AuthenticationException( 'Authentication information must be provided' );
		      } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->purge_files( [ '/purge-url' ] );
	}

	public function testShouldFailWhenUrlInvalid() {
		list( $cf, $cache ) = $this->getMocksWithDep( 'cache' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$cache->shouldReceive( 'purge_files' )
		      ->once()
		      ->with( 'zone1234', [ '/invalid/URL' ] )
		      ->andReturnUsing( function() {
			      return (object) [
				      'result'  => [],
				      'success' => false,
				      'errors'  => [
					      (object) [
						      'message' => 'Unable to purge /invalid/URL, which is an invalid URL.',
					      ],
				      ],
			      ];
		      } );

		$response = $cf->purge_files( [ '/invalid/URL' ] );
		$this->assertFalse( $response->success );
		$this->assertSame( 'Unable to purge /invalid/URL, which is an invalid URL.', $response->errors[0]->message );
	}

	public function testShouldSucceedWhenUrlsGiven() {
		list( $cf, $cache ) = $this->getMocksWithDep( 'cache' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$cache->shouldReceive( 'purge_files' )
		      ->once()
		      ->with( 'zone1234', [ '/about', '/contact' ] )
		      ->andReturnUsing( function() {
			      return (object) [
				      'result'  => [],
				      'success' => true,
				      'errors'  => [],
			      ];
		      } );

		$response = $cf->purge_files( [ '/about', '/contact' ] );
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
