<?php
/**
 * Plugin Name: Give - Campos Adicionais
 * Plugin URI: https://kattz.com.br
 * Description: Esse plugin é utilizado para habilitar os campos adicionais nos formulários para doações
 * Version: 1.0
 * Author: KATTZ
 * Author URI: https://kattz.com.br
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * NOTE: This is not a "snippet" but a plugin that you can install and activate. You can put it in a
 * folder in your /plugins/ directory, or even just drop it directly into the /plugins/ directory
 * and it will activate like any other plugin.
 *
 * DISCLAIMER: This is provided as an EXAMPLE of how to do custom fields for Give. We provide no
 * guarantees if you put this on a live site. And we do not offer Support for this code at all.
 * It is simply a free resource for your purposes.
 */

/**
 * Custom Form Fields in Donation form
 *
 * @param $form_id
 */
function myprefix123_give_donations_custom_form_fields( $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		?>
		<div id="give-message-wrap" class="form-row form-row-first form-row-responsive">
			<label class="give-label" for="cpf-cnpj">
				<?php _e( 'CPF/CNPJ', 'give' ); ?>
				<?php if ( give_field_is_required( 'cpf_cnpj', $form_id ) ) : ?>
					<span class="give-required-indicator">*</span>
				<?php endif ?>
				<span class="give-tooltip give-icon give-icon-question"
				      data-tooltip="<?php _e( 'Informe aqui o seu CPF ou CNPJ.', 'give' ) ?>">
				</span>
			</label>

			
			<input class="give-input" type="text" name="cpf_cnpj" placeholder="CPF ou CNPJ" id="cpf-cnpj">
		</div>
		<?php
	}
}

add_action( 'give_purchase_form_register_login_fields', 'myprefix123_give_donations_custom_form_fields' );

/**
 * Require custom field "Engraving message" field.
 *
 * @param $required_fields
 * @param $form_id
 *
 * @return array
 */
function myprefix123_give_donations_require_fields( $required_fields, $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		$required_fields['cpf_cnpj'] = array(
			'error_id'      => 'cpf_cnpj',
			'error_message' => __( 'Por favor, informe seu CPF ou CNPJ', 'give' ),
		);
	}

	return $required_fields;
}

add_filter( 'give_donation_form_required_fields', 'myprefix123_give_donations_require_fields', 10, 2 );


/**
 * Add Field to Payment Meta
 *
 * Store the custom field data custom post meta attached to the `give_payment` CPT.
 *
 * @param $payment_id
 *
 * @return mixed
 */
function myprefix123_give_donations_save_custom_fields( $payment_id ) {

	if ( isset( $_POST['cpf_cnpj'] ) ) {
		$message = wp_strip_all_tags( $_POST['cpf_cnpj'], true );
		give_update_payment_meta( $payment_id, 'cpf_cnpj', $message );
	}

}

add_action( 'give_insert_payment', 'myprefix123_give_donations_save_custom_fields' );

/**
 * Show Data in Transaction Details
 *
 * Show the custom field(s) on the transaction page.
 *
 * @param $payment_id
 */
function myprefix123_give_donations_donation_details( $payment_id ) {

	$engraving_message = give_get_meta( $payment_id, 'cpf_cnpj', true );

	if ( $engraving_message ) : ?>

		<div id="cpf-cnpj" class="postbox">
			<h3 class="hndle"><?php esc_html_e( 'CPF ou CNPJ', 'give' ); ?></h3>
			<div class="inside" style="padding-bottom:10px;">
				<?php echo wpautop( $engraving_message ); ?>
			</div>
		</div>

	<?php endif;

}

add_action( 'give_view_donation_details_billing_before', 'myprefix123_give_donations_donation_details', 10, 1 );


/**
 * Get Donation Referral Data
 *
 * Example function that returns Custom field data if present in payment_meta;
 * The example used here is in conjunction with the Give documentation tutorials.
 *
 * @param array $tag_args Array of arguments
 *
 * @return string
 */
function myprefix123_donation_referral_data( $tag_args ) {
	$engraving_message = give_get_meta( $tag_args['payment_id'], 'cpf_cnpj', true );

	$output = __( 'No referral data found.', 'give' );

	if ( ! empty( $engraving_message ) ) {
		$output = wp_kses_post( $engraving_message );
	}

	return $output;
}

/**
 * Adds a Custom "Engraved Message" Tag
 *
 * This function creates a custom Give email template tag.
 */
function myprefix123_add_sample_referral_tag() {
	give_add_email_tag( 'cpf_cnpj', 'This outputs the cpf cnpj (custom field)', 'myprefix123_donation_referral_data' );
}

add_action( 'give_add_email_tags', 'myprefix123_add_sample_referral_tag' );

/**
 * Add Donation engraving message fields.
 *
 * @params array    $args
 * @params int      $donation_id
 * @params int      $form_id
 *
 * @return array
 */
function myprefix123_donation_receipt_args( $args, $donation_id, $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		$engraving_message              = give_get_meta( $donation_id, 'cpf_cnpj', true );
		$args['cpf_cnpj'] = array(
			'name'    => __( 'CPF ou CNPJ', 'give' ),
			'value'   => wp_kses_post( $engraving_message ),
			// Do not show Engraved field if empty
			'display' => empty( $engraving_message ) ? false : true,
		);
	}

	return $args;
}

add_filter( 'give_donation_receipt_args', 'myprefix123_donation_receipt_args', 30, 3 );


/**
 * Add Donation engraving message fields in export donor fields tab.
 */
function myprefix123_donation_standard_donor_fields() {
	?>
	<li>
		<label for="cpf-cnpj">
			<input type="checkbox" checked
			       name="give_give_donations_export_option[cpf_cnpj]"
			       id="cpf-cnpj"><?php _e( 'CPF ou CNPJ', 'give' ); ?>
		</label>
	</li>
	<?php
}

add_action( 'give_export_donation_standard_donor_fields', 'myprefix123_donation_standard_donor_fields' );


/**
 * Add Donation engraving message header in CSV.
 *
 * @param array $cols columns name for CSV
 *
 * @return  array $cols columns name for CSV
 */
function myprefix123_update_columns_heading( $cols ) {
	if ( isset( $cols['cpf_cnpj'] ) ) {
		$cols['cpf_cnpj'] = __( 'CPF ou CNPJ', 'give' );
	}

	return $cols;

}

add_filter( 'give_export_donation_get_columns_name', 'myprefix123_update_columns_heading' );


/**
 * Add Donation engraving message fields in CSV.
 *
 * @param array Donation data.
 * @param Give_Payment $payment Instance of Give_Payment
 * @param array $columns Donation data $columns that are not being merge
 *
 * @return array Donation data.
 */
function myprefix123_export_donation_data( $data, $payment, $columns ) {
	if ( ! empty( $columns['cpf_cnpj'] ) ) {
		$message                        = $payment->get_meta( 'cpf_cnpj' );
		$data['cpf_cnpj'] = isset( $message ) ? wp_kses_post( $message ) : '';
	}

	return $data;
}

add_filter( 'give_export_donation_data', 'myprefix123_export_donation_data', 10, 3 );

/**
 * Remove Custom meta fields from Export donation standard fields.
 *
 * @param array $responses Contain all the fields that need to be display when donation form is display
 * @param int $form_id Donation Form ID
 *
 * @return array $responses
 */
function myprefix123_export_custom_fields( $responses, $form_id ) {
	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) && ! empty( $responses['standard_fields'] ) ) {
		$standard_fields = $responses['standard_fields'];
		if ( in_array( 'cpf_cnpj', $standard_fields ) ) {
			$standard_fields              = array_diff( $standard_fields, array( 'cpf_cnpj' ) );
			$responses['standard_fields'] = $standard_fields;
		}
	}

	return $responses;
}

add_filter( 'give_export_donations_get_custom_fields', 'myprefix123_export_custom_fields', 10, 2 );












/**
 * Custom Form Fields in Donation form
 *
 * @param $form_id
 */
function telefone_give_donations_custom_form_fields( $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		?>
		<div id="give-message-wrap-telefone" class="form-row form-row-last form-row-responsive">
			<label class="give-label" for="telefone">
				<?php _e( 'Telefone', 'give' ); ?>
				<?php if ( give_field_is_required( 'telefone', $form_id ) ) : ?>
					<span class="give-required-indicator">*</span>
				<?php endif ?>
				<span class="give-tooltip give-icon give-icon-question"
				      data-tooltip="<?php _e( 'Informe aqui o seu telefone para contato.', 'give' ) ?>">
				</span>
			</label>

			
			<input class="give-input" type="text" name="telefone" placeholder="(__)_____-____" id="telefone">
		</div>
		<?php
	}
}

add_action( 'give_purchase_form_register_login_fields', 'telefone_give_donations_custom_form_fields' );

/**
 * Require custom field "Engraving message" field.
 *
 * @param $required_fields
 * @param $form_id
 *
 * @return array
 */
function telefone_give_donations_require_fields( $required_fields, $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		$required_fields['telefone'] = array(
			'error_id'      => 'telefone',
			'error_message' => __( 'Por favor, informe seu telefone para contato', 'give' ),
		);
	}

	return $required_fields;
}

add_filter( 'give_donation_form_required_fields', 'telefone_give_donations_require_fields', 10, 2 );


/**
 * Add Field to Payment Meta
 *
 * Store the custom field data custom post meta attached to the `give_payment` CPT.
 *
 * @param $payment_id
 *
 * @return mixed
 */
function telefone_give_donations_save_custom_fields( $payment_id ) {

	if ( isset( $_POST['telefone'] ) ) {
		$message = wp_strip_all_tags( $_POST['telefone'], true );
		give_update_payment_meta( $payment_id, 'telefone', $message );
	}

}

add_action( 'give_insert_payment', 'telefone_give_donations_save_custom_fields' );

/**
 * Show Data in Transaction Details
 *
 * Show the custom field(s) on the transaction page.
 *
 * @param $payment_id
 */
function telefone_give_donations_donation_details( $payment_id ) {

	$engraving_message = give_get_meta( $payment_id, 'telefone', true );

	if ( $engraving_message ) : ?>

		<div id="telefone" class="postbox">
			<h3 class="hndle"><?php esc_html_e( 'Telefone', 'give' ); ?></h3>
			<div class="inside" style="padding-bottom:10px;">
				<?php echo wpautop( $engraving_message ); ?>
			</div>
		</div>

	<?php endif;

}

add_action( 'give_view_donation_details_billing_before', 'telefone_give_donations_donation_details', 10, 1 );


/**
 * Get Donation Referral Data
 *
 * Example function that returns Custom field data if present in payment_meta;
 * The example used here is in conjunction with the Give documentation tutorials.
 *
 * @param array $tag_args Array of arguments
 *
 * @return string
 */
function telefone_donation_referral_data( $tag_args ) {
	$engraving_message = give_get_meta( $tag_args['payment_id'], 'telefone', true );

	$output = __( 'No referral data found.', 'give' );

	if ( ! empty( $engraving_message ) ) {
		$output = wp_kses_post( $engraving_message );
	}

	return $output;
}

/**
 * Adds a Custom "Engraved Message" Tag
 *
 * This function creates a custom Give email template tag.
 */
function telefone_add_sample_referral_tag() {
	give_add_email_tag( 'telefone', 'This outputs phone number (custom field)', 'telefone_donation_referral_data' );
}

add_action( 'give_add_email_tags', 'telefone_add_sample_referral_tag' );

/**
 * Add Donation engraving message fields.
 *
 * @params array    $args
 * @params int      $donation_id
 * @params int      $form_id
 *
 * @return array
 */
function telefone_donation_receipt_args( $args, $donation_id, $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		$engraving_message              = give_get_meta( $donation_id, 'telefone', true );
		$args['telefone'] = array(
			'name'    => __( 'Telefone', 'give' ),
			'value'   => wp_kses_post( $engraving_message ),
			// Do not show Engraved field if empty
			'display' => empty( $engraving_message ) ? false : true,
		);
	}

	return $args;
}

add_filter( 'give_donation_receipt_args', 'telefone_donation_receipt_args', 30, 3 );


/**
 * Add Donation engraving message fields in export donor fields tab.
 */
function telefone_donation_standard_donor_fields() {
	?>
	<li>
		<label for="telefone">
			<input type="checkbox" checked
			       name="give_give_donations_export_option[telefone]"
			       id="telefone"><?php _e( 'Telefone', 'give' ); ?>
		</label>
	</li>
	<?php
}

add_action( 'give_export_donation_standard_donor_fields', 'telefone_donation_standard_donor_fields' );


/**
 * Add Donation engraving message header in CSV.
 *
 * @param array $cols columns name for CSV
 *
 * @return  array $cols columns name for CSV
 */
function telefone_update_columns_heading( $cols ) {
	if ( isset( $cols['telefone'] ) ) {
		$cols['telefone'] = __( 'Telefone', 'give' );
	}

	return $cols;

}

add_filter( 'give_export_donation_get_columns_name', 'telefone_update_columns_heading' );


/**
 * Add Donation engraving message fields in CSV.
 *
 * @param array Donation data.
 * @param Give_Payment $payment Instance of Give_Payment
 * @param array $columns Donation data $columns that are not being merge
 *
 * @return array Donation data.
 */
function telefone_export_donation_data( $data, $payment, $columns ) {
	if ( ! empty( $columns['telefone'] ) ) {
		$message                        = $payment->get_meta( 'telefone' );
		$data['telefone'] = isset( $message ) ? wp_kses_post( $message ) : '';
	}

	return $data;
}

add_filter( 'give_export_donation_data', 'telefone_export_donation_data', 10, 3 );

/**
 * Remove Custom meta fields from Export donation standard fields.
 *
 * @param array $responses Contain all the fields that need to be display when donation form is display
 * @param int $form_id Donation Form ID
 *
 * @return array $responses
 */
function telefone_export_custom_fields( $responses, $form_id ) {
	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) && ! empty( $responses['standard_fields'] ) ) {
		$standard_fields = $responses['standard_fields'];
		if ( in_array( 'telefone', $standard_fields ) ) {
			$standard_fields              = array_diff( $standard_fields, array( 'telefone' ) );
			$responses['standard_fields'] = $standard_fields;
		}
	}

	return $responses;
}

add_filter( 'give_export_donations_get_custom_fields', 'telefone_export_custom_fields', 10, 2 );







/**
 * Custom Form Fields in Donation form
 *
 * @param $form_id
 */
function endereco_give_donations_custom_form_fields( $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		?>
		<div id="give-message-wrap-endereco" class="form-row form-row-wide">
			<label class="give-label" for="endereco">
				<?php _e( 'Endereço', 'give' ); ?>
				<?php if ( give_field_is_required( 'endereco', $form_id ) ) : ?>
					<span class="give-required-indicator">*</span>
				<?php endif ?>
				<span class="give-tooltip give-icon give-icon-question"
				      data-tooltip="<?php _e( 'Informe aqui seu endereço.', 'give' ) ?>">
				</span>
			</label>

			
			<input class="give-input" type="text" name="endereco" placeholder="Informe aqui seu endereço" id="endereco">
		</div>
		<?php
	}
}

add_action( 'give_purchase_form_register_login_fields', 'endereco_give_donations_custom_form_fields' );

/**
 * Require custom field "Engraving message" field.
 *
 * @param $required_fields
 * @param $form_id
 *
 * @return array
 */
function endereco_give_donations_require_fields( $required_fields, $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		$required_fields['endereco'] = array(
			'error_id'      => 'endereco',
			'error_message' => __( 'Por favor, informe seu endereço', 'give' ),
		);
	}

	return $required_fields;
}

add_filter( 'give_donation_form_required_fields', 'endereco_give_donations_require_fields', 10, 2 );


/**
 * Add Field to Payment Meta
 *
 * Store the custom field data custom post meta attached to the `give_payment` CPT.
 *
 * @param $payment_id
 *
 * @return mixed
 */
function endereco_give_donations_save_custom_fields( $payment_id ) {

	if ( isset( $_POST['endereco'] ) ) {
		$message = wp_strip_all_tags( $_POST['endereco'], true );
		give_update_payment_meta( $payment_id, 'endereco', $message );
	}

}

add_action( 'give_insert_payment', 'endereco_give_donations_save_custom_fields' );

/**
 * Show Data in Transaction Details
 *
 * Show the custom field(s) on the transaction page.
 *
 * @param $payment_id
 */
function endereco_give_donations_donation_details( $payment_id ) {

	$engraving_message = give_get_meta( $payment_id, 'endereco', true );

	if ( $engraving_message ) : ?>

		<div id="endereco" class="postbox">
			<h3 class="hndle"><?php esc_html_e( 'Endereço', 'give' ); ?></h3>
			<div class="inside" style="padding-bottom:10px;">
				<?php echo wpautop( $engraving_message ); ?>
			</div>
		</div>

	<?php endif;

}

add_action( 'give_view_donation_details_billing_before', 'endereco_give_donations_donation_details', 10, 1 );


/**
 * Get Donation Referral Data
 *
 * Example function that returns Custom field data if present in payment_meta;
 * The example used here is in conjunction with the Give documentation tutorials.
 *
 * @param array $tag_args Array of arguments
 *
 * @return string
 */
function endereco_donation_referral_data( $tag_args ) {
	$engraving_message = give_get_meta( $tag_args['payment_id'], 'endereco', true );

	$output = __( 'No referral data found.', 'give' );

	if ( ! empty( $engraving_message ) ) {
		$output = wp_kses_post( $engraving_message );
	}

	return $output;
}

/**
 * Adds a Custom "Engraved Message" Tag
 *
 * This function creates a custom Give email template tag.
 */
function endereco_add_sample_referral_tag() {
	give_add_email_tag( 'endereco', 'This outputs the address (custom field)', 'endereco_donation_referral_data' );
}

add_action( 'give_add_email_tags', 'endereco_add_sample_referral_tag' );

/**
 * Add Donation engraving message fields.
 *
 * @params array    $args
 * @params int      $donation_id
 * @params int      $form_id
 *
 * @return array
 */
function endereco_donation_receipt_args( $args, $donation_id, $form_id ) {

	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) ) {
		$engraving_message              = give_get_meta( $donation_id, 'endereco', true );
		$args['endereco'] = array(
			'name'    => __( 'endereco', 'give' ),
			'value'   => wp_kses_post( $engraving_message ),
			// Do not show Engraved field if empty
			'display' => empty( $engraving_message ) ? false : true,
		);
	}

	return $args;
}

add_filter( 'give_donation_receipt_args', 'endereco_donation_receipt_args', 30, 3 );


/**
 * Add Donation engraving message fields in export donor fields tab.
 */
function endereco_donation_standard_donor_fields() {
	?>
	<li>
		<label for="endereco">
			<input type="checkbox" checked
			       name="give_give_donations_export_option[endereco]"
			       id="endereco"><?php _e( 'Endereço', 'give' ); ?>
		</label>
	</li>
	<?php
}

add_action( 'give_export_donation_standard_donor_fields', 'endereco_donation_standard_donor_fields' );


/**
 * Add Donation engraving message header in CSV.
 *
 * @param array $cols columns name for CSV
 *
 * @return  array $cols columns name for CSV
 */
function endereco_update_columns_heading( $cols ) {
	if ( isset( $cols['endereco'] ) ) {
		$cols['endereco'] = __( 'endereco', 'give' );
	}

	return $cols;

}

add_filter( 'give_export_donation_get_columns_name', 'endereco_update_columns_heading' );


/**
 * Add Donation engraving message fields in CSV.
 *
 * @param array Donation data.
 * @param Give_Payment $payment Instance of Give_Payment
 * @param array $columns Donation data $columns that are not being merge
 *
 * @return array Donation data.
 */
function endereco_export_donation_data( $data, $payment, $columns ) {
	if ( ! empty( $columns['endereco'] ) ) {
		$message                        = $payment->get_meta( 'endereco' );
		$data['endereco'] = isset( $message ) ? wp_kses_post( $message ) : '';
	}

	return $data;
}

add_filter( 'give_export_donation_data', 'endereco_export_donation_data', 10, 3 );

/**
 * Remove Custom meta fields from Export donation standard fields.
 *
 * @param array $responses Contain all the fields that need to be display when donation form is display
 * @param int $form_id Donation Form ID
 *
 * @return array $responses
 */
function endereco_export_custom_fields( $responses, $form_id ) {
	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	// if ( $form_id == 754) {
	$forms = array( 64, 3801 );
	if ( in_array( $form_id, $forms ) && ! empty( $responses['standard_fields'] ) ) {
		$standard_fields = $responses['standard_fields'];
		if ( in_array( 'endereco', $standard_fields ) ) {
			$standard_fields              = array_diff( $standard_fields, array( 'endereco' ) );
			$responses['standard_fields'] = $standard_fields;
		}
	}

	return $responses;
}

add_filter( 'give_export_donations_get_custom_fields', 'endereco_export_custom_fields', 10, 2 );