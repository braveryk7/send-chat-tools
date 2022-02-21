<?php
declare( strict_types = 1 );

define( 'ABSPATH', '' );

require_once './class/class-sct-developer-notify.php';

/**
 * Test: Sct_Developer_Notify
 */
class Sct_Developer_NotifyTest extends PHPUnit\Framework\TestCase {
	/**
	 * This test class instance.
	 *
	 * @var object $instance instance.
	 */
	private $instance;

	/**
	 * SetUp.
	 * Create instance.
	 */
	protected function setUp() :void {
		$this->instance = new Sct_Developer_Notify();
	}
}
