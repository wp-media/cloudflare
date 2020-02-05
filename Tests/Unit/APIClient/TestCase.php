<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use Cloudflare\Api;
use InvalidArgumentException;
use Mockery;
use WPMedia\Cloudflare\Tests\Unit\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

	protected function getMocks( $setApiExpects = true ) {
		$api = $this->getApi( $setApiExpects );

		return [ $api, $this->getFacade( $api ) ];
	}

	protected function getMocksWithDep( $property = null, $setApiExpects = true ) {
		$api = $this->getApi( $setApiExpects );
		$cf  = $this->getFacade( $api );

		if ( ! array_key_exists( $property, $this->deps ) ) {
			throw new InvalidArgumentException( 'No dependency given for test.' );
		}

		$dep = Mockery::mock( $this->deps[ $property ], [ $api ] );
		$this->set_reflective_property( $dep, $property, $cf );

		return [ $cf, $dep ];
	}

	private function getApi( $setApiExpects = true ) {
		if ( $setApiExpects ) {
			return Mockery::mock( Api::class, [
				'setEmail'      => null,
				'setAuthKey'    => null,
				'setCurlOption' => null,
			] );
		}

		Return Mockery::mock( Api::class );
	}

	private function getCache( $api, $cf ) {

	}
}
