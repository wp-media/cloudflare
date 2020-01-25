<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::list_pagerules
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ListPagerules extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $pagerules ) = $this->getMocksWithDep( 'page_rules', false );

		$pagerules->shouldReceive( 'list_pagerules' )
		          ->once()
		          ->with( null, 'active' )
		          ->andReturnUsing( function() {
			          throw new AuthenticationException( 'Authentication information must be provided' );
		          } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->list_pagerules();
	}

	public function testShouldGetPageRulesWhenZoneIdIsSet() {
		list( $cf, $pagerules ) = $this->getMocksWithDep( 'page_rules' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$pagerules->shouldReceive( 'list_pagerules' )
		          ->once()
		          ->with( 'zone1234', 'active' )
		          ->andReturnUsing( function() {
			          return (object) [
				          'result'  => [],
				          'success' => true,
				          'errors'  => [],
			          ];
		          } );

		$response = $cf->list_pagerules();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
