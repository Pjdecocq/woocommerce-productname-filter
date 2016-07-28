<?php
/*
 * Plugin Name: Woocommerce Productname Filter
 * Version: 1.0
 * Plugin URI: http://wijzijnstuurlui.nl
 * Description: Registers a widget with a product name filter.
 * Author: Stuurlui
 * Author URI: http://wijzijnstuurlui.nl
 */
 defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
 
/* Register widget */
 function wpn_filter() {
 	register_widget( 'wpn_filter' );
 }
 add_action( 'widgets_init', 'wpn_filter' );
 
/* Enqueue .js file */
function _enqueue() {
	wp_enqueue_script('wpn_filter', '/wp-content/plugins/woocommerce-productname-filter/wpn_filter.js', true, true);
}
add_action( 'wp_enqueue_scripts', '_enqueue' );
 
/* Check if class already exists to prevent error messages */
 if( !class_exists( 'wpn_filter' ) ) {
 	
	 class wpn_filter extends WP_Widget {
	 	
		/**
		 * Tag identifier used by file includes and selector attributes.
		 * @var string
		 */
		protected $tag = 'wpn_filter';
		
		protected $version = '1.0';
	
		
		public function __construct() {
			
			$widget_details = array(
				'classname'		=> 'wpn-filter',
				'description'	=> 'Sorts product by name, making use of a widget.'
			);
			
			parent::__construct( 'wpn_filter', 'Woocommerce Productname Filter', $widget_details );
			
		}
		
		public function form( $instance ) {
			
			// Backend form
			$title = '';
			if( !empty($instance['title'] ) ) {
				$title = $instance['title'];
			}
			?>
			
			<p>
				<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />	
			</p>
			
			<?php

		}
		
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( !empty( $new_instance['title'] )  ? strip_tags( $new_instance['title'] ) : '');
			
			return $new_instance;
			
		}

		public function widget( $args, $instance ) {
			
			/* Function to make correct redirect title */
			function strip_title( $key ) {		
				$title_val = str_replace(' ', '-', strtolower( $key ) );
				return $title_val;		
			}
			
			$product_args = array(
				'post_type'		=> 'product',
				'post_status'	=> 'publish',
				'orderby'		=> 'name',
				'order'			=> 'DESC',
				'posts_per_page'=> -1
			);
			$all_products = get_posts($product_args);
			
			$product_filter = '<select id="'.$this->tag.'" name="'.$this->tag.'">';
			foreach($all_products as $item){			
				$product_filter.= '<option value="'.strip_title($item->guid).'">'.$item->post_title.'</option>';				
			}	
			$product_filter.= '</select>';		
			
			// Frontend display of widget HTML
			echo $args['before_widget'];
			if( !empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
			echo $product_filter;
			echo $args['after_widget'];
			
		}
		
	 } // end class
 } // end class_exists() function