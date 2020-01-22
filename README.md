# Cloudflare Addon
This module adds Cloudflare to [WP Rocket](https://wp-rocket.me) or any other product. It includes:

- Cloudflare API: to interact with Cloudflare's APIs.
- Cloudflare handler: to make it easier to interact with the API.
- Subscriber: to wire to WP Rocket or any other product.

It uses native WordPress functionality including [`wp_remote_get()`](https://developer.wordpress.org/reference/functions/wp_remote_get/) and [`wp_remote_request()`]() instead of directly using curl.
