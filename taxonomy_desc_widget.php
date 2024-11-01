<?php
/*
Plugin Name: Taxonomy Description Widget
Plugin URI: http://geoffreydesigns.com
Description: Geoffrey Burdett
Version: 1.0
Author: Geoffrey Burdett
Author URI: http://geoffreydesigns.com
License: http://www.gnu.org/licenses/gpl-3.0.html
Tags: Category, Taxonomy, Tags, Description, Widget
*/

class Taxonomy_Desc_Widget extends WP_Widget 
{

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() 
    {
		parent::__construct(
			'taxonomy_description_widget',
			__('Taxonomy Description Widget', 'taxonomy_description_widget'),
			array( 'description' => __( 'The description of the current page\'s category or other taxonomy.', 'taxonomy_description_widget' ) )
		);
	}


	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) 
    {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$taxonomies = apply_filters( 'taxonomies', $instance['taxonomies'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) 
        {
			echo $args['before_title'] . $title . $args['after_title'];
		}
        foreach($taxonomies AS $taxonomy)
        {
            $terms = get_the_terms($post->id, $taxonomy);
            foreach($terms AS $term)
            {
                if ($term->description !== '')
                {
                    echo __('<h4>' . $term->name . '</h4>', 'taxonomy_description_widget' );
                    echo __( '<p>' . $term->description . '</p>', 'taxonomy_description_widget' );
                }
            }
        }
        echo $args['after_widget'];
	}


	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) 
    {
		if ( isset( $instance[ 'title' ] ) ) 
        {
			$title = $instance[ 'title' ];
		}
		else 
        {
			$title = __( '', 'taxonomy_description_widget' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        
        <p>
		<label for="<?php echo $this->get_field_id( 'taxonomies[]' ); ?>"><?php _e( 'Select Taxonomies to Display:' ); ?></label><br />
        <?php 
        $args=array(
            'public'   => true
        );
        $output = 'objects';
        $taxonomies = get_taxonomies($args,$output);
        if  ($taxonomies) 
        {
            printf (
                '<select multiple="multiple" name="%s[]" id="%s" class="widefat">',
                $this->get_field_name('taxonomies'),
                $this->get_field_id('taxonomies')
            );
            foreach ($taxonomies as $taxonomy ) 
            {
                if (in_array($taxonomy->name,$instance['taxonomies'])) 
                {
                    $select = 'selected="selected"';
                }
                else
                {
                    $select = '';
                }
                
                echo "<option value='{$taxonomy->name}' $select>{$taxonomy->labels->name}</option>";
            }
    
            echo '</select>';
        }  
	}


	/**
	 * Processes widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        foreach($new_instance['taxonomies'] as $key => $taxonomy)
        {
		    $instance['taxonomies'][$key] = ( ! empty( $taxonomy ) ) ? strip_tags( $taxonomy ) : '';
        }

		return $instance;
	}
}


// register widget
function register_taxonomy_description_widget() {
    register_widget( 'Taxonomy_Desc_Widget' );
}
add_action( 'widgets_init', 'register_taxonomy_description_widget' );