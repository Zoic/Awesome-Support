<?php
/**
 * Ticket Stakeholders.
 *
 * This metabox is used to display all parties involved in the ticket resolution.
 *
 * @since 3.0.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Add nonce
wp_nonce_field( 'wpas_update_cf', 'wpas_cf', false, true );

// Set post-dependant values
if ( isset( $post ) && is_a( $post, 'WP_Post' ) && 'auto-draft' !== $post->post_status ) {

	// Client
	$client        = get_userdata( $post->post_author );
	$client_id     = $client->ID;
	$client_name   = $client->data->display_name;
	$client_option = "<option value='$client_id' selected='selected'>$client_name</option>";
	$client_link   = esc_url( admin_url( add_query_arg( array(
		'post_type' => 'ticket',
		'author'    => $client_id
	), 'edit.php' ) ) );

	// Staff
	$staff_id = wpas_get_cf_value( 'assignee', get_the_ID() );

} else {

	// Staff
	$staff_id = get_current_user_id();

	// Client
	$client_id   = 0;
	$client_name = '';
	$client_link = '';

}

// Set post-independent vars
$staff         = get_user_by( 'ID', $staff_id );
$staff_name    = $staff->data->display_name;
$client_option = "<option value='$client_id' selected='selected'>$client_name</option>";
?>
<div id="wpas-stakeholders">
	<label for="wpas-issuer"><strong><?php _e( 'Ticket Creator', 'awesome-support' ); ?></strong></label>
	<p>

		<?php if ( current_user_can( 'create_ticket' ) ):

			$users_atts = array( 'agent_fallback' => true, 'select2' => true, 'name' => 'post_author_override', 'id' => 'wpas-issuer', 'data_attr' => array( 'capability' => 'create_ticket' ) );

			if ( isset( $post ) ) {
				$users_atts['selected'] = $post->post_author;
			}

			echo wpas_dropdown( $users_atts, $client_option );

		else: ?>
			<a id="wpas-issuer" href="<?php echo $client_link; ?>"><?php echo $client_name; ?></a></p>
		<?php endif; ?>

	<p class="description"><?php printf( __( 'This ticket has been raised by the user hereinabove.', 'awesome-support' ), '#' ); ?></p>
	<hr>

	<label for="wpas-assignee"><strong><?php _e( 'Support Staff', 'awesome-support' ); ?></strong></label>
	<p>
		<?php
		$staff_atts = array(
			'name'      => 'wpas_assignee',
			'id'        => 'wpas-assignee',
			'disabled'  => ! current_user_can( 'assign_ticket' ) ? true : false,
			'select2'   => true,
			'data_attr' => array( 'capability' => 'edit_ticket' )
		);

		echo wpas_dropdown( $staff_atts, "<option value='$staff_id' selected='selected'>$staff_name</option>" );
		?>
	</p>
	<p class="description"><?php printf( __( 'The above agent is currently responsible for this ticket.', 'awesome-support' ), '#' ); ?></p>

</div>