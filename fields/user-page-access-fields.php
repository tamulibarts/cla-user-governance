<?php
/**
 * The file that defines the Author post type custom fields
 *
 * @link       https://github.tamu.edu/liberalarts-web/cla-user-governance/blob/master/fields/author-fields.php
 * @since      1.0.0
 * @package    cla-user-governance
 * @subpackage cla-user-governance/fields
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_5fd297ff1424c',
			'title'                 => 'Page Access',
			'fields'                => array(
				array(
					'key'               => 'field_5fd299302bcff',
					'label'             => 'User Page Access',
					'name'              => 'user_page_access',
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'collapsed'         => '',
					'min'               => 0,
					'max'               => 50,
					'layout'            => 'table',
					'button_label'      => 'Add User',
					'sub_fields'        => array(
						array(
							'key'               => 'field_5fd299652bd00',
							'label'             => 'User',
							'name'              => 'user',
							'type'              => 'user',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'role'              => '',
							'allow_null'        => 0,
							'multiple'          => 0,
							'return_format'     => 'array',
						),
						array(
							'key'               => 'field_5fd2996d2bd01',
							'label'             => 'Pages',
							'name'              => 'pages',
							'type'              => 'post_object',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'post_type'         => array(
								0 => 'page',
								1 => 'post',
							),
							'taxonomy'          => '',
							'allow_null'        => 0,
							'multiple'          => 1,
							'return_format'     => 'id',
							'ui'                => 1,
						),
					),
				),
				array(
					'key'               => 'field_5fd29a782bd03',
					'label'             => 'Who can see this settings page',
					'name'              => 'who_can_see_settings_page',
					'type'              => 'user',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'role'              => '',
					'allow_null'        => 1,
					'multiple'          => 1,
					'return_format'     => 'array',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'cla-user-page-access',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);

endif;
