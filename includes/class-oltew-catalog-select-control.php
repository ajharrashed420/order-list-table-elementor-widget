<?php
/**
 * Searchable catalog selector for the Elementor editor.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Extend Elementor's native multiple selector with remote catalog search. */
class Oltew_Catalog_Select_Control extends \Elementor\Control_Select2 {
	/**
	 * Get the custom control identifier.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'oltew_catalog_select';
	}

	/**
	 * Preserve the standard select settings and enable multiple selection.
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		return array_merge(
			parent::get_default_settings(),
			array(
				'multiple'     => true,
				'catalog_type' => 'products',
				'label_block'  => true,
			)
		);
	}
}
