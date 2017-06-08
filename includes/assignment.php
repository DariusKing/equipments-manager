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


		add_action( 'wp_ajax_em_get_category', array( $this, 'autocomplete_suggestions' ) );
		add_action( 'wp_ajax_nopriv_em_get_category', array( $this, 'autocomplete_suggestions' ) );
	}

	/**
	 * Display the assignment fields.
	 */
	function assignment_fields_display() {
//	    echo urldecode('http://localhost/serveon/wp-admin/load-scripts.php?c=1&load%5B%5D=jquery-ui-core,jquery-ui-widget,jquery-ui-position,jquery-ui-menu,wp-a11y,jquery-ui-autocomplete,hoverIntent,common,admin-bar,pa&load%5B%5D=ssword-strength-meter,underscore,wp-util,user-profile,svg-painter,heartbeat,wp-auth-check,jquery-ui-mouse,jquery-ui-resizable,jq&load%5B%5D=uery-ui-draggable,jquery-ui-button,jquery-ui-dialog&ver=4.5.3');
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
				<th width="30%">Item</th>
				<th width="30%">Qty</th>
<!--				<th width="2%"></th>-->
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
							<input type="hidden" class="widefat em_category" name="category_id[]" value="<?php if ( '' !== $assignment['category_id'] ) echo esc_attr( $assignment['category_id'] ); ?>"/>
						</td>
						<td>
							<input type="text" class="widefat em_item" name="item[]" value="<?php if ( '' !== $assignment['item'] ) echo esc_attr( $assignment['item'] ); ?>"/>
							<input type="hidden" class="widefat em_item" name="item_d[]" value="<?php if ( '' !== $assignment['item_id'] ) echo esc_attr( $assignment['item_id'] ); ?>"/>
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
						<input type="hidden" class="widefat em_category" name="category_id[]" />
					</td>
					<td>
						<input type="text" class="widefat em_item" name="item[]" />
						<input type="hidden" class="widefat em_item" name="item_id[]" />
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
						<input type="hidden" class="widefat em_category" name="category_id[]" />
					</td>
					<td>
						<input type="text" class="widefat em_item" name="item[]" />
						<input type="hidden" class="widefat em_item" name="item_id[]" />
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
					<input type="hidden" class="widefat em_category" name="category_id[]" />
				</td>
				<td>
					<input type="text" class="widefat em_item" name="item[]" />
					<input type="hidden" class="widefat em_item" name="item_id[]" />
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


		$categories   = $_POST['category'];
		$category_ids = $_POST['category_id'];
		$items        = $_POST['item'];
		$item_ids     = $_POST['item_id'];
		$qty          = $_POST['qty'];
		$qty_ids      = $_POST['qty_id'];

		$count = count( $categories );

		for ( $i = 0; $i < $count; $i++ ) {
			if ( ! empty( $categories[ $i ] ) ) {
				$new[ $i ]['category']    = $categories[ $i ];
				$new[ $i ]['category_id'] = absint( $category_ids[ $i ] );
				$new[ $i ]['item']        = $items[ $i ];
				$new[ $i ]['item_id']     = absint( $item_ids[ $i ] );
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
		$terms = get_terms( array(
			'taxonomy' => 'equipments-category',
			'search'   => trim( esc_attr( strip_tags( $_REQUEST['term'] ) ) ),
		) );

		$suggestions = array();

		foreach ( $terms as $term ) {
			$suggestion = array();
			$suggestion['label'] = esc_html( $term->name );
			$suggestion['id']    = $term->term_id;

			$suggestions[] = $suggestion;
		}

		$response = $_GET['callback'] . '(' . json_encode( $suggestions ) . ')';
		echo $response;
		exit;
	}
}
