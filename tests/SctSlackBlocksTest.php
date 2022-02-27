<?php
declare( strict_types = 1 );

/**
 * Test: Sct_Slack_Blocks
 */
class SctSlackBlocksTest extends PHPUnit\Framework\TestCase {
	/**
	 * This test class instance.
	 *
	 * @var object $instance instance.
	 */
	private $instance;

	/**
	 * Settings: ABSPATH, test class file, WordPress functions.
	 */
	public static function setUpBeforeClass(): void {
		if ( ! defined( 'ABSPATH' ) ) {
			define( 'ABSPATH', '' );
		}

		require_once './class/class-sct-slack-blocks.php';
		require_once './tests/lib/wordpress-functions.php';
	}
}
