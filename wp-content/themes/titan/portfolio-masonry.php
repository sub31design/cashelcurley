<?php
/**
 * The main template file for display portfolio page.
 *
 * Template Name: Portfolio Masonry
 * @package WordPress
 */

/**
*	Get Current page object
**/
$page = get_page($post->ID);
$current_page_id = '';

if(isset($page->ID))
{
    $current_page_id = $page->ID;
}

//prepare data for pagintion
$offset_query = '';
if(!isset($_GET['page']) OR empty($_GET['page']) OR $_GET['page'] == 1)
{
    $current_page = 1;
}


//Get content gallery
$args = array(
    'numberposts' => -1,
    'order' => 'ASC',
    'orderby' => 'menu_order',
    'post_type' => array('portfolios'),
    'portfoliosets' => '',
);
if(!empty($term))
{
    $args['portfoliosets'].= $term;
}

$page_photo_arr = get_posts($args);


//Get all portfolio items for paging
$args = array(
    'numberposts' => -1,
    'order' => 'ASC',
    'orderby' => 'menu_order',
    'post_type' => array('portfolios'),
    'portfoliosets' => '',
);
if(!empty($term))
{
    $args['portfoliosets'].= $term;
}

$all_photo_arr = get_posts($args);

get_header(); 

?>

<br class="clear"/>
</div>

<?php

//Get Page background style
$bg_style = get_post_meta($current_page_id, 'page_bg_style', true);

if($bg_style == 'Static Image')
{
    if(has_post_thumbnail($current_page_id, 'full'))
    {
        $image_id = get_post_thumbnail_id($current_page_id); 
        $image_thumb = wp_get_attachment_image_src($image_id, 'full', true);
        $pp_page_bg = $image_thumb[0];
    }
    else
    {
    	$pp_page_bg = get_template_directory_uri().'/example/bg.jpg';
    }
    
    wp_enqueue_script("script-static-bg", get_template_directory_uri()."/templates/script-static-bg.php?bg_url=".$pp_page_bg, false, THEMEVERSION, true);
    
} // end if static image
else
{
    $page_bg_gallery_id = get_post_meta($current_page_id, 'page_bg_gallery_id', true);
    wp_enqueue_script("script-supersized-gallery", get_template_directory_uri()."/templates/script-supersized-gallery.php?gallery_id=".$page_bg_gallery_id, false, THEMEVERSION, true);
?>

<div id="thumb-tray" class="load-item">
    <div id="thumb-back"></div>
    <div id="thumb-forward"></div>
    <a id="prevslide" class="load-item"><img src="<?php echo get_template_directory_uri(); ?>/images/arrow_back.png" alt=""/></a>
    <a id="nextslide" class="load-item"><img src="<?php echo get_template_directory_uri(); ?>/images/arrow_forward.png" alt=""/></a>
</div>

<input type="hidden" id="pp_image_path" name="pp_image_path" value="<?php echo get_template_directory_uri(); ?>/images/"/>

<?php
}
?>

<?php
	//Display main gallery contents
    if(!empty($all_photo_arr))
    {
?>

<?php
if($bg_style == 'Static Image')
{
?>
<div class="page_control_static">
    <a id="page_minimize" href="#">
    	<img src="<?php echo get_template_directory_uri(); ?>/images/icon_zoom.png" alt=""/>
    </a>
    <a id="page_maximize" href="#">
    	<img src="<?php echo get_template_directory_uri(); ?>/images/icon_plus.png" alt=""/>
    </a>
</div>
<?php
}
else
{
?>
<div class="page_control">
    <a id="page_minimize" href="#">
    	<img src="<?php echo get_template_directory_uri(); ?>/images/icon_minus.png" alt=""/>
    </a>
    <a id="page_maximize" href="#">
    	<img src="<?php echo get_template_directory_uri(); ?>/images/icon_plus.png" alt=""/>
    </a>
</div>
<?php
}
?>

<?php
$page_audio = get_post_meta($current_page_id, 'page_audio', true);

if(!empty($page_audio))
{
?>
<div class="page_audio">
	<?php echo do_shortcode('[audio width="30" height="30" src="'.$page_audio.'"]'); ?>
</div>
<?php
}
?>

<!-- Begin content -->
<div id="page_content_wrapper" class="fade-in two">
    
    <div class="inner">

    	<div class="inner_wrapper">
    	
	    	<div id="page_caption">
	    		<h1 class="cufon"><?php echo $post->post_title; ?></h1>
	    	</div>
	    	
	    	<?php
	    	    if(!empty($post->post_content))
	    	    {
	    	?>
	    	    <?php echo pp_apply_content($post->post_content); ?>
	    	<?php
	    	    }
	    	?>
	    	
	    <div id="page_main_content" class="sidebar_content full_width transparentbg">
	
		<?php
		    //Get social media sharing option
		    $pp_social_sharing = get_option('pp_social_sharing');
		    
		    if(!empty($pp_social_sharing))
		    {
		?>
		<!-- AddThis Button BEGIN -->
		<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
		<a class="addthis_button_preferred_1"></a>
		<a class="addthis_button_preferred_2"></a>
		<a class="addthis_button_preferred_3"></a>
		<a class="addthis_button_preferred_4"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
		</div>
		<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ppulpipatnan"></script>
		<!-- AddThis Button END -->
		<br class="clear"/>
		<?php
		    }
		?>
		
		<div class="gallery_mansory_wrapper">
    	
    	<?php
    		foreach($all_photo_arr as $key => $portfolio_item)
			{
				$thumb_image_url = '';
				$small_image_url = get_template_directory_uri().'/images/000_70.png';
    			$masory_class = '';
    			$image_id = get_post_thumbnail_id($portfolio_item->ID);
						
				if(!empty($portfolio_item->guid))
    			{
				    $image_url[0] = $portfolio_item->guid;
    			    $small_image_url = wp_get_attachment_image_src($image_id, 'gallery_m', true);
    			    $double_width_image_url = wp_get_attachment_image_src($image_id, 'gallery_m2w', true);
    			    $double_height_image_url = wp_get_attachment_image_src($image_id, 'gallery_m2h', true);
				}
				
				$last_class = '';
    			$thumb_image_url = $small_image_url[0];
    			$thumb_width = $small_image_url[1];
    			$thumb_height = $small_image_url[2];
    			
    			if($key == 0 OR $key == 6 OR $key == 11)
    			{
    				$thumb_image_url = $double_width_image_url[0];
    				$thumb_width = $double_width_image_url[1];
    				$thumb_height = $double_width_image_url[2];
    				$masory_class = 'double_width';
    			}
    			
    			if($key == 5 OR $key == 8)
    			{
    				$thumb_image_url = $double_height_image_url[0];
    				$thumb_width = $double_height_image_url[1];
    				$thumb_height = $double_height_image_url[2];
    				$masory_class = 'double_height';
    			}
				
				//Get portfolio content permalink
				$portfolio_link_url = get_post_meta($portfolio_item->ID, 'portfolio_link_url', true);
				
				if(empty($portfolio_link_url))
				{
				    $permalink_url = get_permalink($portfolio_item->ID);
				}
				else
				{
				    $permalink_url = $portfolio_link_url;
				}
    	?>
    	
    	<div class="mansory_thumbnail <?php echo $masory_class; ?>">
    		<?php 
			if(!empty($image_url[0]))
			{
		?>		
				
				<?php
						$portfolio_type = get_post_meta($portfolio_item->ID, 'portfolio_type', true);
						$portfolio_video_id = get_post_meta($portfolio_item->ID, 'portfolio_video_id', true);
						
						switch($portfolio_type)
						{
						case 'External Link':
						default:
					?>
					<img src="<?php echo $thumb_image_url; ?>" alt="" class="one_fourth_img"/>
					
					<div class="mask" style="width:100%;height:100%">
						<a href="<?php echo $permalink_url; ?>">
                       		<h6><?php echo $portfolio_item->post_title?></h6>
                       		<span class="button white"><?php _e( 'Open video', THEMEDOMAIN ); ?></span>
						</a>
                    </div>
					
					<?php
						break;
						//end external link
						
						case 'Portfolio Content':
        				default:
        			?>
        			<img src="<?php echo $thumb_image_url; ?>" alt="" class="one_fourth_img"/>
					
					<div class="mask" style="width:100%;height:100%">
						<a href="<?php echo get_permalink($portfolio_item->ID); ?>">
                       		<h6><?php echo $portfolio_item->post_title?></h6>
                       		<span class="button white"><?php _e( 'Open video', THEMEDOMAIN ); ?></span>
						</a>
                    </div>
        			
        			<?php
        				break;
        				//end Portfolio Content
						
						case 'Youtube Video':
						default:
					?>
					
					<img src="<?php echo $thumb_image_url; ?>" alt="" class="one_fourth_img"/>
					
					<div class="mask" style="width:100%;height:100%">
						<a data-title="<?php echo $portfolio_item->post_title; ?>" href="#video_<?php echo $portfolio_video_id; ?>" class="lightbox_youtube">
                       		<h6><?php echo $portfolio_item->post_title?></h6>
                       		<span class="button white"><?php _e( 'Open video', THEMEDOMAIN ); ?></span>
						</a>
                    </div>
						
					<div style="display:none;">
			    	    <div id="video_<?php echo $portfolio_video_id; ?>" style="width:900px;height:488px">
			    	        
			    	        <iframe title="YouTube video player" width="900" height="488" src="http://www.youtube.com/embed/<?php echo $portfolio_video_id; ?>?theme=dark&rel=0&wmode=transparent" frameborder="0" allowfullscreen></iframe>
			    	        
			    	    </div>	
			    	</div>
					
					<?php
						break;
						//end youtube
					
					case 'Vimeo Video':
					?>
					<img src="<?php echo $thumb_image_url; ?>" alt="" class="one_fourth_img"/>

					<div class="mask" style="width:100%;height:100%">
						<a data-title="<?php echo $portfolio_item->post_title; ?>" href="#video_<?php echo $portfolio_video_id; ?>" class="lightbox_vimeo">
                       		<h6><?php echo $portfolio_item->post_title?></h6>
                       		<span class="button white"><?php _e( 'Open video', THEMEDOMAIN ); ?></span>
						</a>
                    </div>
						
					<div style="display:none;">
			    	    <div id="video_<?php echo $portfolio_video_id; ?>" style="width:900px;height:506px">
			    	    
			    	        <iframe src="http://player.vimeo.com/video/<?php echo $portfolio_video_id; ?>?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="900" height="506" frameborder="0"></iframe>
			    	        
			    	    </div>	
			    	</div>
					
					<?php
						break;
						//end vimeo
						
					case 'Self-Hosted Video':
					
						//Get video URL
						$portfolio_mp4_url = get_post_meta($portfolio_item->ID, 'portfolio_mp4_url', true);
						$preview_image = wp_get_attachment_image_src($image_id, 'large', true);
					?>
					<img src="<?php echo $thumb_image_url; ?>" alt="" class="one_fourth_img"/>

					<div class="mask" style="width:100%;height:100%">
						<a data-title="<?php echo $portfolio_item->post_title; ?>" href="#video_self_<?php echo $key; ?>" class="lightbox_vimeo">
                       		<h6><?php echo $portfolio_item->post_title?></h6>
                       		<span class="button white"><?php _e( 'Open video', THEMEDOMAIN ); ?></span>
						</a>
                    </div>
						
					<div style="display:none;">
			    	    <div id="video_self_<?php echo $key; ?>" style="width:900px;height:488px">
			    	    
			    	        <div id="self_hosted_vid_<?php echo $key; ?>">JW Player goes here</div>
			    	        <?php do_shortcode('[jwplayer id="self_hosted_vid_'.$key.'" file="'.$portfolio_mp4_url.'" image="'.$preview_image[0].'" width="900" height="488"]'); ?>
			    	        
			    	    </div>	
			    	</div>
					
					<?php
						break;
						//end self-hosted
					?>
					
					<?php
						}
						//end switch
					?>
		<?php
			}		
		?>		
    		
    	</div>
    	
    	<?php
    		}
    	?>
    	</div>
	    </div>
	    	
	</div>
	    
</div>

<br class="clear"/>
<?php get_footer(); ?>

<?php
    }
?>