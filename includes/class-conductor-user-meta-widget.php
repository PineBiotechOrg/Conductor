<?php

/**
 * Widget API: Conductor_User_Meta_Widget class
 *
 * @package Joshua McKendall
 * @subpackage Conductor/includes
 * @since 1.0.0
 */

/**
 * Core class used to implement an User Meta widget.
 *
 * @since 1.0.0
 *
 * @see WP_Widget
 */
class Conductor_User_Meta_Widget extends WP_Widget {


	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'		=> 'conductor_user_meta_widget',
			'description'	=> esc_html__('A User preview widget', 'conductor'),
		);

		parent::__construct('conductor_user_meta_widget', esc_html__('User Meta', 'conductor'), $widget_ops);
	}

	/**
	 * Display the login widget.
	 *
	 * @since 1.9.0
	 *
	 * @see WP_Widget::widget() for description of parameters.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings, as saved by the user.
	 */
	public function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';

		/**
		 * Filters the title of the Login widget.
		 *
		 * @since 1.9.0
		 * @since 2.3.0 Added 'instance' and 'id_base' to arguments passed to filter.
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];

		echo $args['before_title'] . esc_html( $title ) . $args['after_title']; ?>

		<?php if ( is_user_logged_in() ) : ?>

			<?php
			/**
			 * Fires before the display of widget content if logged in.
			 *
			 * @since 1.9.0
			 */
			do_action( 'conductor_before_user_meta_widget_loggedin' ); ?>

			<div class="conductor-user-meta-widget-user-avatar">
				<a href="<?php echo bp_loggedin_user_domain(); ?>">
					<?php bp_loggedin_user_avatar( 'type=thumb&width=30&height=30' ); ?>
				</a>

				<div class="bp-login-widget-user-links">
					<div class="bp-login-widget-user-link"><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></div>
				</div>
			</div>

			<div class="conductor-user-menu">

				<?php

				/**
				 * Fires after the display of widget content if logged in.
				 *
				 * @since 1.9.0
				 */
				do_action( 'conductor_after_user_meta_widget_loggedin' ); ?>

			</div>

		<?php else : ?>

			<?php

			/**
			 * Fires before the display of widget content if logged out.
			 *
			 * @since 1.9.0
			 */
			do_action( 'conductor_before_user_meta_widget_loggedout' ); ?>

			<div class="conductor-user-menu">

				<?php echo conductor_user_meta_loggedout_links(); ?>

			</div>



				<?php

				/**
				 * Fires inside the display of the login widget form.
				 *
				 * @since 2.4.0
				 */
				do_action( 'conductor_user_meta_widget_form' ); ?>

			<?php

			/**
			 * Fires after the display of widget content if logged out.
			 *
			 * @since 1.9.0
			 */
			do_action( 'conductor_after_user_meta_widget_loggedout' ); ?>

		<?php endif;

		echo $args['after_widget'];
	}

	/**
	 * Update the login widget options.
	 *
	 * @since 1.9.0
	 *
	 * @param array $new_instance The new instance options.
	 * @param array $old_instance The old instance options.
	 * @return array $instance The parsed options to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	/**
	 * Output the login widget options form.
	 *
	 * @since 1.9.0
	 *
	 * @param array $instance Settings for this widget.
	 * @return void
	 */
	public function form( $instance = array() ) {

		$settings = wp_parse_args( $instance, array(
			'title' => '',
		) ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'buddypress' ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $settings['title'] ); ?>" /></label>
		</p>

		<?php
	}


}