<?php

namespace WPMedia\Cloudflare\Tests\Integration;

use Cloudflare\Api;
use WP_Rocket\Admin\Options;
use WP_Rocket\Admin\Options_Data;
use WP_Rocket\Event_Management\Event_Manager;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\CloudflareFacade;
use WPMedia\Cloudflare\CloudflareSubscriber;

class Factory {
	private $container = [];

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function init() {
		require_once dirname( __DIR__ ) . '/Fixtures/functions.php';
		$this->addOptions();
		$this->initContainer();
	}

	private function addOptions() {
		update_option(
			'wp_rocket_setting',
			[
				'cdn'                         => 0,
				'cdn_cnames'                  => [],
				'cdn_zone'                    => [],
				'cdn_reject_files'            => [],
				'do_cloudflare'               => 0,
				'cloudflare_email'            => '',
				'cloudflare_api_key'          => '',
				'cloudflare_zone_id'          => '',
				'cloudflare_devmode'          => 0,
				'cloudflare_protocol_rewrite' => 0,
				'cloudflare_auto_settings'    => 0,
				'cloudflare_old_settings'     => '',
				'varnish_auto_purge'          => 0,
			]
		);
	}

	private function initContainer() {
		$this->container['options_api']       = new Options( 'wp_rocket_' );
		$this->container['options']           = new Options_Data(
			$this->container['options_api']->get( 'settings', [] )
		);
		$this->container['cloudflare_api']    = new Api();
		$this->container['cloudflare_facade'] = new CloudflareFacade( $this->container['cloudflare_api'] );
		$this->container['cloudflare']        = new Cloudflare( $this->container['options'], $this->container['cloudflare_facade'] );

		$this->container['cloudflare_subscriber'] = new CloudflareSubscriber(
			$this->container['cloudflare'],
			$this->container['options'],
			$this->container['options_api']
		);

		$this->container['event_manager'] = new Event_Manager();
		$this->container['event_manager']->add_subscriber( $this->container['cloudflare_subscriber'] );
	}

	public function getContainer( $key ) {
		if ( isset( $this->container[ $key ] ) ) {
			return $this->container[ $key ];
		}
	}

	public function restoreState() {
		$this->addOptions();
	}
}
