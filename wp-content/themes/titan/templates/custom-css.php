<?php header("Content-type: text/css; charset: UTF-8"); ?> 

<?php
require_once( '../../../../wp-load.php' );
?>

<?php
	$pp_h1_font_color = get_option('pp_h1_font_color');
	if(!empty($pp_h1_font_color))
	{
?>
.post_header h2, h1, h2, h3, h4, h5, h6, h7, #page_caption h1, #page_content_wrapper .sidebar .content .sidebar_widget li h2, #contact_form label, #commentform label, #page_content_wrapper .sidebar .content .sidebar_widget li h2.widgettitle, h2.widgettitle, .post_date, .pagination span, .pagination a:hover
{
	color: <?php echo $pp_h1_font_color; ?>;
}
<?php
	}
?>

<?php
	$pp_menu_font_size = get_option('pp_menu_font_size');
	
	if(!empty($pp_menu_font_size))
	{
?>
#menu_wrapper .nav ul li a, #menu_wrapper div .nav li a { font-size:<?php echo $pp_menu_font_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_submenu_font_size = get_option('pp_submenu_font_size');
	
	if(!empty($pp_submenu_font_size))
	{
?>
#menu_wrapper .nav ul li ul li a, #menu_wrapper div .nav li ul li a, #menu_wrapper div .nav li.current-menu-parent ul li a { font-size:<?php echo $pp_submenu_font_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_page_header_font_size = get_option('pp_page_header_font_size');
	
	if(!empty($pp_page_header_font_size))
	{
?>
#page_caption h1 { font-size:<?php echo $pp_page_header_font_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_h1_size = get_option('pp_h1_size');
	
	if(!empty($pp_h1_size))
	{
?>
h1 { font-size:<?php echo $pp_h1_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_h2_size = get_option('pp_h2_size');
	
	if(!empty($pp_h2_size))
	{
?>
h2 { font-size:<?php echo $pp_h2_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_h3_size = get_option('pp_h3_size');
	
	if(!empty($pp_h3_size))
	{
?>
h3 { font-size:<?php echo $pp_h3_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_h4_size = get_option('pp_h4_size');
	
	if(!empty($pp_h4_size))
	{
?>
h4 { font-size:<?php echo $pp_h4_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_h5_size = get_option('pp_h5_size');
	
	if(!empty($pp_h5_size))
	{
?>
h5 { font-size:<?php echo $pp_h5_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_h6_size = get_option('pp_h6_size');
	
	if(!empty($pp_h6_size))
	{
?>
h6 { font-size:<?php echo $pp_h6_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_image_title_font_size = get_option('pp_image_title_font_size');
	
	if(!empty($pp_image_title_font_size))
	{
?>
#gallery_caption h2 { font-size:<?php echo $pp_image_title_font_size; ?>px; }
<?php
	}
	
?>

<?php
	$pp_link_color = get_option('pp_link_color');
	
	if(!empty($pp_link_color))
	{
?>
a { color:<?php echo $pp_link_color; ?>; }
<?php
	}
	
?>

<?php
	$pp_hover_link_color = get_option('pp_hover_link_color');
	
	if(!empty($pp_hover_link_color))
	{
?>
#page_content_wrapper a:hover, #page_content_wrapper a:active { color:<?php echo $pp_hover_link_color; ?>; }
<?php
	}
?>

<?php
	$pp_sidebar_font_color = get_option('pp_sidebar_font_color');
	
	if(!empty($pp_sidebar_font_color))
	{
?>
#page_content_wrapper .inner .sidebar_wrapper .sidebar .content { color:<?php echo $pp_sidebar_font_color; ?>; }
<?php
	}
?>

<?php
	$pp_sidebar_link_color = get_option('pp_sidebar_link_color');
	
	if(!empty($pp_sidebar_link_color))
	{
?>
#page_content_wrapper .inner .sidebar_wrapper a { color:<?php echo $pp_sidebar_link_color; ?>; }
<?php
	}
?>

<?php
	$pp_sidebar_hover_link_color = get_option('pp_sidebar_hover_link_color');
	
	if(!empty($pp_sidebar_link_color))
	{
?>
#page_content_wrapper .inner .sidebar_wrapper a:hover, #page_content_wrapper .inner .sidebar_wrapper a:active { color:<?php echo $pp_sidebar_hover_link_color; ?>; }
<?php
	}
?>

<?php
	$pp_footer_font_color = get_option('pp_footer_font_color');
	
	if(!empty($pp_sidebar_font_color))
	{
?>
#footer { color:<?php echo $pp_footer_font_color; ?>; }
<?php
	}
?>

<?php
	$pp_footer_link_color = get_option('pp_footer_link_color');
	
	if(!empty($pp_footer_link_color))
	{
?>
#footer a { color:<?php echo $pp_footer_link_color; ?>; }
<?php
	}
?>

<?php
	$pp_footer_hover_link_color = get_option('pp_footer_hover_link_color');
	
	if(!empty($pp_footer_hover_link_color))
	{
?>
#footer a:hover, #footer a:active { color:<?php echo $pp_footer_hover_link_color; ?>; }
<?php
	}
?>

<?php
	$pp_button_bg_color = get_option('pp_button_bg_color');
	
	if(!empty($pp_button_bg_color))
	{
?>
input[type=submit], input[type=button], a.button, .button, #toTop:hover { 
	background: <?php echo $pp_button_bg_color; ?>;
}
.widget_tag_cloud div a:hover, .meta-tags a:hover, #footer .widget_tag_cloud div a:hover, #footer .meta-tags a:hover, .tag_cloud a:hover, .woocommerce ul.products li.product a.add_to_cart_button, .woocommerce-page ul.products li.product a.add_to_cart_button, .woocommerce ul.products li.product a.add_to_cart_button.loading, .woocommerce-page ul.products li.product a.add_to_cart_button.loading, .woocommerce ul.products li.product a.add_to_cart_button:hover, .woocommerce-page ul.products li.product a.add_to_cart_button:hover, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit, .woocommerce #content input.button, .woocommerce-page a.button, .woocommerce-page button.button, .woocommerce-page input.button, .woocommerce-page #respond input#submit, .woocommerce-page #content input.button, .woocommerce .widget_shopping_cart .widget_shopping_cart_content a.button, .woocommerce table.cart td.actions .button.alt.checkout-button, .woocommerce #payment #place_order, .woocommerce-page #payment #place_order, .woocommerce table.cart td.actions .button.alt {
	background: <?php echo $pp_button_bg_color; ?> !important;
}
<?php
	}
	
?>

<?php
	$pp_button_font_color = get_option('pp_button_font_color');
	
	if(!empty($pp_button_font_color))
	{
?>
input[type=submit], input[type=button], a.button, .button { 
	color: <?php echo $pp_button_font_color; ?>;
}
input[type=submit]:hover, input[type=button]:hover, a.button:hover, .button:hover
{
	color: <?php echo $pp_button_font_color; ?> !important;
}
.widget_tag_cloud div a:hover, .meta-tags a:hover, #footer .widget_tag_cloud div a:hover, #footer .meta-tags a:hover, .tag_cloud a:hover, .woocommerce ul.products li.product a.add_to_cart_button, .woocommerce-page ul.products li.product a.add_to_cart_button, .woocommerce ul.products li.product a.add_to_cart_button.loading, .woocommerce-page ul.products li.product a.add_to_cart_button.loading, .woocommerce ul.products li.product a.add_to_cart_button:hover, .woocommerce-page ul.products li.product a.add_to_cart_button:hover, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit, .woocommerce #content input.button, .woocommerce-page a.button, .woocommerce-page button.button, .woocommerce-page input.button, .woocommerce-page #respond input#submit, .woocommerce-page #content input.button, .woocommerce .widget_shopping_cart .widget_shopping_cart_content a.button, .woocommerce table.cart td.actions .button.alt.checkout-button, .woocommerce #payment #place_order, .woocommerce-page #payment #place_order, .woocommerce table.cart td.actions .button.alt {
	color: <?php echo $pp_button_font_color; ?> !important;
}
<?php
	}
	
?>

<?php
	$pp_button_border_color = get_option('pp_button_border_color');
	
	if(!empty($pp_button_border_color))
	{
?>
input[type=submit], input[type=button], a.button, .button { 
	border: 1px solid <?php echo $pp_button_border_color; ?>;
}
.widget_tag_cloud div a:hover, .meta-tags a:hover, #footer .widget_tag_cloud div a:hover, #footer .meta-tags a:hover, .tag_cloud a:hover {
	border: 1px solid <?php echo $pp_button_border_color; ?> !important;
}
<?php
	}
	
?>

<?php

$pp_h1_font_color = get_option('pp_h1_font_color');
if(!empty($pp_h1_font_color))
{
?>
.post_header h2, h1, h2, h3, h4, h5, .portfolio_header h6, pre, code, tt
{
	color: <?php echo $pp_h1_font_color; ?>;
}
<?php
}
if(isset($_SESSION['pp_font_family']))
{
    $pp_font_family = $_SESSION['pp_font_family'];
}
else
{
    $pp_font_family = get_option('pp_font_family');
}

if(!empty($pp_font_family))
{
?>
h1, h2, h3, h4, h5, h6, #gallery_caption h2, #menu_wrapper .nav ul li a, #menu_wrapper div .nav li a, #copyright, .fancybox-title-outside-wrap { font-family: '<?php echo $pp_font_family; ?>' !important; }		
<?php
}

$pp_menu_lower = get_option('pp_menu_lower');

if(!empty($pp_menu_lower))
{
?>
.nav li a, #menu_close { text-transform: none; }		
<?php
}

$pp_menu_font_color = get_option('pp_menu_font_color');

if(!empty($pp_menu_font_color))
{
?>
#menu_wrapper .nav ul li a, #menu_wrapper div .nav li a { color: <?php echo $pp_menu_font_color; ?>; }
<?php
}

$pp_submenu_font_color = get_option('pp_submenu_font_color');

if(!empty($pp_submenu_font_color))
{
?>
#menu_wrapper div .nav li ul li a, #menu_wrapper div .nav li.current-menu-item ul li a, #menu_wrapper div .nav li ul li.current-menu-item a, #menu_wrapper .nav ul li ul li a, #menu_wrapper .nav ul li.current-menu-item ul li a, #menu_wrapper .nav ul li ul li.current-menu-item a, #menu_wrapper div .nav li.current-menu-parent ul li a, #menu_wrapper div .nav li ul li.current-menu-parent a { color: <?php echo $pp_submenu_font_color; ?>;  }
<?php
}

$pp_menu_hover_font_color = get_option('pp_menu_hover_font_color');

if(!empty($pp_menu_hover_font_color))
{
?>
#menu_wrapper .nav ul li a.hover, #menu_wrapper .nav ul li a:hover, #menu_wrapper div .nav li a.hover, #menu_wrapper div .nav li a:hover, #menu_wrapper .nav ul li ul li a:hover, #menu_wrapper .nav ul li ul li a:hover, #menu_wrapper div .nav li ul li a:hover, #menu_wrapper div .nav li ul li a:hover, #menu_wrapper div .nav li.current-menu-item ul li a:hover, #menu_wrapper div .nav li.current-menu-parent ul li a:hover { color: <?php echo $pp_menu_hover_font_color; ?>;  }
<?php
}

$pp_menu_active_font_color = get_option('pp_menu_active_font_color');

if(!empty($pp_menu_active_font_color))
{
?>
#menu_wrapper div .nav li.current-menu-item > a, #menu_wrapper div .nav li.current-menu-parent > a, #menu_wrapper div .nav li.current-menu-ancestor > a { color: <?php echo $pp_menu_active_font_color; ?>;  }
<?php
}

$pp_font_color = get_option('pp_font_color');

if(!empty($pp_font_color))
{
?>
body { color: <?php echo $pp_font_color; ?>; }
<?php
}
?>

<?php
	$pp_menu_bg_color = get_option('pp_menu_bg_color');

	if(!empty($pp_menu_bg_color))
	{
	
?>
.top_bar
{
	background: <!--?php echo $pp_menu_bg_color; ?-->white;
}
<?php
	}
?>

<?php
	$pp_submenu_bg_color = get_option('pp_submenu_bg_color');

	if(!empty($pp_submenu_bg_color))
	{
	
?>
#menu_wrapper .nav ul li ul, #menu_wrapper div .nav li ul
{
	background: <?php echo $pp_submenu_bg_color; ?>;
}
<?php
	}
?>

<?php
	$pp_submenu_border_color = get_option('pp_submenu_border_color');

	if(!empty($pp_submenu_border_color))
	{
	
?>
#menu_wrapper div .nav li ul li a, #menu_wrapper div .nav li.current-menu-item ul li a, #menu_wrapper div .nav li ul li.current-menu-item a,#menu_wrapper .nav ul li ul li a, #menu_wrapper .nav ul li.current-menu-item ul li a, #menu_wrapper .nav ul li ul li.current-menu-item a, #menu_wrapper div .nav li.current-menu-parent ul li a, #menu_wrapper div .nav li ul li.current-menu-parent a, #menu_wrapper .nav ul li ul li a, #menu_wrapper div .nav li ul li a, #menu_wrapper div .nav li.current-menu-parent ul li a, #menu_wrapper .nav ul li ul li a:hover, #menu_wrapper .nav ul li ul li a:hover, #menu_wrapper div .nav li ul li a:hover, #menu_wrapper div .nav li ul li a:hover, #menu_wrapper div .nav li.current-menu-item ul li a:hover, #menu_wrapper div .nav li.current-menu-parent ul li a:hover
{
	border-color: <?php echo $pp_submenu_border_color; ?>;
}
<?php
	}
?>

<?php
	$pp_submenu_bg_color = get_option('pp_submenu_bg_color');

	if(!empty($pp_submenu_bg_color))
	{
	
?>
#menu_wrapper .nav ul li ul, #menu_wrapper div .nav li ul
{
	background: <?php echo $pp_submenu_bg_color; ?>;
}
<?php
	}
?>

<?php
	$pp_submenu_hover_font_color = get_option('pp_submenu_hover_font_color');

	if(!empty($pp_submenu_hover_font_color))
	{
	
?>
#menu_wrapper .nav ul li ul li a:hover, #menu_wrapper .nav ul li ul li a:hover, #menu_wrapper div .nav li ul li a:hover, #menu_wrapper div .nav li ul li a:hover, #menu_wrapper div .nav li.current-menu-item ul li a:hover, #menu_wrapper div .nav li.current-menu-parent ul li a:hover
{
	color: <?php echo $pp_submenu_hover_font_color; ?>;
}
<?php
	}
?>

<?php
	$pp_portfolio_hover_bg_color = get_option('pp_portfolio_hover_bg_color');
	$pp_portfolio_hover_bg_color_rgb = HexToRGB($pp_portfolio_hover_bg_color);

	if(!empty($pp_portfolio_hover_bg_color))
	{
	
?>
.one_half.gallery2 .mask, .one_third.gallery3 .mask, .one_fourth.gallery4 .mask, .wall_thumbnail .mask, .mansory_thumbnail .mask
{
	background: <?php echo $pp_portfolio_hover_bg_color; ?>;
	background-color: rgba(<?php echo $pp_portfolio_hover_bg_color_rgb['r']; ?>,<?php echo $pp_portfolio_hover_bg_color_rgb['g']; ?>,<?php echo $pp_portfolio_hover_bg_color_rgb['b']; ?>, 0.7);
}
<?php
	}
?>

<?php
	$pp_portfolio_hover_font_color = get_option('pp_portfolio_hover_font_color');

	if(!empty($pp_portfolio_hover_font_color))
	{
	
?>
.one_half.gallery2 h4, .one_half.gallery2 span.caption, .one_third.gallery3 h5, .one_third.gallery3 span.caption, .one_fourth.gallery4 h6, .one_fourth.gallery4 span.caption, .wall_thumbnail h6, .wall_thumbnail span.caption, .mansory_thumbnail h6, .mansory_thumbnail span.caption
{
	color: <?php echo $pp_portfolio_hover_font_color; ?>;
}
<?php
	}
?>