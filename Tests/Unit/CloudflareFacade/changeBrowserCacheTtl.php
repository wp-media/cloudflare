<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Api;
use Cloudflare\Exception\AuthenticationException;
use Cloudflare\Zone\Settings;
use Mockery;
use WPMedia\Cloudflare\Tests\Unit\TestCase;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_browser_cache_ttl
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeBrowserCacheTtl extends TestCase {

	protected function getFacade( $api_mock ) {
		$mock = Mockery::mock( 'WPMedia\Cloudflare\CloudflareFacade[init_api_objects]', [ $api_mock ] )->shouldAllowMockingProtectedMethods();
		$mock->shouldReceive( 'init_api_objects' )->andReturnNull();

		return $mock;
	}

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api      = Mockery::mock( Api::class );
		$cf       = $this->getFacade( $api );
		$settings = Mockery::mock( Settings::class, [ $api ] );
		$this->set_reflective_property( $settings, 'settings', $cf );

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

	public function testShouldChangeBrowserCacheTtlWhenTTLGiven() {
		$api      = Mockery::mock( Api::class, [
			'setEmail'      => null,
			'setAuthKey'    => null,
			'setCurlOption' => null,
		] );
		$cf       = $this->getFacade( $api );
		$settings = Mockery::mock( Settings::class, [ $api ] );
		$this->set_reflective_property( $settings, 'settings', $cf );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_browser_cache_ttl' )
		         ->once()
		         ->with( 'zone1234', 31536000 )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'   => (object) [
					         'value' => 31536000,
				         ],
				         'success'  => true,
				         'errors'   => [],
				         'messages' => [],
			         ];
		         } );

		$response = $cf->change_browser_cache_ttl( 31536000 );
		$this->assertTrue( $response->success );
		$this->assertSame( 31536000, $response->result->value );
	}
}
