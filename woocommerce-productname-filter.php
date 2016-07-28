<?php
/*
 * Plugin Name: Woocommerce Productname Filter
 * Version: 1.0
 * Plugin URI: https://github.com/Pjdecocq/
 * Description: Registers a widget with a product name filter.
 * Author: Paul de Cocq
 * Author URI: https://github.com/Pjdecocq/
 */
 defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
 
/* Register widget */
 function wpn_filter() {
 	register_widget( 'wpn_filter' );
 }
 add_action( 'widgets_init', 'wpn_filter' );
 
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
				'order'			=> 'ASC',
				'posts_per_page'=> -1
			);
			$all_products = get_posts($product_args);
			
			$product_filter = '<select id="'.$this->tag.'" name="'.$this->tag.'">';
			foreach($all_products as $item){			
				$product_filter.= '<option value="'.strip_title($item->guid).'">'.$item->post_title.'</option>';				
			}	
			$product_filter.= '</select>';		
			$this->_enqueue(); // Load neccesary scripts
			
			// Frontend display of widget HTML
			echo $args['before_widget'];
			if( !empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
			echo $product_filter;
			echo $args['after_widget'];
			
		}
		
		/* Enqueue .js file */
		protected function _enqueue() {
				
			$plugin_path = plugin_dir_url(__FILE__);
			wp_enqueue_script($this->tag,  $plugin_path . $this->tag . '.js', true, true);
		}
		
	 } // end class
 } // end class_exists() function
?>
