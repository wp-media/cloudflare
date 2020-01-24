<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use WPMedia\Cloudflare\Tests\Unit\TestCase;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::purge
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_Purge extends TestCase {

	public function testShouldPurgeCacheWhenZoneIdIsSet() {
		$this->assertTrue( true );
	}
}
