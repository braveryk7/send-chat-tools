<?php
/**
 * Create Slack blocks kit item.
 *
 * @author Ken-chan
 * @package WordPress
 * @subpackage Send Chat Tools
 * @since 1.2.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You do not have access rights.' );
}

/**
 * Create Slack blocks kit item.
 */
class Sct_Slack_Blocks {

	/**
	 * Create header.
	 *
	 * @param string $type content type.
	 * @param string $text content text.
	 * @param bool   $emoji Use emoji.
	 */
	public function header( string $type, string $text, bool $emoji = true ): array {
		$header = [
			'type' => 'header',
			'text' => [
				'type'  => $type,
				'text'  => $text,
				'emoji' => $emoji,
			],
		];

		return $header;
	}

	/**
	 * Create single column.
	 *
	 * @param string $type content type.
	 * @param string $text content text.
	 */
	public function single_column( string $type, string $text ): array {
		$column = [
			'type' => 'section',
			'text' => [
				'type' => $type,
				'text' => $text,
			],
		];

		return $column;
	}

	/**
	 * Create two column.
	 *
	 * @param array $content1st [ $type, $text ].
	 * @param array $content2nd [ $type, $text ].
	 */
	public function two_column( array $content1st, array $content2nd ): array {
		$columns = [
			'type'   => 'section',
			'fields' => [
				[
					'type' => $content1st[0],
					'text' => $content1st[1],
				],
				[
					'type' => $content2nd[0],
					'text' => $content2nd[1],
				],
			],
		];

		return $columns;
	}

	/**
	 * Create context.
	 *
	 * @param string $type content type.
	 * @param string $text content text.
	 */
	public function context( string $type, string $text ): array {
		$context = [
			'type'     => 'context',
			'elements' => [
				[
					'type' => $type,
					'text' => $text,
				],
			],
		];

		return $context;
	}

	/**
	 * Create divider.
	 */
	public function divider(): array {
		$divider = [
			'type' => 'divider',
		];

		return $divider;
	}
}
