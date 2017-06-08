<?php

/**
 * Class EquipmentsManagerAssignment
 */
class EquipmentsManagerAssignment {

	/**
	 * PHP 4 Compatible Constructor.
	 */
	function EquipmentsManagerAssignment() {
		$this->__construct();
	}

	/**
	 * PHP 5 Compatible Constructor.
	 */
	function __construct() {
		add_action( 'show_user_profile', array( $this, 'assignment_fields_display' ) );
		add_action( 'edit_user_profile', array( $this, 'assignment_fields_display' ) );

		add_action( 'personal_options_update', array( $this, 'assignment_save_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'assignment_save_fields' ) );

		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_style( 'my-jquery-ui' );

		// Ajax handler for autocomplete suggestions request.
		add_action( 'wp_ajax_em_get_suggestions', array( $this, 'autocomplete_suggestions' ) );
		add_action( 'wp_ajax_nopriv_em_get_suggestions', array( $this, 'autocomplete_suggestions' ) );
	}

	/**
	 * Display the assignment fields.
	 */
	function assignment_fields_display() {
		// If is current user's profile (profile.php)
		if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE ) {
			$user_id = get_current_user_id();
		} elseif ( ! empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ) {
			// If is another user's profile page.
			$user_id = $_GET['user_id'];
		} else {
			return;
		}

		wp_enqueue_script( 'equipments-manager-script', plugin_dir_url( __DIR__ ) . 'js/script.js', [], false, true );
		printf(
			'<h2>%s</h2>',
			esc_html__( 'Equipments Assignment', 'equipments-manager' )
		);

		$assignments = get_user_meta( $user_id, 'em_assignments', true );

		wp_nonce_field( 'equipments_manager_assignment_nonce', 'equipments_manager_assignment_nonce' );
		?>
		<table id="repeatable-fieldset-one" width="100%">
			<thead>
			<tr>
				<th width="2%"></th>
				<th width="30%">Category</th>
				<th width="30%">Equipment</th>
				<th width="30%">Qty</th>
			</tr>
			</thead>
			<tbody>
			<?php

			if ( $assignments ) {

				foreach ( $assignments as $assignment ) {
					?>
					<tr>
						<td><a class="button remove-row" href="#">-</a></td>
						<td>
							<input type="text" class="widefat em_category" name="category[]" value="<?php if ( '' !== $assignment['category'] ) echo esc_attr( $assignment['category'] ); ?>"/>
							<input type="hidden" class="widefat em_category_id" name="category_id[]" value="<?php if ( '' !== $assignment['category_id'] ) echo esc_attr( $assignment['category_id'] ); ?>"/>
						</td>
						<td>
							<input type="text" class="widefat em_equipment" name="equipment[]" value="<?php if ( '' !== $assignment['equipment'] ) echo esc_attr( $assignment['equipment'] ); ?>"/>
							<input type="hidden" class="widefat em_equipment_id" name="equipment_d[]" value="<?php if ( '' !== $assignment['equipment_id'] ) echo esc_attr( $assignment['equipment_id'] ); ?>"/>
						</td>
						<td>
							<input type="text" class="widefat em_qty" name="qty[]" value="<?php if ( '' !== $assignment['qty'] ) echo esc_attr( $assignment['qty'] ); ?>"/>
						</td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td><a class="button remove-row" href="#">-</a></td>
					<td>
						<input type="text" class="widefat em_category" name="category[]" />
						<input type="hidden" class="widefat em_category_id" name="category_id[]" />
					</td>
					<td>
						<input type="text" class="widefat em_equipment" name="equipment[]" />
						<input type="hidden" class="widefat em_equipment_id" name="equipment_id[]" />
					</td>
					<td>
						<input type="text" class="widefat em_qty" name="qty[]" />
					</td>
				</tr>
				<?php
			} else {
				// show a blank one
				?>
				<tr>
					<td><a class="button remove-row" href="#">-</a></td>
					<td>
						<input type="text" class="widefat em_category" name="category[]" />
						<input type="hidden" class="widefat em_category_id" name="category_id[]" />
					</td>
					<td>
						<input type="text" class="widefat em_equipment" name="equipment[]" />
						<input type="hidden" class="widefat em_equipment_id" name="equipment_id[]" />
					</td>
					<td>
						<input type="text" class="widefat em_qty" name="qty[]" />
					</td>
				</tr>
			<?php } ?>

			<!-- empty hidden one for jQuery -->
			<tr class="empty-row screen-reader-text">
				<td><a class="button remove-row" href="#">-</a></td>
				<td>
					<input type="text" class="widefat em_category" name="category[]" />
					<input type="hidden" class="widefat em_category_id" name="category_id[]" />
				</td>
				<td>
					<input type="text" class="widefat em_equipment" name="equipment[]" />
					<input type="hidden" class="widefat em_equipment_id" name="equipment_id[]" />
				</td>
				<td>
					<input type="text" class="widefat em_qty" name="qty[]" />
				</td>
			</tr>
			</tbody>
		</table>

		<p><a id="add-row" class="button" href="#">Add another</a>
			<input type="submit" class="button button-primary" value="Save" />
		</p>

		<?php
	}

	/**
	 * Save assignment data.
	 *
	 * @param  int $user_id Current user ID.
	 */
	function assignment_save_fields( $user_id ) {
		if (
			! isset( $_POST['equipments_manager_assignment_nonce'] ) ||
			! wp_verify_nonce( $_POST['equipments_manager_assignment_nonce'], 'equipments_manager_assignment_nonce' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		$old = get_user_meta( $user_id, 'em_assignments', true );
		$new = array();


		$categories    = $_POST['category'];
		$category_ids  = $_POST['category_id'];
		$equipments    = $_POST['equipment'];
		$equipment_ids = $_POST['equipment_id'];
		$qty           = $_POST['qty'];
		$qty_ids       = $_POST['qty_id'];

		$count = count( $categories );

		for ( $i = 0; $i < $count; $i++ ) {
			if ( ! empty( $categories[ $i ] ) ) {
				$new[ $i ]['category']    = $categories[ $i ];
				$new[ $i ]['category_id'] = absint( $category_ids[ $i ] );
				$new[ $i ]['equipment']        = $equipments[ $i ];
				$new[ $i ]['equipment_id']     = absint( $equipment_ids[ $i ] );
				$new[ $i ]['qty']         = absint( $qty[ $i ] );
			}
		}

		if ( ! empty( $new ) && $new !== $old ) {
			update_user_meta( $user_id, 'em_assignments', $new );
		} elseif ( empty( $new ) && $old ) {
			update_user_meta( $user_id, 'em_assignments', $old );
		}
	}

	function autocomplete_suggestions() {
		$suggestions = array();
		if ( 'category' === trim( $_REQUEST['type'] ) ) {
			$terms = get_terms( array(
				'taxonomy' => 'equipments-category',
				'search'   => trim( esc_attr( strip_tags( $_REQUEST['term'] ) ) ),
			) );

			foreach ( $terms as $term ) {
				$suggestions[] = array(
					'label' => esc_html( $term->name ),
					'id'    => $term->term_id,
				);
			}
		} elseif ( 'equipment' === trim( $_REQUEST['type'] ) ) {
			$the_query = new WP_Query( array(
				'post_type' => 'equipments',
				's'    => trim( esc_attr( strip_tags( $_REQUEST['term'] ) ) ),
				'tax_query' => array(
					array(
						'taxonomy' => 'equipments-category',
						'field'    => 'term_id',
						'terms'    => absint( $_REQUEST['category'] ),
					),
				),
			) );

			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$suggestions[] = array(
						'label' => esc_html( get_the_title() ),
						'id'    => get_the_ID(),
					);
				}
			}
		}

		$response = $_GET['callback'] . '(' . json_encode( $suggestions ) . ')';
		echo $response;
		exit;
	}
}
