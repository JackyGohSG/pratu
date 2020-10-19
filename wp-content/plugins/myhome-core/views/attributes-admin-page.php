<div>

	<backend-panel
		id="myhome-backend-panel"
		:atts="<?php echo esc_attr( \MyHomeCore\Attributes\Attribute_Factory::get_all_unfiltered_data() ); ?>"
		:selected-elements="<?php echo esc_attr( json_encode( \MyHomeCore\Estates\Elements\Estate_Elements_Settings::get() ) ); ?>"
		:available-elements="<?php echo esc_attr( json_encode( \MyHomeCore\Estates\Elements\Estate_Elements_Settings::get_available() ) ); ?>"
		:element-types="<?php echo esc_attr( json_encode( \MyHomeCore\Estates\Elements\Estate_Elements_Settings::get_types() ) ); ?>"
		:agent-fields='<?php echo esc_attr( json_encode( \MyHomeCore\Users\Fields\Settings::get_fields() ) ); ?>'
		:search-forms='<?php echo esc_attr( json_encode( \MyHomeCore\Components\Listing\Search_Forms\Search_Form::get_all_search_forms_data() ) ); ?>'
		:submit-property-steps='<?php echo esc_attr( json_encode( \MyHomeCore\Frontend_Panel\Submit_Property_Settings::get_steps( true ) ) ); ?>'
		:submit-property-fields='<?php echo esc_attr( json_encode( \MyHomeCore\Frontend_Panel\Submit_Property_Settings::get_available_fields() ) ); ?>'
	>
		<?php if ( isset( \MyHomeCore\My_Home_Core()->settings->props['mh-breadcrumbs'] ) && ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-breadcrumbs'] ) ) : ?>
			<div class="mh-breadcrumbs-required-fields">
				<?php esc_html_e( 'Fields that are part of the breadcrumbs are required. Please add it to visible or disable in your /wp-admin/ > MyHome Themes > Breadcrumbs. List of required fields:', 'myhome-core' ); ?>
				<?php foreach ( \MyHomeCore\Common\Breadcrumbs\Breadcrumbs::get_attributes() as $myhome_attribute ) : ?>
					<?php echo esc_html( $myhome_attribute->get_name() ); ?><span class="mh-breadcrumbs-required-fields__comma">,</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</backend-panel>

</div>
