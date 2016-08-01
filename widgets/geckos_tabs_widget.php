<?php
class Geckos_Tabs_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// store the widget options
		$widget_ops = array(
			'classname' => 'geckos_tabs_widget content-vertical-massive clearfix', //classnames added to the list <li> element of the widget
			'description' => __('Add this widget to the widget area of your choice and display your posts as vertical tabs', 'geckos-kit'),
		);
		parent::__construct( 'geckos_tabs_widget', __('Geckos Tabs', 'geckos-kit'), $widget_ops ); // pass it to WP_Widget
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// set the default values of each option
		$defaults = array( 'title' => __('Title', 'geckos-kit'), 'message' => __('Short description', 'geckos-kit'), 'tabs_color' => '#e3e3e3' );
		// pull in the instance values (widget settings), array is empty if the widget was just added to the widget area
		$instance = wp_parse_args((array) $instance, $defaults);
		if(isset($instance['title'])) {
			$title = esc_attr($instance['title']);
		}
		if(isset($instance['message'])) {
    	$message = esc_attr($instance['message']);
		}
		if(isset($instance['tabs_color'])) {
			$tabs_color = esc_attr($instance['tabs_color']);
		}
    ?>
     <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'geckos-kit'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
		<p>
      <label for="<?php echo $this->get_field_id('message'); ?>"><?php _e('Description', 'geckos-kit'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" type="text" value="<?php echo $message; ?>" />
    </p>
		<p>
      <label for="<?php echo $this->get_field_id( 'tabs_color' ); ?>"><?php _e( 'Tabs Color:', 'geckos-kit' ); ?></label>
      <input class="color-picker widefat" name="<?php echo $this->get_field_name( 'tabs_color' ); ?>" id="<?php echo $this->get_field_id( 'tabs_color' ); ?>" type="text" data-alpha="true" value="<?php echo $tabs_color; ?>" />
    </p>
    <?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		// sanitizing user entered data using the strip_tags PHP function
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['message'] = strip_tags($new_instance['message']);
		$instance['tabs_color'] = strip_tags($new_instance['tabs_color']);

        return $instance;
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		extract( $args );
    $title 						= apply_filters('widget_title', $instance['title']);
    $message 					= $instance['message'];
		$tabs_color 			= $instance['tabs_color'];
    ?>
          <?php echo $before_widget; ?>

					<div id="geckos-tabs">
						<div class="section-title clearfix text-center">
            <?php if ( $title ) { ?>
                <h3 class="heading"><?php echo $title; ?></h3>
						<?php } ?>
								<div class="excerpt"><?php echo $message; ?></div>
						</div>
						<div class="tabs-container container" style="min-height: 500px;">
								<?php
								$args = array(
							        'post_type' => 'geckos-tabs',
							        'post_status' => 'publish',
							        'nopaging' => true,
							        'order' => 'ASC',
							        'orderby' => 'menu_order'
						    	);

						    $query = new WP_Query( $args );

						    if ( $query->have_posts() ) :
								$tabs_item_number = 0;
						    	while ( $query->have_posts() ) : $query->the_post();
									// retrieve the _geckos_icon current value
									$post_id = get_the_ID();
									$icon = get_post_meta( $post_id, '_geckos_icon', true );?>

									<input type="radio" class="<?php echo 'tab-'.$tabs_item_number ?>" name="tab" checked="<?php if( $tabs_item_number == 0) echo 'checked'; ?>">
									<h3><?php the_title(); ?></h3><i class="fa fa-<?php echo $icon; ?>"></i>

									<?php
									$tabs_item_number++;
									endwhile;

									wp_reset_postdata();

									endif; ?>
									</ul>

							<div class="tab-content">

								<style type="text/css">
									.tabs-container > input:hover + h3,
									.tabs-container > input:checked + h3 {
									  background: <?php echo $tabs_color; ?>;
									}
									.tab-overlay {
										background: rgba(<?php gcko_hex_to_rgb($tabs_color, 0.8); ?>);
									}
								</style>

										<?php
										// second loop for tab content
										$query = new WP_Query( $args );

								    if ( $query->have_posts() ) :
										$tabs_item_number = 0;

								    	while ( $query->have_posts() ) : $query->the_post();
											$thumb_id = get_post_thumbnail_id();
											$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'full', true);
											$thumb_url = $thumb_url_array[0];?>

											<div class="tab-pane <?php echo 'tab-item-'.$tabs_item_number ?>" style="background: url('<?php echo $thumb_url; ?>') no-repeat center center; background-size: cover;">
												<div class="tab-overlay">
													<p><?php	echo the_content(); ?></p>
												</div>
										  </div>

											<?php $tabs_item_number++;
											endwhile;
											wp_reset_postdata();
										endif; ?>
							</div> <!-- .tabs-content -->
						</div> <!-- .tabs-container -->
					</div> <!-- #geckos-tabs -->

          <?php echo $after_widget; ?>
    <?php
	}
} // end class

/**
 * Hooks
 */

// hook the custom register function after the default widgets have been registered
add_action('widgets_init', 'geckos_register_tabs_widget');


/**
 * Custom functions
 */

function geckos_register_tabs_widget(){
	register_widget('Geckos_Tabs_Widget');
}

function gcko_hex_to_rgb($hex, $opacity = "1") {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }

   echo $r . ',' . $g . ',' . $b . ',' . $opacity;
}
