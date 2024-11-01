<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing ?>
<tr valign="top">
	<td colspan="2" style="padding: 0;">
		<h2 style="padding-left: 14px;"><?php echo esc_html( $data['title'] ); ?></h2>
		<p style="padding-left: 14px; margin-bottom: 8px;"><?php echo esc_html( $data['description'] ); ?></p>

		<table class="sksoftware-settings-table widefat">
			<thead>
			<tr>
				<th></th>
				<?php foreach ( $table_fields as $table_field ) : ?>
					<th>
						<?php echo esc_html( $table_field['title'] ); ?>
					</th>
				<?php endforeach; ?>
				<th>
					<?php echo esc_html__( 'Actions', 'sksoftware-postone-for-woocommerce' ); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="<?php echo count( $table_fields ) + 2; ?>">
					<button type="button" class="button button-secondary" data-toggle="sksoftware-postone-for-woocommerce-add-new-row">
						<?php echo esc_html__( 'Add new row', 'sksoftware-postone-for-woocommerce' ); ?>
					</button>
				</td>
			</tr>
			</tfoot>
			<tbody class="sksoftware-settings-table-sortable" data-table-row-template="<?php echo esc_attr( $row_template ); ?>" data-current-index="<?php echo count( array_keys( $table_data ) ); ?>">
			<?php
			foreach ( array_keys( $table_data ) as $index ) {
				echo $this->generate_table_row_html( $key, $table_fields, $table_data, $index ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
			</tbody>
		</table>
	</td>
</tr>
