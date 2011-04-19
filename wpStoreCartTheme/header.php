<?PHP global $wpscThemeOptions, $wpStoreCart, $wpsc, $cart; 
$_ajax = admin_url('admin-ajax.php'); // Include ajax
require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
if(!isset($_SESSION)) {
        @session_start();

}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php 
	if ( is_single() ) { single_post_title(); }
	elseif ( is_home() || is_front_page() ) { bloginfo('name'); print ' | '; bloginfo('description'); }
	elseif ( is_page() ) { single_post_title(''); }
	elseif ( is_search() ) { bloginfo('name'); print ' | Search results for ' . wp_specialchars($s); }
	elseif ( is_404() ) { bloginfo('name'); print ' | Not Found'; }
	else { bloginfo('name'); wp_title('|'); }
?></title>
 

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>

<script type="text/javascript">
	Cufon.replace('h6'); 	
	jQuery(document).ready(function($) { // 
		$('#smallcart').html('<img src="<?php bloginfo('template_directory'); ?>/img/loading.gif" width="80" height="12" />');
		var data = {
			action: 'update_small_cart'
		};
		
		$.post('<?php echo($_ajax); ?>', data, function(result) {
			$('#smallcart').html(result);
		});
		
		<?PHP if($wpscThemeOptions['use_product_preview']=='true') { ?>$(".tooltip-target").ezpz_tooltip(); <?PHP ;} ?>
		
	});
</script>


<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="<?php printf( __( '%s latest posts', 'your-theme' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'your-theme' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

</head>
<body>

<div class="wrapper"><!-- Wrapper Start -->

	<div class="header"><h6><?PHP bloginfo('name'); ?></h6></div>
    
    <div id="top_panel"><!-- Top panel Start -->
    
    	<div class="top"><!-- Top Start -->
        
        	<div id="nav-menu"><!-- Menu Start -->
				<ul>
					<?PHP if($wpscThemeOptions['display_home_link']=="true"){?><li><a href="<?PHP echo get_bloginfo('home'); ?>" title="Home Page">Home</a></li><?PHP } ?>
					<?php wp_list_pages('title_li=&sort_column=menu_order&depth=1'); ?>
				</ul>
			</div> <!-- Menu End -->
            
 
        
        </div><!-- Top End -->
    
   	  <div class="bottom"><!-- Bottom Start -->
        
       	  <div class="cart"><!-- Cart Start -->
			
            	<img src="<?php bloginfo('template_directory'); ?>/img/cart.png" align="absmiddle"/>
                <span id="smallcart"><?PHP if(isset($wpStoreCart)) { $devOptions = $wpStoreCart->getAdminOptions(); echo $wpscThemeOptions['text_you_have'];?> <b><?PHP echo $cart->itemcount; ?></b> <?PHP echo $wpscThemeOptions['text_items_with_total_value_of'];?> <b><?PHP echo $devOptions['currency_symbol'] .number_format($cart->total, 2) .$devOptions['currency_symbol_right']; ?></b> <?PHP } ?></span>
                <a style="padding-left:9px;" href="<?PHP if(isset($wpStoreCart)) {echo get_permalink( $devOptions['checkoutpage'] );}?>"><?PHP echo $wpscThemeOptions['text_view_cart'];?> <img src="<?php bloginfo('template_directory'); ?>/img/blue_arrow.png" style="padding-bottom:1px;"></a> 
                <a style="padding-left:9px;" href="<?PHP if(isset($wpStoreCart)) {echo get_permalink( $devOptions['checkoutpage'] );}?>"><?PHP echo $wpscThemeOptions['text_check_out'];?> <img src="<?php bloginfo('template_directory'); ?>/img/blue_arrow.png" style="padding-bottom:1px;"></a>
                
          </div><!-- Cart End -->
          
          <div class="login"><!-- Login Start -->
		  <img src="<?php bloginfo('template_directory'); ?>/img/user.png" style="float:left; padding-right:9px; padding-top:4px;" align="absmiddle" />
          <?PHP if ( !is_user_logged_in() ) { ?>
          		                
               	<form name="wploginform" action="<?php echo get_option('home'); ?>/wp-login.php" method="post" style="width:315px; float:left;">
					<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
					<input style="float:right;" type="text" name="pwd"  id="loginpassword" value="<?PHP echo $wpscThemeOptions['text_password']; ?>" onfocus="if(this.value == '<?PHP echo $wpscThemeOptions['text_password']; ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?PHP echo $wpscThemeOptions['text_password']; ?>';}" />
					<input style="float:right;" type="text" name="log" id="loginusername" value="<?PHP echo $wpscThemeOptions['text_username']; ?>" onfocus="if(this.value == '<?PHP echo $wpscThemeOptions['text_username']; ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?PHP echo $wpscThemeOptions['text_username']; ?>';}"/>
				</form> 
                
                <a href="<?php echo wp_login_url(); ?>?action=register"><img src="<?php bloginfo('template_directory'); ?>/img/register.jpg" style="float:right;" /></a>
                <img src="<?php bloginfo('template_directory'); ?>/img/login.jpg"  style="float:right; padding-right:3px; cursor:pointer;" onclick="document.wploginform.submit()" />
		  <?PHP } else { 
					global $userdata, $wpStoreCart;
					get_currentuserinfo();		  
					$username = $userdata->user_nicename;
					

					if(isset($wpStoreCart)) {
						$devOptions = $wpStoreCart->getAdminOptions();

						if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
							$permalink = get_permalink($devOptions['mainpage']) .'?wpsc=orders';
						} else {
							$permalink = get_permalink($devOptions['mainpage']) .'&wpsc=orders';
						}

					} else {
						$permalink = '#';
					}
					
					
					echo '<span style="position:relative;top:4px;">'.$wpscThemeOptions['text_welcome_back'].' '.$username; if($permalink != '#'){echo ' <a href="'. $permalink .'" style="padding-left:9px;">'.$wpscThemeOptions['text_my_orders'].' <img src="'; echo bloginfo('template_directory'); echo '/img/blue_arrow.png" style="padding-bottom:1px;"></a>';} echo ' <a href="'. wp_logout_url() .'" style="padding-left:9px;">'.$wpscThemeOptions['text_logout'].' <img src="'; echo bloginfo('template_directory'); echo '/img/blue_arrow.png" style="padding-bottom:1px;"></a></span>';
		  ?>
		  
		  <?PHP } ?>
          </div>
          <!-- Login End -->
        
      </div><!-- Bottom End -->
    
    </div><!-- Top panel End -->
	
	<div id="content"><!-- Content Start -->