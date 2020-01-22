<?php

namespace WPMedia\Cloudflare\Tests\Unit;

use function WPMedia\Cloudflare\Tests\init_test_suite;

require_once dirname( dirname( __FILE__ ) ) . '/bootstrap-functions.php';
init_test_suite( 'Unit' );

define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );
define( 'WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS );
define( 'MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS );
define( 'YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS );
