<?php

/**
 * Class EquipmentsManagerAdminSettings
 */
class EquipmentsManagerAdminSettings {

	/**
	 * @var string $prefix The prefix for storing custom fields in the postmeta table.
	 */
	var $prefix = 'em_';
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
		add_action( 'admin_menu', array( $this, 'create_fields' ) );
		add_action( 'save_post', array( $this, 'save_fields' ), 9, 2 );
		add_action( 'display_custom_fields', array( $this, 'display_fields_values' ) );
	}

	/**
	 * Creating custom post type.
	 */
	function create_cpt() {

		// Registering new CPT `equipments`.
		register_post_type( 'equipments',
			array(
				'labels' => array(
					'name'               => __( 'Equipments', 'equipments-manager' ),
					'singular_name'      => __( 'Equipment', 'equipments-manager' ),
					'add_new'            => __( 'Add New Equipment', 'equipments-manager' ),
					'add_new_item'       => __( 'Add New Equipment', 'equipments-manager' ),
					'edit'               => __( 'Edit', 'equipments-manager' ),
					'edit_item'          => __( 'Edit Equipment', 'equipments-manager' ),
					'new_item'           => __( 'New Equipment', 'equipments-manager' ),
					'view'               => __( 'View', 'equipments-manager' ),
					'view_item'          => __( 'View Equipment', 'equipments-manager' ),
					'search_items'       => __( 'Search Equipments', 'equipments-manager' ),
					'not_found'          => __( 'No Equipments found', 'equipments-manager' ),
					'not_found_in_trash' => __( 'No Equipments found in Trash', 'equipments-manager' ),
					'parent'             => __( 'Parent Equipments', 'equipments-manager' ),
				),

				'public'      => true,
				'supports'    => array( 'title', 'editor', 'thumbnail' ),
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
			'hierarchical'      => true,
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
	 * Create Custom Fields meta box.
	 */
	function create_fields() {
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

	/**
	 * Save the Custom Field values.
	 */
	function save_fields( $post_id, $post ) {
		if (
			! isset( $_POST['equipments-manager-custom-fields_wpnonce'] ) ||
			! wp_verify_nonce( $_POST['equipments-manager-custom-fields_wpnonce'], 'equipments-manager-custom-fields' )
		) {
			return;
		}

		if ( $this->post_type !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( $this->custom_fields as $custom_field ) {
			$field = $this->prefix . $custom_field['name'];
			if ( isset( $_POST[ $field ] ) && ! empty( trim( $_POST[ $field ] ) ) ) {
				$value = $_POST[ $field ];

				update_post_meta( $post_id, $field, $value );
			} else {
				delete_post_meta( $post_id, $field );
			}
		}
	}

	/**
	 * Display custom filed values on front end.
	 */
	function display_fields_values() {
		global $post;

		if ( $post->post_type !== $this->post_type || ! $post instanceof WP_Post ) {
			return;
		}

		$fields = get_post_meta( $post->ID );

		printf( '<div class="equipments-manager-fields"><h3>Details</h3>' );
		$categories = get_the_terms( $post, 'equipments-category' );
		if ( $categories && is_array( $categories ) && ! empty( $categories ) ) {
			printf( '<div class="equipments-manager-field"><label>Category</label>' );
			foreach ( $categories as $category ) {
				printf(
					'<span><a href="%s">%s</a>,&nbsp;</span>',
					esc_url( get_term_link( $category->term_id ) ),
					esc_attr( $category->name )
				);
			}
			printf( '</div>' );
		}
		foreach ( $this->custom_fields as $custom_field ) {
			$field = $this->prefix . $custom_field['name'];
			if ( ! empty( $fields[ $field ][0] ) ) {
				printf(
					'<div class="equipments-manager-field"><label>%s</label><span>%s</span></div>',
					esc_attr( $custom_field['title'] ),
					esc_html( $fields[ $field ][0] )
				);
			}
		}
		printf( '</div>' );
	}
}
