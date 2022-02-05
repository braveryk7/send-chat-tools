<?php
declare( strict_types = 1 );

define( 'ABSPATH', '' );

require_once './class/class-sct-base.php';

/**
 * Test: Sct_Base
 */
class Sct_BaseTest extends PHPUnit\Framework\TestCase {
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
		$this->instance = new Sct_Base();
	}

	/**
	 * TEST: add_prefix()
	 */
	public function test_add_prefix() {
		$this->assertSame( 'sct_options', $this->instance->add_prefix( 'options' ) );
	}
}
