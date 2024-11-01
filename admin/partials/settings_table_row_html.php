<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing ?>
<tr>
    <td class="sksoftware-settings-table-handle"></td>
	<?php
	foreach ( $table_fields as $table_field_key => $table_field ) {
		$table_field['placeholder'] = $table_field['title'];

		$table_field_html = $this->{'generate_table_' . $table_field['type'] . '_html'}( $table_field_key, $table_field, $table_key, $table_data, $index );

		echo $table_field_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
    <td>
        <button type="button" class="button-link button-link-delete" data-toggle="sksoftware-postone-for-woocommerce-delete-row">
			<?php echo esc_html__( 'Delete', 'sksoftware-postone-for-woocommerce' ); ?>
        </button>
    </td>
</tr>
