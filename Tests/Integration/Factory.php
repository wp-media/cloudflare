<?php

namespace WPMedia\Cloudflare\Tests\Integration;

use WP_Rocket\Admin\Options;
use WP_Rocket\Admin\Options_Data;
use WP_Rocket\Event_Management\Event_Manager;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\CloudflareSubscriber;
use WPMedia\PHPUnit\TestCaseTrait;

class Factory {
	use TestCaseTrait;
	private $container = [];

	public static $api_credentials_config_file;
	public static $api_key;
	public static $email;
	public static $zone_id;
	public static $site_url;

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function init() {
		$this->stubPolyfills();

		add_filter( 'site_url', [ $this, 'setSiteUrl' ] );

		$this->setUpApiCredentials();
		$this->resetToOriginalState();
		$this->initContainer();

		remove_filter( 'site_url', [ $this, 'setSiteUrl' ] );
	}

	public function setSiteUrl() {
		return self::$site_url;
	}

	private function setUpApiCredentials() {
		self::$api_credentials_config_file = 'cloudflare.php';
		self::$email                       = self::getApiCredential( 'ROCKET_CLOUDFLARE_EMAIL' );
		self::$api_key                     = self::getApiCredential( 'ROCKET_CLOUDFLARE_API_KEY' );
		self::$zone_id                     = self::getApiCredential( 'ROCKET_CLOUDFLARE_ZONE_ID' );
		self::$site_url                    = self::getApiCredential( 'ROCKET_CLOUDFLARE_SITE_URL' );
	}

	public function resetToOriginalState() {
		$transients = [
			'rocket_cloudflare_is_api_keys_valid',
			'rocket_cloudflare_ips',
		];
		foreach ( $transients as $transient ) {
			delete_transient( $transient );
		}

		$this->addOptions();
	}

	private function addOptions() {
		$options = $this->getOptions();
		update_option( 'wp_rocket_settings', $options );

		if ( isset( $this->container['options'] ) ) {
			$this->container['options']->set_values( $options );
		}
	}

	public function getOptions() {
		return [
			'cdn'                         => 0,
			'cdn_cnames'                  => [],
			'cdn_zone'                    => [],
			'cdn_reject_files'            => [],
			'do_cloudflare'               => 1,
			'cloudflare_email'            => self::$email,
			'cloudflare_api_key'          => self::$api_key,
			'cloudflare_zone_id'          => self::$zone_id,
			'cloudflare_devmode'          => 0,
			'cloudflare_protocol_rewrite' => 0,
			'cloudflare_auto_settings'    => 0,
			'cloudflare_old_settings'     => '',
			'varnish_auto_purge'          => 0,
		];
	}

	private function initContainer() {
		$this->container['options_api']    = new Options( 'wp_rocket_' );
		$this->container['options']        = new Options_Data(
			$this->container['options_api']->get( 'settings', [] )
		);
		$this->container['cloudflare_api'] = new APIClient( 'cloudflare/1.0' );
		$this->container['cloudflare']     = new Cloudflare( $this->container['options'], $this->container['cloudflare_api'] );

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

	/**
	 * Gets the credential's value from either an environment variable (stored locally on the machine or CI) or from a
	 * local constant defined in `tests/env/local/cloudflare.php`.
	 *
	 * @param string $name Name of the environment variable or constant to find.
	 *
	 * @return string returns the value if available; else an empty string.
	 */
	public static function getApiCredential( $name ) {
		$var = getenv( $name );
		if ( ! empty( $var ) ) {
			return $var;
		}

		if ( ! self::$api_credentials_config_file ) {
			return '';
		}

		$config_file = dirname( __DIR__ ) . '/env/local/cloudflare.php';
		if ( ! is_readable( $config_file ) ) {
			return '';
		}

		// This file is local to the developer's machine and not stored in the repo.
		require_once $config_file;

		return rocket_get_constant( $name, '' );
	}
}
