<?php
/*
Plugin Name: wpStoreCart User Customized Products
Plugin URI: http://wpstorecart.com/
Description: This plugin lets you create & sell products that your customers can customize with their own images & text.  Great for screen printing shops, custom mugs, posters, shirts, hats, postcards, and more.
Version: 5.0.0
Author: Jeff Quindlen
Author URI: http://indiedevbundle.com/wordpress/ecommerce/
*/

/**
 * Installs database schema
 * @global type $wpdb 
 */
function wpscCustomizeProductInstallWpms() {
    global $wpdb;
    $table_name = $wpdb->prefix . "wpstorecart_custom_def";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "
        CREATE TABLE `{$table_name}` (
            `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            `productkey` INT NOT NULL, `custkey` INT NOT NULL, 
            `allowedcustomizations` VARCHAR(4) NOT NULL, 
            `x` INT NOT NULL, 
            `y` INT NOT NULL, 
            `width` INT NOT NULL, 
            `height` INT NOT NULL                                         
        );
        ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    $table_name = $wpdb->prefix . "wpstorecart_custom_orders";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "    
        CREATE TABLE `{$table_name}` (
            `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `productkey` INT NOT NULL ,
            `userid` INT NOT NULL ,
            `orderid` INT NOT NULL ,
            `custdefkey` INT NOT NULL ,
            `ipaddress` VARCHAR( 39 ) NOT NULL ,
            `guestemail` VARCHAR( 255 ) NOT NULL ,
            `customtext` TEXT NOT NULL ,
            `textformat` TEXT NOT NULL ,
            `textx` INT NOT NULL ,
            `texty` INT NOT NULL ,
            `textwidth` INT NOT NULL ,
            `textheight` INT NOT NULL ,
            `image` VARCHAR( 255 ) NOT NULL ,
            `imagex` INT NOT NULL ,
            `imagey` INT NOT NULL ,
            `imagewidth` INT NOT NULL ,
            `imageheight` INT NOT NULL
        );    
        ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);  
    }
    
    
}










/**
 * Starts install process
 * @global type $wpdb
 * @return type 
 */
function wpscCustomizeProductInstall() {
    global $wpdb;

    if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
                    $old_blog = $wpdb->blogid;
                    // Get all blog ids
                    $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
                    foreach ($blogids as $blog_id) {
                            switch_to_blog($blog_id);
                            wpscCustomizeProductInstallWpms();
                    }
                    switch_to_blog($old_blog);
                    return;
            }
    }
    wpscCustomizeProductInstallWpms();    
}











// Load the definitions
function wpscCustomizeProductLoadDefinitions($primkey) {
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_custom_def` WHERE `productkey`='{$primkey}';", ARRAY_A);
    if(@isset($results[0]['primkey'])) {
        return $results;
    } else {
        return NULL;
    }
}






// HTML table output of custom definitions for the selected product
function wpscCustomizeProductDisplayTableDefinitions($primkey) {
    $output = null;
    $results = wpscCustomizeProductLoadDefinitions($primkey);
    if(@isset($results[0]['primkey'])) {
        foreach ($results as $result) {
            $output .= '<tr id="wpsc-customize-edit-record-'.$result['custkey'].'"><td><input type="hidden" id="wpsc-customize-edit-record-val-'.$result['custkey'].'" name="wpsc-customize-edit-record-val-'.$result['custkey'].'" value="'.$result['custkey'].'" readonly="true" style="width:40px;" /><input type="text" id="wpsc-customize-edit-record-primkey-val-'.$result['custkey'].'" name="wpsc-customize-edit-record-primkey-val-'.$result['custkey'].'" value="'.$result['primkey'].'" readonly="true" style="width:40px;" /><a href="#" onclick="wpscCustomizeDelete('.$result['custkey'].');return false;"><img src="'.plugins_url().'/wpstorecart/images/delete.png" alt="delete"></a></td><td>';
            $output .= '<select id="wpsc-customize-edit-record-allowed-types-val-'.$result['custkey'].'" name="wpsc-customize-edit-record-allowed-types-val-'.$result['custkey'].'" onchange="wpscCustomizeSaveDone('.$result['custkey'].', '.$result['primkey'].');">
                    <option value="pics"'; if($result['allowedcustomizations']=='pics'){$output.=' selected="selected" ';}; $output .='>Pictures</option>
                    <option value="text"'; if($result['allowedcustomizations']=='text'){$output.=' selected="selected" ';}; $output .='>Text</option>
                    <!--<option value="both"'; if($result['allowedcustomizations']=='both'){$output.=' selected="selected" ';}; $output .='>Pictures &amp; Text</option>-->
                </select>
                </td>';
            $output .= '<td id="wpsc-customize-edit-record-x-'.$result['custkey'].'"><input type="text" id="wpsc-customize-edit-record-x-val-'.$result['custkey'].'" name="wpsc-customize-edit-record-x-val-'.$result['custkey'].'" value="'.$result['x'].'" readonly="true" style="width:60px;" /></td><td id="wpsc-customize-edit-record-y-'.$result['custkey'].'"><input type="text" id="wpsc-customize-edit-record-y-val-'.$result['custkey'].'" name="wpsc-customize-edit-record-y-val-'.$result['custkey'].'" value="'.$result['y'].'" readonly="true" style="width:60px;" /></td><td id="wpsc-customize-edit-record-width-'.$result['custkey'].'"><input type="text" id="wpsc-customize-edit-record-width-val-'.$result['custkey'].'" name="wpsc-customize-edit-record-width-val-'.$result['custkey'].'" value="'.$result['width'].'" readonly="true" style="width:60px;" /></td><td id="wpsc-customize-edit-record-height-'.$result['custkey'].'"><input type="text" id="wpsc-customize-edit-record-height-val-'.$result['custkey'].'" name="wpsc-customize-edit-record-height-val-'.$result['custkey'].'" value="'.$result['height'].'" readonly="true" style="width:60px;" /></td></tr>';
        }
    }
    return $output;
}







// The Javascript definitions of shapes
function wpscCustomizeProductDisplayJSDefinitions($primkey, $is_admin=true) {
    $output = null;
    $results = wpscCustomizeProductLoadDefinitions($primkey);
    if(@isset($results[0]['primkey'])) {
        foreach ($results as $result) {
            $output .= '
                wpsc_rect['.$result['custkey'].'] = wpsc_paper.rect('.$result['x'].','.$result['y'].','.$result['width'].','.$result['height'].').attr({"fill":"white","fill-opacity":"0","stroke":"red"});
                wpsc_rect['.$result['custkey'].'].data("wpsc_primkey", '.$result['primkey'].')
                wpsc_rect['.$result['custkey'].'].data("wpsc_id", '.$result['custkey'].');
                wpsc_rect['.$result['custkey'].'].toFront();                    
';
            
            if($is_admin) {
            $output .= '
                wpsc_rect['.$result['custkey'].'].mousemove(wpscCustChangeCursor);
                wpsc_rect['.$result['custkey'].'].drag(wpscCustDragMove, function(){this.ox = this.attr("x");this.oy = this.attr("y");this.ow = this.attr("width");this.oh = this.attr("height");this.dragging = true;}, wpscMouseMoveDone);
                ;  
                ';
            }
        }
    }
    return $output;
}







// The number of custom definitions for the specified product
function wpscCustomizeProductCountDefinitions($primkey) {
    global $wpdb;
    $return_value = 0;
    $results = $wpdb->get_results("SELECT `custkey` FROM `{$wpdb->prefix}wpstorecart_custom_def` WHERE `productkey`='{$primkey}' ORDER BY `custkey` DESC LIMIT 0 , 1;", ARRAY_A);
    if(@isset($results[0]['custkey'])) {
        $return_value = intval($results[0]['custkey']) + 1;
    }    
    
    return $return_value;
}








/**
 * Admin panel additions
 */
function wpscCustomizeProductAdminPanel() {
    ?>
    <script type="text/javascript">
    /* <![CDATA[ */        
        
    var wpsc_rect = [];

    function wpscCustomizeDelete(id) {
        if(confirm('Are you sure you want to remove this record from the database?')) {
            var wpsc_primkey = wpsc_rect[id].data("wpsc_primkey");
            jQuery.post('<?php echo plugins_url();?>/wpstorecart/plugins/wpsc-user-customize-products/del_definition.php', {wpsc_primkey:wpsc_primkey}, function() {
                wpsc_rect[id].removeData();
                wpsc_rect[id].remove(); // Remove the drawing
                jQuery('#wpsc-customize-edit-record-'+id).remove(); // Remove the table entry
                jQuery('#wpsc-customize-saved-notify').fadeIn(200).delay(400).fadeOut(200);
            });              
        }
        return false;
    }
    
    function wpscCustomizeSaveDone(wpsc_cust_current_id, wpsc_primkey) {
        jQuery.post('<?php echo plugins_url();?>/wpstorecart/plugins/wpsc-user-customize-products/update_definition.php', {wpsc_cust_current_id: wpsc_cust_current_id, wpsc_primkey:wpsc_primkey, wpsc_cust_updated_types: jQuery('#wpsc-customize-edit-record-allowed-types-val-'+wpsc_cust_current_id).val(), wpsc_cust_updated_x:jQuery('#wpsc-customize-edit-record-x-val-'+wpsc_cust_current_id).val(), wpsc_cust_updated_y:jQuery('#wpsc-customize-edit-record-y-val-'+wpsc_cust_current_id).val(), wpsc_cust_updated_width:jQuery('#wpsc-customize-edit-record-width-val-'+wpsc_cust_current_id).val(), wpsc_cust_updated_height:jQuery('#wpsc-customize-edit-record-height-val-'+wpsc_cust_current_id).val() }, function() {
            jQuery('#wpsc-customize-saved-notify').fadeIn(200).delay(400).fadeOut(200);
        });     
    }

    jQuery(document).ready( function () {
        var wpsccustomizeimg = new Image();
        wpsccustomizeimg.src = jQuery('#wpStoreCartproduct_thumbnail').val();    
        wpsccustomizeimg.onmousedown="return false;";
        wpsccustomizeimg.onload = function() {

            var wpsc_rect_count = parseInt(1 + parseInt(jQuery("#wpsc-rect-count").val()));

            function wpscCustDragMove(dx, dy) {

                var wpsc_cust_current_id = this.data("wpsc_id");

                if(this.attr('cursor')=='nw-resize') {
                    this.attr({ x: this.ox + dx, y: this.oy + dy, width: this.ow - dx, height: this.oh - dy });                
                } else if(this.attr('cursor')=='ne-resize') {
                    this.attr({ y: this.oy + dy , width: this.ow + dx, height: this.oh - dy });              
                } else if(this.attr('cursor')=='se-resize') {
                    this.attr({ width: this.ow + dx, height: this.oh + dy });             
                } else if(this.attr('cursor')=='sw-resize') {
                    this.attr({ x: this.ox + dx, width: this.ow - dx, height: this.oh + dy });           
                } else {
                    this.attr({ x: this.ox + dx, y: this.oy + dy });                
                }

                jQuery('#wpsc-customize-edit-record-x-val-'+wpsc_cust_current_id).val(Math.round(this.attr('x')));
                jQuery('#wpsc-customize-edit-record-y-val-'+wpsc_cust_current_id).val(Math.round(this.attr('y')));
                jQuery('#wpsc-customize-edit-record-width-val-'+wpsc_cust_current_id).val(this.attr('width'));
                jQuery('#wpsc-customize-edit-record-height-val-'+wpsc_cust_current_id).val(this.attr('height'));

            }


            function wpscCustChangeCursor(e, mouseX, mouseY) {

                if (this.dragging === true) {
                    return;
                }

                var relX = mouseX - jQuery("#wpsc-raph-workspace").offset().left - this.attr('x');
                var relY = mouseY - jQuery("#wpsc-raph-workspace").offset().top - this.attr('y');

                if (relX < 12 && relY < 12) { 
                    this.attr('cursor', 'nw-resize');
                } else if (relX > this.attr('width') - 12 && relY < 12) { 
                    this.attr('cursor', 'ne-resize');
                } else if (relX > this.attr('width') - 12 && relY > this.attr('height') - 12) { 
                    this.attr('cursor', 'se-resize');
                } else if (relX < 12 && relY > this.attr('height') - 12) { 
                    this.attr('cursor', 'sw-resize');
                } else { 
                    this.attr('cursor', 'move');
                }

            }

            //global variables
            var mouseDownX = 0;
            var mouseDownY = 0;

            jQuery(document).bind("dragstart", function(e) {
                if (e.target.nodeName.toUpperCase() == "IMG") {
                    return false;
                }
            });

            jQuery('img').bind('dragstart', function(event) { event.preventDefault(); });

            var wpsc_paper = Raphael('wpsc-raph-workspace', this.width, this.height);

            <?php echo wpscCustomizeProductDisplayJSDefinitions($_GET['keytoedit']); ?>

            function wpscCustomizeCreateNewRecord(id) {
                jQuery('<tr id="wpsc-customize-edit-record-'+id+'"><td><input type="hidden" id="wpsc-customize-edit-record-val-'+id+'" name="wpsc-customize-edit-record-val-'+id+'" value="'+id+'" readonly="true" style="width:40px;" /><input type="text" id="wpsc-customize-edit-record-primkey-val-'+id+'" name="wpsc-customize-edit-record-primkey-val-'+id+'" value="0" readonly="true" style="width:40px;" /><a href="#" onclick="wpscCustomizeDelete('+id+');return false;"><img src="<?php echo plugins_url();?>/wpstorecart/images/delete.png" alt="delete"></a></td><td><select id="wpsc-customize-edit-record-allowed-types-val-'+id+'" name="wpsc-customize-edit-record-allowed-types-val-'+id+'" onchange="wpscCustomizeSaveDone( '+id+', jQuery(\'#wpsc-customize-edit-record-primkey-val-'+id+'\').val() );"><option value="pics">Pictures</option><option value="text">Text</option><!--<option value="both">Pictures &amp; Text</option>--></select></td><td id="wpsc-customize-edit-record-x-'+id+'"><input type="text" id="wpsc-customize-edit-record-x-val-'+id+'" name="wpsc-customize-edit-record-x-val-'+id+'" value="'+Math.round(wpsc_rect[wpsc_rect_count].attr('x'))+'" readonly="true" style="width:60px;" /></td><td id="wpsc-customize-edit-record-y-'+id+'"><input type="text" id="wpsc-customize-edit-record-y-val-'+id+'" name="wpsc-customize-edit-record-y-val-'+id+'" value="'+Math.round(wpsc_rect[wpsc_rect_count].attr('y'))+'" readonly="true" style="width:60px;" /></td><td id="wpsc-customize-edit-record-width-'+id+'"><input type="text" id="wpsc-customize-edit-record-width-val-'+id+'" name="wpsc-customize-edit-record-width-val-'+id+'" value="'+wpsc_rect[wpsc_rect_count].attr('width')+'" readonly="true" style="width:60px;" /></td><td id="wpsc-customize-edit-record-height-'+id+'"><input type="text" id="wpsc-customize-edit-record-height-val-'+id+'" name="wpsc-customize-edit-record-height-val-'+id+'" value="'+wpsc_rect[wpsc_rect_count].attr('height')+'" readonly="true" style="width:60px;" /></td></tr>').appendTo('#wpsc-customize-edit');
            }

            function wpscMouseMoveDone() {
                this.dragging = false;  
                var wpsc_cust_current_id = this.data("wpsc_id");
                var wpsc_primkey = this.data("wpsc_primkey");

                jQuery.post('<?php echo plugins_url();?>/wpstorecart/plugins/wpsc-user-customize-products/update_definition.php', {wpsc_cust_current_id: wpsc_cust_current_id, wpsc_primkey:wpsc_primkey, wpsc_cust_updated_types: jQuery('#wpsc-customize-edit-record-allowed-types-val-'+wpsc_cust_current_id).val(), wpsc_cust_updated_x:jQuery('#wpsc-customize-edit-record-x-val-'+wpsc_cust_current_id).val(), wpsc_cust_updated_y:jQuery('#wpsc-customize-edit-record-y-val-'+wpsc_cust_current_id).val(), wpsc_cust_updated_width:jQuery('#wpsc-customize-edit-record-width-val-'+wpsc_cust_current_id).val(), wpsc_cust_updated_height:jQuery('#wpsc-customize-edit-record-height-val-'+wpsc_cust_current_id).val() }, function() {
                    jQuery('#wpsc-customize-saved-notify').fadeIn(200).delay(400).fadeOut(200);
                });                
            }

            function wpscCustomizeCreateRect(x,y,width,height) {
                jQuery("#wpsc-rect-count").val(wpsc_rect_count);             
                wpsc_rect[wpsc_rect_count] = wpsc_paper.rect(x,y,width,height).attr({"fill":"white","fill-opacity":"0","stroke":"red"});
                wpscCustomizeCreateNewRecord(wpsc_rect_count);            
                wpsc_rect[wpsc_rect_count].data("wpsc_id", wpsc_rect_count);
                wpsc_rect[wpsc_rect_count].toFront();
                wpsc_rect[wpsc_rect_count].mousemove(wpscCustChangeCursor);
                wpsc_rect[wpsc_rect_count].drag(wpscCustDragMove, function(){this.ox = this.attr('x');this.oy = this.attr('y');this.ow = this.attr('width');this.oh = this.attr('height');this.dragging = true;}, wpscMouseMoveDone);
                wpscCustomizeDrawRegionToggleOff();

                // wpsc_user_customize_vars.plugins_url
                jQuery.post('<?php echo plugins_url();?>/wpstorecart/plugins/wpsc-user-customize-products/new_definition.php', jQuery("#wpstorecartaddproductform").serialize(), function(data) {
                    jQuery('#wpsc-customize-edit-record-primkey-val-'+wpsc_rect_count).val(data);
                    wpsc_rect[wpsc_rect_count].data("wpsc_primkey", data);                
                    wpsc_rect_count++;
                    jQuery('#wpsc-customize-saved-notify').fadeIn(200).delay(400).fadeOut(200);
                });  


            }

            function OnMouseDown(e){
                var offset = jQuery("#wpsc-raph-workspace").offset();
                mouseDownX = e.pageX - offset.left;
                mouseDownY = e.pageY - offset.top;
            }

            function OnMouseUp(e){
                var offset = jQuery("#wpsc-raph-workspace").offset();
                var upX = e.pageX - offset.left;
                var upY = e.pageY - offset.top;

                var width = upX - mouseDownX;
                var height = upY - mouseDownY;

                wpscCustomizeCreateRect(mouseDownX, mouseDownY, width, height);
            }

            var backimage = wpsc_paper.rect(0,0,this.width, this.height).attr({
                fill: "url("+jQuery('#wpStoreCartproduct_thumbnail').val()+")"
            });

            backimage.toBack();

            function wpscCustomizeDrawRegionToggleOn() {
                //register events on document load
                backimage.mousedown(OnMouseDown);
                backimage.mouseup(OnMouseUp);
                jQuery('#wpsc-customize-instructions').show();
                jQuery('#wpsc-customize-new-area').hide();   
                jQuery("#wpsc-raph-workspace").mouseover(function() {
                   jQuery('#wpsc-raph-workspace').css('cursor','crosshair'); 
                });
                return false;
            }

            function wpscCustomizeDrawRegionToggleOff() {
                //deregister events
                backimage.unmousedown(OnMouseDown);
                backimage.unmouseup(OnMouseUp);
                jQuery('#wpsc-customize-new-area').show();
                jQuery('#wpsc-customize-instructions').hide();   
                jQuery("#wpsc-raph-workspace").unbind('mouseover mouseout');
                jQuery('#wpsc-raph-workspace').css('cursor','auto'); 
                return false;
            }

            jQuery("#wpsc-customize-new-area").click(function() {
                wpscCustomizeDrawRegionToggleOn();
                return false;
            });

            jQuery("#wpsc-customize-instructions-cancel").click(function() {
                wpscCustomizeDrawRegionToggleOff();
                return false;
            });

        }

    });
    
    /* ]]> */
    </script>
    <?php
    
    $wpsc_cust_def_count = wpscCustomizeProductCountDefinitions($_GET['keytoedit']);
    if($wpsc_cust_def_count > 0) {
        $toggle_complete = '';
        $toggle_complete_display = '';
    } else {
        $toggle_complete = '<a class="button-secondary" href="#" onclick="jQuery(\'#wpsc-customize-workspace\').show();jQuery(this).hide();return false;" id="wpsc-customize-enable-workspace">Enable Product Customization</a><br />';
        $toggle_complete_display = 'display:none;';
    }
    
    echo '
    <tr>
        <td><p>User customization: <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-95555556" /><div class="tooltip-content" id="example-content-95555556">Toggle and setup the ability for users to customize products.</div></p>
            <p><strong><em>NOTE: Changes to<br />customizations are<br />saved in real<br />time using AJAX</em></strong></p>
            <p id="wpsc-customize-saved-notify" style="color:green;display:none;">Changes saved...</p></td>
        <td>
            '.$toggle_complete.'
            <div id="wpsc-customize-workspace" style="'.$toggle_complete_display.'">
                <div id="wpsc-customize-toolbar">
                    <a href="#" class="button-secondary" id="wpsc-customize-new-area">Define a New Customizable Area</a><br />
                    <div id="wpsc-customize-instructions" style="display:none;">Click and hold the left mouse button, and then drag and release the left mouse button when you\'re done.  To cancel, press this button: <a href="#" class="button-secondary" id="wpsc-customize-instructions-cancel">Cancel</a></div>
                </div>
                <div id="wpsc-raph-workspace" style="width: 100%; height: 100%;"></div><br />
                <table class="widefat wpsc5table">
                    <thead>
                        <tr><th>ID</th><th>Allowed Customizations</th><th>X</th><th>Y</th><th>Width</th><th>Height</th></tr>
                    </thead>
                    <tbody id="wpsc-customize-edit">
                        '.wpscCustomizeProductDisplayTableDefinitions($_GET['keytoedit']).'
                    </tbody>
                </table>
                <br />
                <input type="hidden" value="'.$wpsc_cust_def_count.'" id="wpsc-rect-count" name="wpsc-rect-count" />
            </div>
        </td>
    </tr>';    
}

add_action('wpsc_admin_edit_product_table_after_product_thumbnail', 'wpscCustomizeProductAdminPanel');










// Admin scripts and styles are loaded
function wpscCustomizeProductAdminEnqueue($hook) {
    if( (is_admin() ) && (isset($_GET['page'])) && ($_GET['page'] == "wpstorecart-new-product") ) {
        wp_enqueue_script( 'wpsc-raphael', plugins_url('/js/raphael.js', __FILE__) );
    } else {
        return;
    }
}

add_action( 'admin_enqueue_scripts', 'wpscCustomizeProductAdminEnqueue' );










// Attaches to the checkout process, and associates current customized product with the order
function wpscCustomizeProductAssociateWithOrder() {
    global $wpdb, $wpscPaymentGateway;
    $order_id = $wpscPaymentGateway['order_id'];
    if(@!isset($_SESSION)) {
            @session_start();
    }    
    
    @$exploded = explode(',', $_SESSION['wpsc_custom_keys']);
    foreach ($exploded as $explode) {
        $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_custom_orders` SET `orderid`='{$wpscPaymentGateway['order_id']}' WHERE `primkey`='".intval($explode)."';");
    }
    $_SESSION['wpsc_custom_keys'] = null;
    
}
add_action( 'wpsc_process_payment_gateways', 'wpscCustomizeProductAssociateWithOrder' );











// Frontend display records
function wpscCustomizeProductDisplayFrontendDefinitions($primkey) {
    global $wpsc_wordpress_upload_dir;
    $output = null;
    $results = wpscCustomizeProductLoadDefinitions($primkey);
    $count = 1;
    
    if(@isset($results[0]['primkey'])) {
        foreach ($results as $result) {
            $output .= '<tr id="wpsc-customize-frontend-record-'.$result['custkey'].'"><td>
                        <script type="text/javascript">
                        /* <![CDATA[ */
                            jQuery(document).ready(function(){

                                jQuery("#wpsc-customize-toolbar-form-'.$result['custkey'].'").ajaxForm(function(r) { 
                                    
                                    jQuery("#wpsc-custom-delete-'.$result['custkey'].'").show();

                                    if(jQuery("#wpsc-custom-selected-image-'.$result['custkey'].'").val() != "") {
                                        var keytoremove = jQuery("#wpsc-custom-selected-image-count-'.$result['custkey'].'").val();
                                        wpscCustomDeleteImage(keytoremove);    
                                    }

                                    jQuery("#wpsc-custom-selected-image-'.$result['custkey'].'").val(r);
                                    jQuery("#wpsc-custom-selected-image-count-'.$result['custkey'].'").val('.$count.');

                                    var wpsccustomizeclientimg = new Image();
                                    wpsccustomizeclientimg.src = "'.$wpsc_wordpress_upload_dir['baseurl'].'/wpstorecart/"+r;    
                                    wpsccustomizeclientimg.onmousedown="return false;";
                                    wpsccustomizeclientimg.onload = function() {

                                        function wpscCustChangeCursor(e, mouseX, mouseY) {

                                            if (this.dragging === true) {
                                                return;
                                            }

                                            var relX = mouseX - jQuery("#wpsc-raph-workspace").offset().left - this.attr("x");
                                            var relY = mouseY - jQuery("#wpsc-raph-workspace").offset().top - this.attr("y");

                                            if (relX < 12 && relY < 12) { 
                                                this.attr("cursor", "nw-resize");
                                            } else if (relX > this.attr("width") - 12 && relY < 12) { 
                                                this.attr("cursor", "ne-resize");
                                            } else if (relX > this.attr("width") - 12 && relY > this.attr("height") - 12) { 
                                                this.attr("cursor", "se-resize");
                                            } else if (relX < 12 && relY > this.attr("height") - 12) { 
                                                this.attr("cursor", "sw-resize");
                                            } else { 
                                                this.attr("cursor", "move");
                                            }
                                        }

                                        var thisOriginalWidth = this.width;
                                        var thisOriginalHeight = this.height;
                                        var thisCurrentWidth = this.width;
                                        var thisCurrentHeight = this.height;
                                        var thisCurrentRatio = 1;
                                        var thisCurrentHRatio = 1;
                                        if (wpsc_rect['.$result['custkey'].'].attr("width") <  this.width) {
                                            thisCurrentRatio = wpsc_rect['.$result['custkey'].'].attr("width") / this.width;
                                            thisCurrentWidth = parseInt(wpsc_rect['.$result['custkey'].'].attr("width"));
                                            thisCurrentHeight = this.height * thisCurrentRatio;                                                
                                        }
                                        if (wpsc_rect['.$result['custkey'].'].attr("height") <  thisCurrentHeight) {
                                            thisCurrentHeight = parseInt(wpsc_rect['.$result['custkey'].'].attr("height"));
                                        }                                        

                                        wpsc_newimage['.$count.'] = wpsc_paper.image("'.$wpsc_wordpress_upload_dir['baseurl'].'/wpstorecart/"+r, wpsc_rect['.$result['custkey'].'].attr("x"), wpsc_rect['.$result['custkey'].'].attr("y"),thisCurrentWidth, thisCurrentHeight);
                                        jQuery("#wpsc-custom-selected-image-x-'.$result['custkey'].'").val(wpsc_rect['.$result['custkey'].'].attr("x"));
                                        jQuery("#wpsc-custom-selected-image-y-'.$result['custkey'].'").val(wpsc_rect['.$result['custkey'].'].attr("y"));
                                        jQuery("#wpsc-custom-selected-image-width-'.$result['custkey'].'").val(thisCurrentWidth);
                                        jQuery("#wpsc-custom-selected-image-height-'.$result['custkey'].'").val(thisCurrentHeight);
                                        
                                        wpsc_newimage['.$count.'].toFront();   
                                        wpsc_newimage['.$count.'].mousemove(wpscCustChangeCursor);
                                        
                                        var wpscftx = wpsc_rect['.$result['custkey'].'].attr("x");
                                        var wpscfty = wpsc_rect['.$result['custkey'].'].attr("y");
                                        var wpscftwidth = wpsc_rect['.$result['custkey'].'].attr("width");
                                        var wpscftheight = wpsc_rect['.$result['custkey'].'].attr("height");
                                        var wpscftx_end = wpscftx + wpscftwidth;
                                        var wpscfty_end = wpscfty + wpscftheight;
                                        
                                        function wpsc_xstart(dx, dy) {
                                            
                                                // storing original coordinates
                                                this.ox = this.attr("x");
                                                this.oy = this.attr("y");
                                                this.ow = this.attr("width");
                                                this.oh = this.attr("height");
                                                this.dragging = true;

                                        };
                                        
                                        var wpsc_xmove = function(dx, dy) {

                                            if(this.attr("cursor")=="nw-resize") {
                                                if((this.ow - dx) > wpscftwidth || (this.oh - dy) > wpscftheight || this.attr("y") > wpscfty_end || this.attr("x") > wpscftx_end || (this.ox + dx) < wpscftx || (this.oy + dy) < wpscfty ) {
                                                    //this.attr({x: this.ox, y: this.oy, width: this.ow, height: this.oh}); 
                                                } else {
                                                    this.attr({ x: this.ox + dx, y: this.oy + dy, width: this.ow - dx, height: this.oh - dy });
                                                }
                                            } else if(this.attr("cursor")=="ne-resize") {
                                                if(( (this.ow + dx) - (wpscftx - this.ox)) > wpscftwidth || (this.oh - dy) > wpscftheight || this.attr("y") > wpscfty_end || this.attr("x") > wpscftx_end || (this.oy + dy) < wpscfty) {
                                                    //this.attr({x: this.ox, y: this.oy, width: this.ow, height: this.oh}); 
                                                } else {                                                    
                                                    this.attr({ y: this.oy + dy , width: this.ow + dx, height: this.oh - dy });  
                                                }
                                            } else if(this.attr("cursor")=="se-resize") {
                                                if((this.ow + dx  - (wpscftx - this.ox)) > wpscftwidth || (this.oh + dy - (wpscfty - this.oy)) > wpscftheight) {
                                                    //this.attr({x: this.ox, y: this.oy, width: this.ow, height: this.oh}); 
                                                } else {                                                    
                                                    this.attr({ width: this.ow + dx, height: this.oh + dy });     
                                                }
                                            } else if(this.attr("cursor")=="sw-resize") {
                                                if(( (this.ow - dx) + (wpscftx - this.ox)) > wpscftwidth || (this.oh + dy - (wpscfty - this.oy)) > wpscftheight || this.attr("y") > wpscfty_end || this.attr("x") > wpscftx_end || (this.ox + dx) < wpscftx) {
                                                    //this.attr({x: this.ox, y: this.oy, width: this.ow, height: this.oh}); 
                                                } else {                                                    
                                                    this.attr({ x: this.ox + dx, width: this.ow - dx, height: this.oh + dy });           
                                                }
                                            } else { // Not a resize, so is it a valid drag:

                                                if (this.attr("y") > wpscfty_end || this.attr("x") > wpscftx_end) {
                                                    this.attr({x: this.ox + dx, y: this.oy + dy}); 
                                                } else {
                                                    nowX = Math.min(wpscftx_end - this.attr("width"), this.ox + dx);
                                                    nowY = Math.min(wpscfty_end - this.attr("height"), this.oy + dy);
                                                    nowX = Math.max(wpscftx, nowX);
                                                    nowY = Math.max(wpscfty, nowY);            
                                                    this.attr({x: nowX, y: nowY });
                                                }
                                            }

                                        },
                                        wpsc_xup = function () {
                                            this.dragging = false; 
                                            jQuery("#wpsc-custom-selected-image-x-'.$result['custkey'].'").val(this.attr("x"));
                                            jQuery("#wpsc-custom-selected-image-y-'.$result['custkey'].'").val(this.attr("y"));
                                            jQuery("#wpsc-custom-selected-image-width-'.$result['custkey'].'").val(this.attr("width"));
                                            jQuery("#wpsc-custom-selected-image-height-'.$result['custkey'].'").val(this.attr("height"));                                            
                                        };   
                                            
                                        wpsc_newimage['.$count.'].drag(wpsc_xmove, wpsc_xstart, wpsc_xup);

                                    }

                                }); 

                                jQuery("#wpsc-customize-toolbar-form-'.$result['custkey'].'").submit(function() { 
                                    jQuery(this).ajaxSubmit();
                                    return false;
                                });         
                            });    
                        /* ]]> */
                        </script>
                Area '.$count.' - </td><td><input type="hidden" id="wpsc-customize-frontend-record-val-'.$result['custkey'].'" name="wpsc-customize-frontend-record-val-'.$result['custkey'].'" value="'.$result['custkey'].'" style="width:40px;" /><input type="hidden" id="wpsc-customize-frontend-record-primkey-val-'.$result['custkey'].'" name="wpsc-customize-frontend-record-primkey-val-'.$result['custkey'].'" value="'.$result['primkey'].'" readonly="true" style="width:40px;" />';
            $output .= '<form id="wpsc-customize-toolbar-form-'.$result['custkey'].'" action="'.plugins_url().'/wpstorecart/plugins/wpsc-user-customize-products/toolbar.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" value="'.$result['primkey'].'" name="wpsc_custom_upload_primkey" />
                         '; 
            
                        if($result['allowedcustomizations']=='pics'){$output.='<table><tr><td> Your image:</td><td><input type="file" id="Filedata-'.$result['custkey'].'" name="Filedata" /><button type="submit"><img src="'.plugins_url().'/wpstorecart/images/images.png" alt="" /> Upload Image</button><a href="" id="wpsc-custom-delete-'.$result['custkey'].'" style="display:none;"><img src="'.plugins_url().'/wpstorecart/images/delete.png" onclick="wpscCustomDeleteImage(jQuery(\'#wpsc-custom-selected-image-count-'.$result['custkey'].'\').val());jQuery(\'#Filedata-'.$result['custkey'].'\').val(\'\'); jQuery(\'#wpsc-custom-delete-'.$result['custkey'].'\').hide();return false;" alt="" /></a> <input type="hidden" name="wpsc-custom-selected-image-'.$result['custkey'].'" id="wpsc-custom-selected-image-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-count-'.$result['custkey'].'" id="wpsc-custom-selected-image-count-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-x-'.$result['custkey'].'" id="wpsc-custom-selected-image-x-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-y-'.$result['custkey'].'" id="wpsc-custom-selected-image-y-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-width-'.$result['custkey'].'" id="wpsc-custom-selected-image-width-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-height-'.$result['custkey'].'" id="wpsc-custom-selected-image-height-'.$result['custkey'].'" value="" /> <input type="hidden" name="wpsc-custom-selected-customtext-'.$result['custkey'].'" id="wpsc-custom-selected-customtext-'.$result['custkey'].'" value="" /> <input type="hidden" name="wpsc-custom-selected-textformat-'.$result['custkey'].'" id="wpsc-custom-selected-textformat-'.$result['custkey'].'"  value="" /> <input type="hidden" name="wpsc-custom-selected-textx-'.$result['custkey'].'" id="wpsc-custom-selected-textx-'.$result['custkey'].'"  value="" /> <input type="hidden" name="wpsc-custom-selected-texty-'.$result['custkey'].'" id="wpsc-custom-selected-texty-'.$result['custkey'].'"  value="" /> <input type="hidden" name="wpsc-custom-selected-textwidth-'.$result['custkey'].'" id="wpsc-custom-selected-textwidth-'.$result['custkey'].'"  value="" /> <input type="hidden" name="wpsc-custom-selected-textheight-'.$result['custkey'].'" id="wpsc-custom-selected-textheight-'.$result['custkey'].'"  value="" /></td></tr></table>';};
                        if($result['allowedcustomizations']=='text'){$output.='<table><tr><td> Your text:</td><td><input type="text" value="" id="wpsc-custom-selected-customtext-'.$result['custkey'].'" style="width:150px;" /> <button onclick="wpscCustomPutText('.$result['custkey'].', jQuery(\'#wpsc-custom-selected-customtext-'.$result['custkey'].'\').val());return false;"><img src="'.plugins_url().'/wpstorecart/images/pencil.png" alt="" /> Add Text</button> <button style="width:26px;padding:2px;" onclick="wpscCustomTextBold('.$result['custkey'].');return false;"><img src="'.plugins_url().'/wpstorecart/plugins/wpsc-user-customize-products/image/text_bold.png" alt="" /></button> <button style="width:26px;padding:2px;" onclick="wpscCustomTextItalic('.$result['custkey'].');return false;"><img src="'.plugins_url().'/wpstorecart/plugins/wpsc-user-customize-products/image/text_italic.png" alt="" /></button> Size: <select onchange="wpscCustomTextFontSize('.$result['custkey'].');" id="wpsc-custom-selected-text-size-'.$result['custkey'].'"><option value="10">10</option><option value="12">12</option><option value="14">14</option><option value="18">18</option><option value="21">21</option><option value="24">24</option><option value="28">28</option><option value="32">32</option><option value="36">36</option><option value="42">42</option><option value="48">48</option><option value="56">56</option><option value="64">64</option><option value="72">72</option><option value="84">84</option><option value="96">96</option></select> <input type="hidden" name="wpsc-custom-selected-image-'.$result['custkey'].'" id="wpsc-custom-selected-image-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-count-'.$result['custkey'].'" id="wpsc-custom-selected-image-count-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-x-'.$result['custkey'].'" id="wpsc-custom-selected-image-x-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-y-'.$result['custkey'].'" id="wpsc-custom-selected-image-y-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-width-'.$result['custkey'].'" id="wpsc-custom-selected-image-width-'.$result['custkey'].'" value="" /><input type="hidden" name="wpsc-custom-selected-image-height-'.$result['custkey'].'" id="wpsc-custom-selected-image-height-'.$result['custkey'].'" value="" /> <input type="hidden" name="wpsc-custom-selected-textformat-'.$result['custkey'].'" id="wpsc-custom-selected-textformat-'.$result['custkey'].'"  value="" /> <input type="hidden" name="wpsc-custom-selected-textx-'.$result['custkey'].'" id="wpsc-custom-selected-textx-'.$result['custkey'].'"  value="" /> <input type="hidden" name="wpsc-custom-selected-texty-'.$result['custkey'].'" id="wpsc-custom-selected-texty-'.$result['custkey'].'"  value="" /> <input type="hidden" name="wpsc-custom-selected-textwidth-'.$result['custkey'].'" id="wpsc-custom-selected-textwidth-'.$result['custkey'].'"  value="" /> <input type="hidden" name="wpsc-custom-selected-textheight-'.$result['custkey'].'" id="wpsc-custom-selected-textheight-'.$result['custkey'].'"  value="" /> <input type="text" style="position:relative;z-index:2147483647;" value="#000000" id="wpsc-custom-colorpicker-'.$result['custkey'].'"/> <script type="text/javascript">jQuery("#wpsc-custom-colorpicker-'.$result['custkey'].'").spectrum({ color: "#000", className: "wpsc-spectrum", showInput: true, showPalette: true, showPaletteOnly: false, showSelectionPalette: true, maxPaletteSize: 10, preferredFormat: "hex", 
                            change: function(color) {
                                wpscCustomTextColor('.$result['custkey'].', color.toHexString());
                            },
                            palette: [
                                    ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
                                    "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
                                    ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
                                    "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
                                    ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
                                    "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
                                    "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
                                    "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
                                    "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
                                    "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
                                    "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
                                    "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
                                    "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
                                    "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
                                ]} );</script><style type="text/css">.wpsc-spectrum{position:relative;z-index:2147483647;}; .wpsc-spectrum .sp-palette {max-width: 200px; }</style> 
                                <select id="wpscCustomTextSelectedFont-'.$result['custkey'].'" onchange="wpscCustomTextFont('.$result['custkey'].', jQuery(this).val());">
                                    <option value="Arial">Arial</option>
                                    <option value="Arial Black">Arial Black</option>
                                    <option value="Comic Sans MS">Comic Sans MS</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Impact">Impact</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Verdana">Verdana</option>
                                </select>
                            </td></tr></table>';}

                $output .= '</form></td>';
            $output .= '</tr>';
            $count++;
        }
    }
    return $output;
}








// Save the customizations.  Note that if you're using variations, the primkey provided here may not be the primkey associated with the specific variation
function wpscCustomizeProductSaveCustomization($primkey) {
    global $wpdb, $current_user;
    
    if(@!isset($_SESSION)) {
            @session_start();
    }    
    $_SESSION['wpsc_custom_keys'] = null;    
    
    wp_get_current_user();
    if ( 0 == $current_user->ID ) {
        // Not logged in.
        $theuser = 0;
    } else {
        $theuser = $current_user->ID;
    }         
    
    if ( isset($_SERVER["REMOTE_ADDR"]) )    {
        $wpsc_c_ip_address = esc_sql($_SERVER["REMOTE_ADDR"]);
    } else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    {
        $wpsc_c_ip_address =  esc_sql($_SERVER["HTTP_X_FORWARDED_FOR"]);
    } else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    {
        $wpsc_c_ip_address =  esc_sql($_SERVER["HTTP_CLIENT_IP"]);
    }    
    
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    if(@isset($_GET['wpsc-app-store'])) {
        if(strpos(get_permalink($wpStoreCartOptions['checkoutpage']),'?')===false) {
            $permalink = get_permalink($wpStoreCartOptions['checkoutpage']) .'?wpsc-app-store=1';
        } else {
            $permalink = get_permalink($wpStoreCartOptions['checkoutpage']) .'&wpsc-app-store=1';
        }    
    } else {
        $permalink = get_permalink($wpStoreCartOptions['checkoutpage']);
    }        
    
    $output = null;
    $results = wpscCustomizeProductLoadDefinitions($primkey); 
    if(@isset($results[0]['primkey'])) {
        $i = 0;
        $len = count($results);        
        foreach ($results as $result) {
            $first = '0';
            if ($i == $len - 1) {
                $first = '1';
            }
            
            @$output .= "jQuery.post('".plugins_url()."/wpstorecart/plugins/wpsc-user-customize-products/save_custom_order.php', {first: {$first}, wpstorecartitemqty: jQuery('.wpstorecart-item-qty').val(), productkey: jQuery('.wpstorecart-item-id').val(), userid: {$theuser}, custdefkey: {$result['custkey']}, ipaddress: '{$wpsc_c_ip_address}', guestemail: '".esc_sql($_SESSION['wpsc_email'])."', customtext: jQuery('#wpsc-custom-selected-customtext-{$result['custkey']}').val(), textformat: jQuery('#wpsc-custom-selected-textformat-{$result['custkey']}').val(), textx: jQuery('#wpsc-custom-selected-textx-{$result['custkey']}').val(), texty: jQuery('#wpsc-custom-selected-texty-{$result['custkey']}').val(), textwidth: jQuery('#wpsc-custom-selected-textwidth-{$result['custkey']}').val(), textheight: jQuery('#wpsc-custom-selected-textheight-{$result['custkey']}').val(), image: jQuery('#wpsc-custom-selected-image-{$result['custkey']}').val(), imagex: jQuery('#wpsc-custom-selected-image-x-{$result['custkey']}').val(), imagey: jQuery('#wpsc-custom-selected-image-y-{$result['custkey']}').val(), imagewidth: jQuery('#wpsc-custom-selected-image-width-{$result['custkey']}').val(), imageheight: jQuery('#wpsc-custom-selected-image-height-{$result['custkey']}').val()  }, function(data) {
            ";

            if ($i == $len - 1) {
                @$output .= " window.location = '{$permalink}'; ";
            } 
            @$output .= "           }); 
                        ";            
            
            $i++;
        }   
    }
    return $output;
}







// Replace the regular add to cart button
function wpscCustomizeProductBeforeAddToCart() {
    global $wpsc_results;
    $output = NULL;
    if( wpscCustomizeProductCountDefinitions($wpsc_results[0]['primkey']) > 0 ) {
   
        $output .= '<a href="#wpsc-customize-frontend-form" class="wpsc-inline"><button class="wpsc-button" onclick="return false;">Customize This Product</button></a>';
        $output .= '
                    <script type="text/javascript">
                    /* <![CDATA[ */
                    
                        // Required global variables
                        var wpsc_rect = [];
                        var wpsc_newimage = [];
                        var wpsc_paper = null; 
                        var wpsc_text = [];
                        var wpsc_text_size = [];
                        var wpsc_text_bold = [];
                        var wpsc_text_italic = [];
                        var wpsc_text_color = [];
                        var wpsc_text_font = [];

                        function wpscUpdateTextFormat(wpscc_id) {
                            jQuery("#wpsc-custom-selected-textformat-"+wpscc_id).val(wpsc_text_size[wpscc_id]+","+wpsc_text_bold[wpscc_id]+","+wpsc_text_italic[wpscc_id]+","+wpsc_text_color[wpscc_id]+","+wpsc_text_font[wpscc_id]);
                        }
                        
                        function wpscCustomTextFont(wpscc_id, font) {
                            if( typeof wpsc_text[wpscc_id] != "undefined" ) {
                                wpsc_text_font[wpscc_id] = font;
                                wpsc_text[wpscc_id].attr({"font-family" : font});
                                wpscUpdateTextFormat(wpscc_id);
                            }
                        }

                        function wpscCustomDeleteImage(keytoremove) {
                            try {
                                wpsc_newimage[keytoremove].remove();
                            } catch(err) {
                                //
                            }                        
                        }
                        
                        function wpscCustomTextColor(wpscc_id, color) {
                            if( typeof wpsc_text[wpscc_id] != "undefined" ) {
                                wpsc_text[wpscc_id].attr({"fill" : color});
                                wpsc_text_color[wpscc_id] = jQuery("#wpsc-custom-colorpicker-" + wpscc_id).val();
                                wpscUpdateTextFormat(wpscc_id);
                            }
                        }
                        
                        function wpscCustomTextBold(wpscc_id) {
                            if( typeof wpsc_text[wpscc_id] != "undefined" ) {                        
                                if(wpsc_text[wpscc_id].attr("font-weight") == "normal" || wpsc_text[wpscc_id].attr("font-weight") !=  "bold") {
                                    wpsc_text[wpscc_id].attr({"font-weight" : "bold"});
                                    wpsc_text_bold[wpscc_id] = true;
                                } else {
                                    wpsc_text[wpscc_id].attr({"font-weight" : "normal"});
                                    wpsc_text_bold[wpscc_id] = false;
                                }
                                wpscUpdateTextFormat(wpscc_id);
                            }
                        }
                        
                        function wpscCustomTextItalic(wpscc_id) {
                            if( typeof wpsc_text[wpscc_id] != "undefined" ) {
                                if(wpsc_text[wpscc_id].attr("font-style") == "normal" ) {
                                    wpsc_text[wpscc_id].attr({"font-style" : "italic"});
                                    wpsc_text_italic[wpscc_id] = true;
                                } else {
                                    wpsc_text[wpscc_id].attr({"font-style" : "normal"});
                                    wpsc_text_italic[wpscc_id] = false;
                                }
                                wpscUpdateTextFormat(wpscc_id);
                            }
                        }
                        

                        function wpscCustomTextFontSize(wpscc_id) {
                            if( typeof wpsc_text[wpscc_id] != "undefined" ) {
                                var wpsc_temp_font_size = jQuery("#wpsc-custom-selected-text-size-"+wpscc_id).val();
                                wpsc_text[wpscc_id].attr({"font-size" : wpsc_temp_font_size}); 
                                wpsc_text_size[wpscc_id] = wpsc_temp_font_size;
                                wpscUpdateTextFormat(wpscc_id);
                            }
                        }

                        function wpscCustomPutText(wpscc_id, wpscc_text) {
                            if( typeof wpsc_text[wpscc_id] == "undefined" ) {
                                wpsc_text[wpscc_id] = wpsc_paper.text(wpsc_rect[wpscc_id].attr("x"), wpsc_rect[wpscc_id].attr("y"), wpscc_text);
                                wpsc_text_size[wpscc_id] = 12;
                                wpsc_text_bold[wpscc_id] = false;
                                wpsc_text_italic[wpscc_id] = false;
                                wpsc_text_color[wpscc_id] = jQuery("#wpsc-custom-colorpicker-" + wpscc_id).val();
                                wpsc_text[wpscc_id].attr({"fill" : jQuery("#wpsc-custom-colorpicker-" + wpscc_id).val()});
                                wpsc_text_font[wpscc_id] = jQuery("#wpscCustomTextSelectedFont-"+wpscc_id).val();
                                wpsc_text[wpscc_id].attr({"font-family" : jQuery("#wpscCustomTextSelectedFont-"+wpscc_id).val()});
                            } else {
                                var wpsc_save_old_attribute_x = wpsc_text[wpscc_id].attr("x");
                                var wpsc_save_old_attribute_y = wpsc_text[wpscc_id].attr("y");
                                wpsc_text[wpscc_id].remove();
                                wpsc_text[wpscc_id] = wpsc_paper.text(wpsc_save_old_attribute_x, wpsc_save_old_attribute_y, wpscc_text);
                                wpsc_text[wpscc_id].attr({"fill" : wpsc_text_color[wpscc_id]});
                                wpsc_text[wpscc_id].attr({"font-family" : wpsc_text_font[wpscc_id]});
                                wpsc_text_bold[wpscc_id] = false;
                                wpsc_text_italic[wpscc_id] = false;                                
                            }
                            wpscCustomTextFontSize(wpscc_id);
                            wpscUpdateTextFormat(wpscc_id);

                            function wpscCustTextChangeCursor(e, mouseX, mouseY) {
                                if (this.dragging === true) {
                                    return;
                                }

                                var relX = mouseX - jQuery("#wpsc-raph-workspace").offset().left - wpsc_text[wpscc_id].getBBox().x;
                                var relY = mouseY - jQuery("#wpsc-raph-workspace").offset().top - wpsc_text[wpscc_id].getBBox().y;

                                this.attr("cursor", "move");
                            }
                                        
                            wpsc_text[wpscc_id].mousemove(wpscCustTextChangeCursor);

                            var wpsctexttx = wpsc_rect[wpscc_id].attr("x");
                            var wpsctextty = wpsc_rect[wpscc_id].attr("y");
                            var wpsctexttwidth = wpsc_rect[wpscc_id].attr("width");
                            var wpsctexttheight = wpsc_rect[wpscc_id].attr("height");
                            var wpsctexttx_end = wpsctexttx + wpsctexttwidth;
                            var wpsctextty_end = wpsctextty + wpsctexttheight;

                            function wpsc_tstart(dx, dy) {

                                    // storing original coordinates
                                    this.ox = wpsc_text[wpscc_id].getBBox().x;
                                    this.oy = wpsc_text[wpscc_id].getBBox().y;
                                    this.ow = wpsc_text[wpscc_id].getBBox().width;
                                    this.oh = wpsc_text[wpscc_id].getBBox().height;
                                    this.dragging = true;

                            };

                            var wpsc_tmove = function(dx, dy) {
                                    
                                    if ( wpsc_text[wpscc_id].getBBox().y > wpsctextty_end || wpsc_text[wpscc_id].getBBox().x > wpsctexttx_end) {
                                        this.attr({x: this.ox + dx, y: this.oy + dy}); 
                                    } else {
                                        nowX = Math.min(wpsctexttx_end - (wpsc_text[wpscc_id].getBBox().width / 2), this.ox + dx);
                                        nowY = Math.min(wpsctextty_end - (wpsc_text[wpscc_id].getBBox().height / 2), this.oy + dy);
                                        nowX = Math.max(wpsctexttx + (wpsc_text[wpscc_id].getBBox().width / 2), nowX);
                                        nowY = Math.max(wpsctextty + (wpsc_text[wpscc_id].getBBox().height / 2), nowY);            
                                        this.attr({x: nowX, y: nowY });
                                    }

                            },
                            wpsc_tup = function () {
                                this.dragging = false; 
                                jQuery("#wpsc-custom-selected-textx-" + wpscc_id).val(wpsc_text[wpscc_id].getBBox().x);
                                jQuery("#wpsc-custom-selected-texty-" + wpscc_id).val(wpsc_text[wpscc_id].getBBox().y);
                                jQuery("#wpsc-custom-selected-textwidth-" + wpscc_id).val(wpsc_text[wpscc_id].getBBox().width);
                                jQuery("#wpsc-custom-selected-textheight-" + wpscc_id).val(wpsc_text[wpscc_id].getBBox().height);                                            
                            };                             

                            wpsc_text[wpscc_id].drag(wpsc_tmove, wpsc_tstart, wpsc_tup);
                            
                        }

                        jQuery(document).ready(function(){
                            jQuery(".wpsc-inline").colorbox({inline:true, escKey:false});

                            var wpsccustomizeimg = new Image();
                            wpsccustomizeimg.src = "'.$wpsc_results[0]['thumbnail'].'";    
                            wpsccustomizeimg.onmousedown="return false;";
                            wpsccustomizeimg.onload = function() {

                                wpsc_paper = Raphael("wpsc-raph-workspace", this.width, this.height);
                                '.wpscCustomizeProductDisplayJSDefinitions($wpsc_results[0]['primkey'], false).'

                                var backimage = wpsc_paper.rect(0,0,this.width, this.height).attr({
                                    fill: \'url("'.$wpsc_results[0]['thumbnail'].'")\'
                                });

                                backimage.toBack();

                            }

                        });
                        
                        function wpscCustomProductAddToCart() {
                            '.wpscCustomizeProductSaveCustomization($wpsc_results[0]['primkey']).'
                        }
                        
                    /* ]]> */
                    </script>
                    
                    <div style="display:none;">
                        <div id="wpsc-customize-frontend-form" style="padding:10px; background:#fff;">
                            <h1>Customize This Product</h1>
                            <br />
                            <div id="wpsc-raph-workspace" style="width: 100%; height: 100%;"></div>
                            <p>Click the buttons below to upload your own pictures (transparent .PNG images are recommended.) </p>
                            <table>
                            '.wpscCustomizeProductDisplayFrontendDefinitions($wpsc_results[0]['primkey']).'
                            </table>
                            <br style="clear:both;" />
                            <div style="float:right;"><button onclick="wpscCustomProductAddToCart();">Add to Cart</button></div>
                            <br style="clear:both;" />
                        </div>
                    </div>
            ';
        $output .= '<div style="display:none;">';
    }
    return $output;
}

function wpscCustomizeProductAfterAddToCart() {
    global $wpsc_results;
    $output = NULL;
    if( wpscCustomizeProductCountDefinitions($wpsc_results[0]['primkey']) > 0 ) {
        $output .= '</div>';
    }    
    return $output;
}

add_filter('wpsc_display_product_before_addtocart', 'wpscCustomizeProductBeforeAddToCart');
add_filter('wpsc_display_product_after_addtocart', 'wpscCustomizeProductAfterAddToCart');



function wpscCustomizeProductGetOrders() {
    global $wpdb;

    $results = $wpdb->get_results("SELECT `{$wpdb->prefix}wpstorecart_custom_orders`.`primkey`, `{$wpdb->prefix}wpstorecart_custom_orders`.`productkey`, `{$wpdb->prefix}wpstorecart_custom_orders`.`userid`, `{$wpdb->prefix}wpstorecart_custom_orders`.`orderid`, `{$wpdb->prefix}wpstorecart_products`.`primkey`, `{$wpdb->prefix}wpstorecart_products`.`name`, `{$wpdb->prefix}wpstorecart_products`.`producttype`, `{$wpdb->prefix}wpstorecart_products`.`postid`, `{$wpdb->prefix}wpstorecart_orders`.`orderstatus` FROM `{$wpdb->prefix}wpstorecart_custom_orders`, `{$wpdb->prefix}wpstorecart_products`, `{$wpdb->prefix}wpstorecart_orders`  WHERE `{$wpdb->prefix}wpstorecart_custom_orders`.`productkey`=`{$wpdb->prefix}wpstorecart_products`.`primkey` AND `{$wpdb->prefix}wpstorecart_custom_orders`.`orderid`= `{$wpdb->prefix}wpstorecart_orders`.`primkey`  AND `{$wpdb->prefix}wpstorecart_custom_orders`.`orderid`!='0' GROUP BY `{$wpdb->prefix}wpstorecart_custom_orders`.`orderid`;", ARRAY_A);
    return $results;
}



function wpscHideCustomBeforeAddToCartOnCatalog() {
    global $wpsc_result;
    if( wpscCustomizeProductCountDefinitions($wpsc_result['primkey']) > 0 ) {
        $output .= '<div style="display:none;">';
    }    
    return $output;    
}
function wpscHideCustomAfterAddToCartOnCatalog() {
    global $wpsc_result;
    if( wpscCustomizeProductCountDefinitions($wpsc_result['primkey']) > 0 ) {
        $output .= '</div>';
    }    
    return $output;    
}
add_filter('wpsc_display_catalog_before_addtocart', 'wpscHideCustomBeforeAddToCartOnCatalog'); 
add_filter('wpsc_display_catalog_after_addtocart', 'wpscHideCustomAfterAddToCartOnCatalog');

// Admin menu
function wpscCustomizeProductOrderEntry() {
    echo '<li><img src="'. plugins_url() . '/wpstorecart/images/images.png" class="wpsc-admin-submenu-icon" /> <a href="admin.php?page=wpstorecart-custom-product-orders">Orders With Customized Products</a></li>';
}
add_action('wpsc_admin_menu_inside_orders', 'wpscCustomizeProductOrderEntry', 1);







// Admin page to view custom orders
function wpscAdminPageCustomizeProductOrders() {
        global $wpdb, $user_info;
        wpscCheckAdminPermissions();
        wpscAdminHeader('Orders With Customized Products');    
        echo '<div class="grid_16">'; 
        $results = wpscCustomizeProductGetOrders();
        echo '<table class="widefat wpsc5table" style="position:relative;z-index:2;"><thead><tr><th>#</th><th>Product</th><th>User</th><th>Order ID</th><th>Order Status</th><th>View</th></tr></thead><tbody>';
        foreach($results as $result) {
            if($result['producttype']=='attribute') {
                $newresults = $wpdb->get_results("SELECT `name` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$result['postid']}';", ARRAY_A);
                if(@isset($newresults[0]['name'])) {
                    $result['name'] = $newresults[0]['name'] .' - '. $result['name'];
                }
            }
            if($result['userid'] > 0) {
                $user_info = get_userdata($result['userid']);
                $name = $user_info->user_login;
            } else {
                $name = $result['guestemail'];
            }
            echo '<tr><th>'.$result['primkey'].'</th><th>'.$result['name'].'</th><th>'.$name.'</th><th>'.$result['orderid'].'</th><th>'.$result['orderstatus'].'</th><th><a href="admin.php?page=wpstorecart-custom-product-view&orderid='.$result['orderid'].'"><button>View</button></a></th></tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
        wpscAdminFooter();        
}


// Outputs the custom JS to display the customizations
function wpscCustomizeProductDisplayCustomizationsJS($wpsc_first_results) {
    global $wpsc_wordpress_upload_dir;
    $output = null;
    $textcount = 0;
    $imgcount = 0;
    // Visual representation
    foreach ($wpsc_first_results as $wpsc_first_result) {
        if( trim($wpsc_first_result['customtext']) != '' ) { // We have some custom text

            $textattributes = explode(',', $wpsc_first_result['textformat']);
            $output .= '
                wpsc_text['.$textcount.'] = wpsc_paper.text('.$wpsc_first_result['textx'].', '.$wpsc_first_result['texty'].', "'.$wpsc_first_result['customtext'].'");
                wpsc_text['.$textcount.'].attr({"fill" : "'.$textattributes[3].'"});
                wpsc_text['.$textcount.'].attr({"font-family" : "'.$textattributes[4].'"}); 
                wpsc_text['.$textcount.'].attr({"font-size" : '.$textattributes[0].'}); 

                ';
            if($textattributes[1]=="true") {
                $output .= 'wpsc_text['.$textcount.'].attr({"font-weight" : "bold"});
                    ';
            } else {
                $output .= 'wpsc_text['.$textcount.'].attr({"font-weight" : "normal"});
                    ';
            }
            if($textattributes[2]=="true") {
                $output .= 'wpsc_text['.$textcount.'].attr({"font-style" : "italic"});
                    ';
            } else {
                $output .= 'wpsc_text['.$textcount.'].attr({"font-style" : "normal"});
                    ';
            }                        
            $truex = ($wpsc_first_result['textwidth'] / 2) + $wpsc_first_result['textx'];
            $truey = ($wpsc_first_result['textheight'] / 2) + $wpsc_first_result['texty'];
            $output .= '
                

                wpsc_text['.$textcount.'].attr({"x" : '.$truex.'}); 
                wpsc_text['.$textcount.'].attr({"y" : '.$truey.'});                 
                wpsc_text['.$textcount.'].toFront();
                    ';    

            $textcount++;
        }
        if( trim($wpsc_first_result['image']) != '' ) { // We have some custom text 
            $output .= "wpsc_newimage[{$imgcount}] = wpsc_paper.image('{$wpsc_wordpress_upload_dir['baseurl']}/wpstorecart/{$wpsc_first_result['image']}', {$wpsc_first_result['imagex']}, {$wpsc_first_result['imagey']}, {$wpsc_first_result['imagewidth']}, {$wpsc_first_result['imageheight']});
            wpsc_newimage[{$imgcount}].toFront();
            ";
            $imgcount++;
        }       
        
    }
    

    return $output;
}

function wpscCustomizeProductDisplayCustomizationsRawData($wpsc_first_results) {
    global $wpsc_wordpress_upload_dir;
    // Just the data
    $output = '<h2 class="dark-text">Raw Data:</h2><table class="widefat wpsc5table">';
    $output .= '<tr><th>Custom Text</th><th>textformat</th><th>textx</th><th>texty</th><th>textwidth</th><th>textheight</th><th>image</th><th>imagex</th><th>imagey</th><th>imagewidth</th><th>imageheight</th></tr>';
    foreach ($wpsc_first_results as $wpsc_first_result) {
        $output .= "<tr><td>{$wpsc_first_result['customtext']}</td><td>{$wpsc_first_result['textformat']}</td><td>{$wpsc_first_result['textx']}</td><td>{$wpsc_first_result['texty']}</td><td>{$wpsc_first_result['textwidth']}</td><td>{$wpsc_first_result['textheight']}</td>";
        if( trim($wpsc_first_result['image']) != '' ) {
            $output .= "<td><a href=\"{$wpsc_wordpress_upload_dir['baseurl']}/wpstorecart/{$wpsc_first_result['image']}\">{$wpsc_wordpress_upload_dir['baseurl']}/wpstorecart/{$wpsc_first_result['image']}</a></td>"; 
        } else {
            $output .= "<td></td>";
        }
            
        $output .= "<td>{$wpsc_first_result['imagex']}</td><td>{$wpsc_first_result['imagey']}</td><td>{$wpsc_first_result['imagewidth']}</td><td>{$wpsc_first_result['imageheight']}</td></tr>";
    }
    $output .= '</table>';   
    return $output;
}


// View a custom order admin page:
function wpscAdminPageViewCustomizedOrder() {
        global $wpdb;
        wpscCheckAdminPermissions();
        wpscAdminHeader('View a Customized Product');   
        $output = null;
        @$orderid = intval($_GET['orderid']);
        $wpsc_first_results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_custom_orders` WHERE `orderid`='{$orderid}' ;", ARRAY_A);
        $wpsc_results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$wpsc_first_results[0]['productkey']}' ;", ARRAY_A);
        if($wpsc_results[0]['producttype']=='attribute' && trim($wpsc_results[0]['thumbnail'])=='' ) {
            $wpsc_results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$wpsc_results[0]['postid']}' ;", ARRAY_A);
        }
        
        if(@isset($wpsc_results[0]['thumbnail'])) {

            $output .= '
            <script type="text/javascript">
            /* <![CDATA[ */

                // Required global variables
                var wpsc_rect = [];
                var wpsc_newimage = [];
                var wpsc_paper = null; 
                var wpsc_text = [];
                
                jQuery(document).ready( function() {

                    var wpsccustomizeimg = new Image();
                    wpsccustomizeimg.src = "'.$wpsc_results[0]['thumbnail'].'";    
                    wpsccustomizeimg.onmousedown="return false;";
                    wpsccustomizeimg.onload = function() {

                        wpsc_paper = Raphael("wpsc-raph-workspace", this.width, this.height);
                        

                        var backimage = wpsc_paper.rect(0,0,this.width, this.height).attr({
                            fill: \'url("'.$wpsc_results[0]['thumbnail'].'")\'
                        });

                        backimage.toBack();
                        '.  wpscCustomizeProductDisplayCustomizationsJS($wpsc_first_results).'

                    }

                });                
                
            /* ]]> */
            </script>
                ';
            $output .= wpscCustomizeProductDisplayCustomizationsRawData($wpsc_first_results);
        } else {
            $output .= 'Could not find the custom order specified.';
        }
        echo '<div class="grid_16">';
        echo '<div id="wpsc-raph-workspace" style="width: 100%; height: 100%;"></div>';
        echo $output;
        echo '</div>';
        wpscAdminFooter();         
}




function wpscAdminHeadCustomOrderView() {
    wpscAdminHead();
    wp_enqueue_script('wpsc-raphael', plugins_url('/js/raphael.js', __FILE__) );
}




// Load the admin page
function wpscCustomizeProductAdminPages() {
    $wpscCustomizeProductOrdersPage = add_submenu_page(NULL, 'Custom Orders - wpStoreCart ', 'Custom Orders', 'manage_wpstorecart', 'wpstorecart-custom-product-orders', 'wpscAdminPageCustomizeProductOrders');
    $wpscCustomizeViewCustomPage = add_submenu_page(NULL, 'View a Custom Order - wpStoreCart ', 'View a Custom Order', 'manage_wpstorecart', 'wpstorecart-custom-product-view', 'wpscAdminPageViewCustomizedOrder');
    add_action('admin_head-' . $wpscCustomizeViewCustomPage, 'wpscAdminHeadCustomOrderView');
    add_action('admin_head-' . $wpscCustomizeProductOrdersPage, 'wpscAdminHead');
}
add_action('admin_menu', 'wpscCustomizeProductAdminPages', 1); 









// Get the scripts and styles for the frontend
function wpscCustomizeProductPageEnqueue() {
    global $post;
    
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 
    
    if(is_page() && ($post->post_parent == $wpStoreCartOptions['mainpage'])) { // If we're editing the single product page
        wp_register_style('wpsc-colorbox-css', plugins_url() . '/wpstorecart/js/colorbox/colorbox.css');
        wp_enqueue_style('wpsc-colorbox-css'); 
        wp_register_style('wpsc-spectrum-css', plugins_url() . '/wpstorecart/js/spectrum-colorpicker/spectrum.css');
        wp_enqueue_style('wpsc-spectrum-css');         
        wp_enqueue_script('jquery');
        wp_enqueue_script('wpsc-colorbox', plugins_url() . '/wpstorecart/js/colorbox/jquery.colorbox-min.js');  
        wp_enqueue_script('wpsc-spectrum', plugins_url() . '/wpstorecart/js/spectrum-colorpicker/spectrum.js');
        wp_enqueue_script('wpsc-raphael', plugins_url('/js/raphael.js', __FILE__) );
        wp_enqueue_script('wpsc-raphael-free-transform', plugins_url('/js/raphael.free_transform.js', __FILE__) );
        wp_enqueue_script('jquery-form');
    } 
}
add_action('wp_enqueue_scripts', 'wpscCustomizeProductPageEnqueue');

?>