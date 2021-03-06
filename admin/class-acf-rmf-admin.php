<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/rahulsprajapati/profile/
 * @since      1.0.0
 *
 * @package    acf_rmf
 * @subpackage acf_rmf/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    acf_rmf
 * @subpackage acf_rmf/admin
 * @author     Rahul Prajapati <rahul.prajapati@live.in>
 */
class Acf_Rmf_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class, set its properties,
	 * action to add mime type filter in acf relationship field option
	 * and filter to post query for mime types selected in relationship metabox.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version           The version of this plugin.
	 *
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'acf/create_field_options/type=relationship', array( $this, 'acf_rmf_create_options' ), 11, 1 );

		add_filter( 'acf/fields/relationship/query', array( $this, 'acf_rmf_query_post_args' ), 10, 2 );
	}

	/**
	 * callback function for action "acf/create_field_options/type=relationship"
	 *
	 * Create extra options for your field. This is rendered when editing a field.
	 * The value of $field['name'] can be used (like bellow) to save extra data to the $field
	 * Adding mime type filter option in ACF relationship field
	 *
	 * @since    1.0.0
	 *
	 * @param	$field	- an array holding all the field's data
	 */
	public function acf_rmf_create_options( $field ) {
		$all_mimes = get_allowed_mime_types();
		$key       = $field['name'];
		$choices   = array(
			'all' => __( 'All', 'acf-rmf' ),
		);
		foreach ( $all_mimes as $mime ) {
			$choices[ $mime ] = $mime;
		}
		?>
		<tr class="field_option field_option_mime_types">
			<td class="label">
				<label><?php _e( 'MIME types', 'acf-rmf' ); ?></label>
				<p><?php _e( 'Specify mime type.', 'acf-rmf' ) ?></p>
			</td>
			<td>
				<?php
					do_action( 'acf/create_field', array(
						'type'     => 'select',
						'name'     => 'fields[' . $key . '][post_mime_type]',
						'value'    => $field['post_mime_type'],
						'choices'  => $choices,
						'multiple' => 1,
					) );
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * callback function for filter "acf/fields/relationship/query"
	 *
	 * Add "post_mime_type" property in WP_Query args if mime types are selected.
	 *
	 * @since    1.0.0
	 *
	 * @param $options  WP_Query args
	 * @param $field    ACF field types
	 *
	 * @return mixed $options	- the modified options
	 */
	public function acf_rmf_query_post_args( $options, $field ) {
		if ( 'attachment' == $options['post_type'] ) {
			if ( ! empty( $field['post_mime_type'] ) ) {
				$mime_type = $field['post_mime_type'];
				if ( 'all' != $mime_type[0] ) {
					$options['post_mime_type'] = $mime_type;
				}
			}
		}
		return $options;
	}
}
