<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing ?>
<td>
	<input class="wc_input_decimal input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="text" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_id ); ?>" style="width: 100%; max-width: 100%; <?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( wc_format_localized_decimal( $value ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />

	<?php echo $this->get_description_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</td>
