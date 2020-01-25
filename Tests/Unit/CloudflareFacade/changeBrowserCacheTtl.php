<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_browser_cache_ttl
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeBrowserCacheTtl extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings', false );

		$settings->shouldReceive( 'change_browser_cache_ttl' )
		         ->once()
		         ->with( null, 31536000 )
		         ->andReturnUsing( function() {
			         throw new AuthenticationException( 'Authentication information must be provided' );
		         } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->change_browser_cache_ttl( 31536000 );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_browser_cache_ttl' )
		         ->once()
		         ->with( 'zone1234', 'invalid' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => [],
				         'success' => false,
				         'errors'  => [
					         (object) [
						         'message' => 'Invalid value for zone setting browser_cache_ttl',
					         ],
				         ],
			         ];
		         } );

		$response = $cf->change_browser_cache_ttl( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting browser_cache_ttl', $error->message );
	}

	public function testShouldChangeBrowserCacheTtlWhenTTLGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_browser_cache_ttl' )
		         ->once()
		         ->with( 'zone1234', 3600 )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'   => (object) [
					         'value' => 3600,
				         ],
				         'success'  => true,
				         'errors'   => [],
				         'messages' => [],
			         ];
		         } );

		$response = $cf->change_browser_cache_ttl( 3600 );
		$this->assertTrue( $response->success );
		$this->assertSame( 3600, $response->result->value );
	}
}
