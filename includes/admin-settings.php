<?php

/**
 * Class EquipmentsManagerAdminSettings
 */
class EquipmentsManagerAdminSettings {

	/**
	 * @var string $prefix The prefix for storing custom fields in the postmeta table.
	 */
	var $prefix = '_em_';
	/**
	 * @var string $post_type Custom type name.
	 */
	var $post_type = 'equipments';
	/**
	 * @var array $custom_fields Defines the custom fields available
	 */
	var $custom_fields = array(
		array(
			'name'        => 'serial-number',
			'title'       => 'Serial Number',
			'description' => '',
			'type'        => 'text',
		),
		array(
			'name'          => 'status',
			'title'         => 'Status',
			'description'   => '',
			'type'          => 'select',
			'options'       => array(
					'active'   => 'Active',
					'inactive' => 'Inactive',
			),
		),
		array(
			'name'          => 'category',
			'title'         => 'Category',
			'description'   => '',
			'type'          => 'select',
			'options'       => array(),
		),
		array(
			'name'        => 'size',
			'title'       => 'Size',
			'description' => '',
			'type'        => 'text',
		),
		array(
			'name'        => 'manufacturer',
			'title'       => 'Manufacturer',
			'description' => '',
			'type'        => 'text',
		),
		array(
			'name'        => 'make',
			'title'       => 'Make',
			'description' => '',
			'type'        => 'text',
		),
		array(
			'name'        => 'model',
			'title'       => 'Model',
			'description' => '',
			'type'        => 'text',
		),
		array(
			'name'        => 'year',
			'title'       => 'Year',
			'description' => '',
			'type'        => 'text',
		),
		array(
			'name'        => 'quantity',
			'title'       => 'Quantity',
			'description' => '',
			'type'        => 'text',
		),
		array(
			'name'        => 'price',
			'title'       => 'Price',
			'description' => '',
			'type'        => 'text',
		),
	);

	/**
	 * PHP 4 Compatible Constructor.
	 */
	function EquipmentsManagerAdminSettings() {
		$this->__construct();
	}

	/**
	 * PHP 5 Compatible Constructor.
	 */
	function __construct() {
		add_action( 'init', array( $this, 'create_cpt' ) );
		add_action( 'admin_menu', array( $this, 'create_custom_fields' ) );
	}

	/**
	 * Creating custom post type `Equipments`.
	 */
	function create_cpt() {

		// Registering new CPT `equipments`.
		register_post_type( 'equipments',
			array(
				'labels' => array(
					'name'               => 'Equipments',
					'singular_name'      => 'Equipment',
					'add_new'            => 'Add New',
					'add_new_item'       => 'Add New Equipment',
					'edit'               => 'Edit',
					'edit_item'          => 'Edit Equipment',
					'new_item'           => 'New Equipment',
					'view'               => 'View',
					'view_item'          => 'View Equipment',
					'search_items'       => 'Search Equipments',
					'not_found'          => 'No Equipments found',
					'not_found_in_trash' => 'No Equipments found in Trash',
					'parent'             => 'Parent Equipments',
				),

				'public'      => true,
				'supports'    => array( 'title', 'thumbnail' ),
				'taxonomies'  => array( '' ),
				'menu_icon'   => plugin_dir_url( __FILE__ ) . '../images/logo.png',
				'has_archive' => true,
			)
		);

		// Adding new taxonomy `Equipments Category`.
		$labels = array(
			'name'              => __( 'Equipments Category', 'equipments-manager' ),
			'singular_name'     => __( 'Equipments Category', 'equipments-manager' ),
			'search_items'      => __( 'Search Equipments Category', 'equipments-manager' ),
			'all_items'         => __( 'All Equipments Category', 'equipments-manager' ),
			'parent_item'       => __( 'Parent Equipments Category', 'equipments-manager' ),
			'parent_item_colon' => __( 'Parent Equipments Category:', 'equipments-manager' ),
			'edit_item'         => __( 'Edit Equipments Category', 'equipments-manager' ),
			'update_item'       => __( 'Update Equipments Category', 'equipments-manager' ),
			'add_new_item'      => __( 'Add New Equipments Category', 'equipments-manager' ),
			'new_item_name'     => __( 'New Equipments Category Name', 'equipments-manager' ),
			'menu_name'         => __( 'Equipments Category', 'equipments-manager' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_reset'     => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'equipments-category' ),
		);

		register_taxonomy( 'equipments-category', array( 'equipments' ), $args );
	}

	/**
	 * Create the new Custom Fields meta box
	 */
	function create_custom_fields() {
		// Adding custom fields to the `equipments` CPT.
		add_meta_box( 'equipments-manager-quipment-details',
			'Equipment Details',
			array( $this, 'display_fields' ),
			'equipments',
			'normal',
			'high'
		);
	}

	/**
	 * Display custom fields for `equipments` post type.
	 */
	function display_fields() {
		global $post;
		?>
		<div class="form-wrap">
			<?php
			wp_nonce_field( 'equipments-manager-custom-fields', 'equipments-manager-custom-fields_wpnonce', false, true );
			foreach ( $this->custom_fields as $custom_field ) :
			?>
				<div class="form-field form-required">
					<?php
					switch ( $custom_field['type'] ) {
						case 'select':
							// Text area
							printf(
								'<label for="%s"><b>%s</b></label>',
								esc_attr( $this->prefix . $custom_field['name'] ),
								esc_html( $custom_field['title'] )
							);
							printf(
								'<select name="%s" id="%s"><option value=""> -- Select --</option> ',
								esc_attr( $this->prefix . $custom_field['name'] ),
								esc_attr( $this->prefix . $custom_field['name'] )
							);
							$selected = get_post_meta( $post->ID, $this->prefix . $custom_field['name'], true );
							if ( 'status' === $custom_field['name'] ) {
								foreach ( $custom_field['options'] as $name => $value ) {
									printf(
										'<option %s value="%s" >%s</option>',
										( $name === $selected )? 'selected': '',
										esc_attr( $name ),
										esc_html( $value )
									);
								}
							}

							if ( 'category' === $custom_field['name'] ) {
								$options = get_terms( array(
									'taxonomy' => 'equipments-category',
									'hide_empty' => false,
								) );
								$selected = get_post_meta( $post->ID, $this->prefix . $custom_field['name'], true );
								if ( ! empty( $options ) ) {
									foreach ( $options as $option ) {
										printf(
											'<option %s value="%s" >%s</option>',
											( $option->term_id === $selected )? 'selected': '',
											esc_attr( $option->term_id ),
											esc_html( $option->name )
										);
									}
								}
							}
							printf( '</select>' );
							break;
						default:
							// Plain text field
							printf(
								'<label for="%s"><b>%s</b></label>',
								esc_attr( $this->prefix . $custom_field['name'] ),
								esc_html( $custom_field['title'] )
							);
							printf(
								'<input type="text" name="%s" id="%s" value="%s" />',
								esc_attr( $this->prefix . $custom_field['name'] ),
								esc_attr( $this->prefix . $custom_field['name'] ),
								esc_html( get_post_meta( $post->ID, $this->prefix . $custom_field['name'], true ) )
							);
							break;
					} // End switch().
					if ( ! empty( $custom_field['description'] ) ) {
						printf(
							'<p>%s</p>',
							esc_html( $custom_field['description'] )
						);
					} ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}