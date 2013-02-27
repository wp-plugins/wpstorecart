
jQuery.editable.addInputType('datepicker', {
    element: function(settings, original) {
 
        var input = jQuery('<input size=8 />');
 
        // Catch the blur event on month change
        settings.onblur = function(e) {
        };
 
        input.datepicker({
            dateFormat: 'yymmdd',
            onSelect: function(dateText, inst) {
                jQuery(this).parents("form").submit();
            },
            onClose: function(dateText, inst) {
                jQuery(this).parents("form").submit();
            }
 
        });
 
        input.datepicker();
 
        jQuery(this).append(input);
        return (input);
    }
});


jQuery.editable.addInputType('alertimage', {
    element: function(settings, original) {
 
        var input = jQuery('<select name="wpsc-webmenu" id="wpsc-webmenu" class="wpsc-webmenu"><option value="Badge.png">New</option><option value="BuddyChat.png">User</option><option value="Calendar.png">Calendar</option><option value="ChartBar.png">Chart</option><option value="Chat.png">Chat</option><option value="Favorite.png">Star</option><option value="Heart.png">Heart</option><option value="Help.png">Info</option><option value="Mail1.png">Mail</option><option value="Refresh.png">Refresh</option><option value="Settings.png">Wrench</option><option value="SymbolAdd.png">Plus</option><option value="SymbolRemove.png">Minus</option><option value="SymbolCheck.png">Chech Mark</option><option value="SymbolDelete.png">Crossed Out</option><option value="Tag.png">Tag</option></select>');
 
        jQuery(this).append(input);
        
        return (input);
    }
});


jQuery.editable.addInputType('conditions', {
    element: function(settings, original) {
 
            var wpscAvailableConditions = [
                    "newsales() = true;",
                    "newviews() = true;",
                    "newaddtocart() = true;",
                    "newshipping() = true;",
                    "newcheckout() = true;",
                    "newticket() = true;",
                    "shipping() @ hours(24);",
                    "nosales() @ hours(72);",
                    "noviews() @ minutes(90);",
                    "noaddtocart() @ days(1);",
                    "nocheckout() @ days(2);",
                    "sales() < 100;",
                    "views() > 5000;",
                    "addtocart() > 100;",                   
            ];
 
        var input = jQuery('<input size=11 />');
 
        jQuery( input ).autocomplete({
                source: wpscAvailableConditions
        }); 
 
        jQuery(this).append(input);
        return (input);
    }
});


jQuery.editable.addInputType('orderstatus', {
    element: function(settings, original) {
 
        var availableTags = [
                "Awaiting Payment",
                "Authorized",
                "Cancelled",
                "Charged",
                "Completed",
                "Chargeback",
                "Dropped",
                "Invoice Sent",
                "Partially Paid",
                "Pending",
                "Refunded",
                "Under Review",
        ];
 
        var input = jQuery('<input size=11 />');
 
        jQuery( input ).autocomplete({
                source: availableTags
        }); 
 
        jQuery(this).append(input);
        return (input);
    }
});

jQuery(document).ready(function() {

    jQuery('#wpstorecart_admin_content').fadeIn(2000);
    jQuery('#wpstorecart_alert_div').slideToggle(666);

    Cufon.replace('h2');
    Cufon.replace('legend');

    // menu superfish
    jQuery('#navigationTop').superfish();

    // tags
    jQuery("#tags_input").tagsInput();

    // Redraw any datatables
    var wpscResizeDelay = 0;
    jQuery(window).bind('resize', function () {
        try {
            if(wpscResizeDelay > 5) { // The delay means that these refresh functions, which make https requests, don't fire as often, to minimize.  The higher the number, the less often the tables will redraw, but less CPU will used for your webserver.
                uTable.fnAdjustColumnSizing();
                uTable.fnDraw();
                wpscResizeDelay = 0;
            }
            wpscResizeDelay++;
        } catch(err) {

        }                                            
    } );  
    
    
    function wpscResponsiveAdmin() {
        // Menu icons
        if( jQuery(window).width() <= 1275 ) {
           
            if( jQuery(window).width() <= 902 ) {
                jQuery('.wpsc-admin-menu-text-item').hide();
                jQuery('.wpsc-admin-menu-icon').show();
            }    
            if( jQuery(window).width() > 902 ) {
                jQuery('.wpsc-admin-menu-text-item').show();
                jQuery('.wpsc-admin-menu-icon').hide();
            }              
            
        } 
        
        if( jQuery(window).width() > 1275 ) {
            jQuery('.wpsc-admin-menu-text-item').show();
            jQuery('.wpsc-admin-menu-icon').show();
        }
    
        // wpStoreCart carousel & tab icons
        if( jQuery(window).width() <= 1200 ) {
            jQuery('#wpsc-addon-carousel').hide();
            jQuery('.ui-tabs-anchor img').hide();
            jQuery('.tableDescription').hide();
        } 
        if( jQuery(window).width() > 1200 ) {
            jQuery('#wpsc-addon-carousel').show();
            jQuery('.ui-tabs-anchor img').show();
            jQuery('.tableDescription').show();
        }    
    
        // wpStoreCart logo
        if( jQuery(window).width() <= 1075 ) {
            jQuery('#wpsc-logo-li').hide();
        } 
        if( jQuery(window).width() > 1075 ) {
            jQuery('#wpsc-logo-li').show();
        }       
        
    
        
    }
    
    
    
    // Responsive admin
    wpscResponsiveAdmin(); // For initial page load
    jQuery(window).resize(function() { // On resize
        wpscResponsiveAdmin();
    });    
    

    try {    
        uTable.fnDraw();
    } catch(err) {

    }

    jQuery(".tooltip-target").ezpz_tooltip({
        contentPosition: 'aboveStatic',
        stayOnContent: true
    });

    jQuery('#wpstorecart_admin_loader2').fadeOut(500);
                                    
});