<?php
/*
Plugin Name: Rampant Showcaser
Plugin URI: http://therampant.com/
Description: rotating showcase images
Author: Rampant Creative Group, Ben Triola
Version: 1
Author URI: http://therampant.com/
*/
 
 //defines what the widget outputs

    add_action( 'init', 'create_showcaser_post_type' );
	
function create_showcaser_post_type() {
	
	if ( function_exists( 'add_theme_support' ) ) { 
 	 add_theme_support( 'post-thumbnails' ); 
	 add_image_size( 'showcaser', 600, 150, true );
	}	
	
	//create a post type for showcasers	
	register_post_type('showcaser', array(
		 'label' => __('Background Images'),
		 'singular_label' => __('Background Image'),
		 'public' => true,
		 'show_ui' => true,
		 'capability_type' => 'post',
		 'hierarchical' => false,
		 'rewrite' => true,
		 'query_var' => false,
		 //show only the fields for title, and featured image
		 'supports' => array('title', 'thumbnail' )
	));
	
	
	//and init the metaboxes
	add_action("admin_init", "showcaser_fields");
	
	function showcaser_fields(){
	  add_meta_box("hyperlink_meta", "Hyperlink", "hyperlink_meta", "showcaser", "normal", "low");
	}
	
	function hyperlink_meta() {
		  global $post;
		  $custom = get_post_custom($post->ID);
		  $hyperlink = $custom["hyperlink"][0];
		  ?>
		
			
		<div class="my_meta_control">
			<div id="hyperlinkmeta" class="metabox"><input type="text" name="hyperlink" value="<?php echo $hyperlink;?>"/></div>
		</div>
		  <?php
		}
		
	add_action('save_post', 'save_showcaser_details', 10, $post);
		
	function save_showcaser_details($post_ID = 0) {
		global $post_ID;
		$post_ID = (int) $post_ID;
		$post_type = get_post_type( $post_ID );
		$post_status = get_post_status( $post_ID );
		
		$fullhyperlink = $_POST["hyperlink"];
		if ($fullhyperlink == null || $fullhyperlink == "") {}
		elseif (strpos( $fullhyperlink, "http://") !== false) { }
		else { $fullhyperlink = "http://" . $fullhyperlink; }
		
		update_post_meta($post_ID, "hyperlink", $fullhyperlink); 
		return $post_ID;
	}

}

//--------------- Create a Function to call from within templates //

function makeShowcaser() {
	
	?><script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
            <script type="text/javascript" src="http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.2.74.js"></script>
            
            <script type="text/javascript">
            //<![CDATA[
            $(document).ready(function() {
                $('#slideshow')
                .before('<div id="slidenav">') 
                .cycle({ 
                fx:     'fade', 
                speed:   1500, 
                timeout: 9000,
                pause:   1 ,
                pager:  '#slidenav',
                cleartypeNoBg:   true
            });
            });
            //]]>
            </script>
            
            <div id="slideshow">
              <?php $args = array(
            'numberposts'     => 100,
            'orderby'         => 'post_date',
            'order'           => 'ASC',
            'post_type'       => 'showcaser',
            'post_status'     => 'publish' ); 	
                    
            $myposts = get_posts( $args );	
            foreach( $myposts as $post ) : setup_postdata($post); ?>
            
            <?php $hyperlink = get_post_meta($post->ID, 'hyperlink', true); ?>
                       
            <div class="slide"><?php if ($hyperlink) { echo '<a href="'.$hyperlink. '">'; } 
			//echo '<a href=\"'.$hyperlink. '\">';
			 echo get_the_post_thumbnail( $post->ID, 'slideshow'); echo "</a>";?></div>
            <?php endforeach; ?>
            </div>
	
	<?php
}
 