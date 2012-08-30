<?php
/*
Plugin Name: Rampant Showcaser
Plugin URI: http://therampant.com/
Description: rotating showcase images
Author: Rampant Creative Group, Ben Triola
Version: 1
Author URI: http://therampant.com/
*/
 

//enque some scripts
add_action('wp_enqueue_scripts', 'rampant_showcaser_scripts_method');

if (! function_exists('rampant_showcaser_scripts_method') {
	function rampant_showcaser_scripts_method() {
	    wp_register_script( 'jcycle', 'http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.2.74.js');
	    wp_register_style( 'showcaserstyles', plugins_url().'/rampantshowcaser/showcaserstyle.css');
	    wp_enqueue_script( 'jquery' );
	    wp_enqueue_script( 'jcycle' );
	    wp_enqueue_style( 'showcaserstyles' );
	}    
}
 

 //defines what the widget outputs

add_action( 'init', 'create_showcaser_post_type' );
   
if (! function_exists('create_showcaser_post_type') {
	
	function create_showcaser_post_type() {
		
		if ( function_exists( 'add_theme_support' ) ) { 
 	 add_theme_support( 'post-thumbnails' ); 
	 add_image_size( 'showcaser', 930, 450, true );
	}	
		
		//create a post type for showcasers	
		register_post_type('showcaser', array(
			 'label' => __('Showcaser Images'),
			 'singular_label' => __('Showcaser Image'),
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
		add_action("admin_init", "rampant_showcaser_fields");
		
		
		if (! function_exists('rampant_showcaser_fields')) {
			function showcaser_fields(){
			  add_meta_box("hyperlink_meta", "Showcaser Details", "hyperlink_meta", "showcaser", "normal", "low");
			}
		}
		
		
		if (! function_exists()) {
		  function hyperlink_meta() {
		  global $post;
		  $custom = get_post_custom($post->ID);
		  $hyperlink = $custom["hyperlink"][0];
		  $overlayHeader = $custom["overlayHeader"][0];
		  $overlayText = $custom["overlayText"][0];
		  ?>				
			<div class="my_meta_control">
				<div id="hyperlinkmeta" class="metabox">
					<label for="hyperlink">Hyperlink: </label>
					<input type="text" name="hyperlink" value="<?php echo $hyperlink;?>"/>
				</div>
				<div id="overlay" class="metabox">
					<label for="overlay">Overlay Header</label>
					<input type="text" name="overlayHeader" value="<?php echo $overlayHeader;?>"/>
				</div><br/>
				<div id="overlayText" class="metabox">
					<label for="overlay">Overlay Description</label>
					<textarea name="overlayText" style="vertical-align:top;"> <?php echo $overlayText;?></textarea>
				</div>
			</div>
			  <?php
			}
		} //end function_exists
			
		add_action('save_post', 'save_rampant_showcaser_details', 10, $post);
			
		
		if (! function_exists('save_rampant_showcaser_details'){
			function save_rampant_showcaser_details($post_ID = 0) {
			global $post_ID;
			$post_ID = (int) $post_ID;
			$post_type = get_post_type( $post_ID );
			$post_status = get_post_status( $post_ID );
			
			$fullhyperlink = $_POST["hyperlink"];
			if ($fullhyperlink == null || $fullhyperlink == "") {}
			elseif (strpos( $fullhyperlink, "http://") !== false) { }
			else { $fullhyperlink = "http://" . $fullhyperlink; }
			
			update_post_meta($post_ID, "hyperlink", $fullhyperlink);
	
			if ($_POST['overlayHeader']){
			update_post_meta($post_ID, "overlayHeader", $_POST['overlayHeader']); 
			}
			if ($_POST['overlayText']){
			update_post_meta($post_ID, "overlayText", $_POST['overlayText']); 
			}
			
			return $post_ID;
			}
		} //end function exists save rampant details
	
	}
} //end if function exists



//--------------- Create a Function to call from within templates //

if (! function_exists('makeShowcaser') {
	function makeShowcaser() {
		
		?>
	            
	            <script type="text/javascript">
	            //<![CDATA[
	            jQuery(document).ready(function() {
	                jQuery('#slideshow')
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
	            
	            <div class="slidewrap">
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
		            <?php $overlayHeader = get_post_meta($post->ID, 'overlayHeader', true); ?>
		            <?php $overlayText = get_post_meta($post->ID, 'overlayText', true); ?>
		                       
		            <div class="slide"><?php if ($hyperlink) { echo '<a href="'.$hyperlink. '">'; } 
					//echo '<a href=\"'.$hyperlink. '\">';
					 echo get_the_post_thumbnail( $post->ID, 'showcaser'); echo "</a>";?>
					 
					 	<div class="slideoverlay">
					 		<h2><?php echo $overlayHeader; ?></h2><p><?php echo $overlayText; ?></p>
					 	</div>
		            </div> 
		            <?php endforeach; ?>
		            </div>
	            </div>
	           		
		<?php
	}
}
 
