


function wpscMakeProductSingleMockup() {

        jQuery( ".wpsc-single-product" ).sortable({
            helper: 'clone',
            revert: true,
            scroll: false,
            tolerance: 'pointer',            
            start: function( event, ui ) {
                //jQuery( this ).sortable( 'refreshPositions' );
            },
            stop: function(event, ui) {
                wpscMakeProductSingleMockup();
            }
        });   
        
        jQuery( ".wpsc-product-info-sortable" ).sortable({
            helper: 'clone',
            revert: true,
            scroll: false,
            tolerance: 'pointer',            
            start: function( event, ui ) {
                //jQuery( this ).sortable( 'refreshPositions' );
            },
            stop: function(event, ui) {
                wpscMakeProductSingleMockup();
            }
        });          
        
        jQuery( ".wpsc-single-product" ).disableSelection();
        jQuery( ".wpsc-product-info" ).disableSelection();
        
        jQuery( ".wpsc-addtocart").disableSelection();
}

function wpscResetCheckboxes() {
    // This code sets our checkbox's to the correct value:
    if(jQuery('.wpsc-product-img').css('display')=='none') {jQuery("#wpsc-toggle-thumbnail").prop("checked", false);}
    if(jQuery('.wpsc-list-item-name').css('display')=='none') {jQuery("#wpsc-toggle-title").prop("checked", false);}
    if(jQuery('.wpsc-single-intro').css('display')=='none') {jQuery("#wpsc-toggle-intro").prop("checked", false);}
    if(jQuery('.wpsc-single-description').css('display')=='none') {jQuery("#wpsc-toggle-description").prop("checked", false);}
    if(jQuery('.wpsc-oldprice').css('display')=='none') {jQuery("#wpsc-toggle-strike").prop("checked", false);}
    if(jQuery('.wpsc-list-item-price').css('display')=='none') {jQuery("#wpsc-toggle-price").prop("checked", false);}
    if(jQuery('.wpsc-addtocart').css('display')=='none') {jQuery("#wpsc-toggle-addtocart").prop("checked", false);}
    if(jQuery('.wpsc-gallery').css('display')=='none') {jQuery("#wpsc-toggle-gallery").prop("checked", false);}  
    if(jQuery('.wpsc-individualqtylabel').css('display')=='none') {jQuery("#wpsc-toggle-qtylabel").prop("checked", false);}  
    if(jQuery('.wpsc-list-item-qty').css('display')=='none') {jQuery("#wpsc-toggle-qty").prop("checked", false);}  
    if(jQuery('.wpsc-list-item-inventory').css('display')=='none') {jQuery("#wpsc-toggle-inventory").prop("checked", false);}  
}


function wpscRefreshAllControls() {
    wpscResetCheckboxes(); // Set the checkboxes to the correct value

    // Change the sliders to the correct values:


    var wpscthumbnailsize = jQuery('.wpsc-product-img').css('width');
    wpscthumbnailsize = wpscthumbnailsize.replace(/\D/g,'');
    jQuery("#wpsc_slider3").mbsetVal(wpscthumbnailsize); 
    
    jQuery('#wpsc-select-edit-padding').change();
    jQuery('#wpsc-select-edit-margin').change();
    jQuery('#wpsc-select-edit-border').change();
    jQuery('#wpsc-select-edit-font').change();
    jQuery('#wpsc-select-edit-color').change();      
    
}


jQuery(document).ready(function() {

        wpscMakeProductSingleMockup();
        
        jQuery("#wpsc-tabopener").button();


        jQuery( "#wpsc_ex1a .mb_slider").mbSlider({
            minVal:20, 
            maxVal:1000,
            onSlide:function(){
                jQuery('.wpsc-product-img').stop().animate({'height':jQuery("#wpsc_slider3").mbgetVal()+'px', 'width':jQuery("#wpsc_slider3").mbgetVal()+'px'});
            }
        });                             

        jQuery( "#wpsc_ex2 .mb_slider").mbSlider({
            minVal:7, 
            maxVal:64,
            onSlide:function(){
                switch(jQuery("#wpsc-select-edit-font").val()) {
                    case 'title-font':
                        jQuery('.wpsc-list-item-name').stop().animate({'font-size':jQuery("#wpsc_slider4").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart':
                        jQuery('.wpsc-addtocart').stop().animate({'font-size':jQuery("#wpsc_slider4").mbgetVal()+'px'});
                    break;       
                    case 'more-info':
                        jQuery('.wpsc-product-info').stop().animate({'font-size':jQuery("#wpsc_slider4").mbgetVal()+'px'});
                    break;         
                    
                    case 'intro-font':
                        jQuery('.wpsc-single-intro').stop().animate({'font-size':jQuery("#wpsc_slider4").mbgetVal()+'px'});
                    break;  
                    case 'description-font':
                        jQuery('.wpsc-single-description').stop().animate({'font-size':jQuery("#wpsc_slider4").mbgetVal()+'px'});
                    break;  
                    case 'strike-font':
                        jQuery('.wpsc-oldprice').stop().animate({'font-size':jQuery("#wpsc_slider4").mbgetVal()+'px'});
                    break;  
                    case 'price-font':
                        jQuery('.wpsc-price').stop().animate({'font-size':jQuery("#wpsc_slider4").mbgetVal()+'px'});
                    break;                      
                }                                        

            }
        });   

        jQuery("#wpsc_ex3 .mb_slider").mbSlider({
            maxVal:255,
            onSlide:function(){changeColor(jQuery("#R").mbgetVal(),jQuery("#G").mbgetVal(),jQuery("#B").mbgetVal());}
        });

        jQuery("#wpsc_ex4 .mb_slider").mbSlider({
            minVal:0, 
            maxVal:32,
            onSlide:function(){
                switch(jQuery("#wpsc-select-edit-border").val()) {
                    case 'product-border':
                        jQuery('.wpsc-single-product').stop().css({'border-width':jQuery("#wpsc_slider5").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart':
                        jQuery('.wpsc-addtocart').stop().css({'border-width':jQuery("#wpsc_slider5").mbgetVal()+'px'});
                    break;       
                    case 'more-info':
                        jQuery('.wpsc-product-info').stop().css({'border-width':jQuery("#wpsc_slider5").mbgetVal()+'px'});
                    break;                                           
                }                                        

            }
        });           
        
        jQuery("#wpsc_ex5 .mb_slider").mbSlider({
            minVal:0, 
            maxVal:32,
            onSlide:function(){
                switch(jQuery("#wpsc-select-edit-margin").val()) {
                    case 'product-margin-top':
                        jQuery('.wpsc-single-product').stop().css({'margin-top':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart-top':
                        jQuery('.wpsc-addtocart').stop().css({'margin-top':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;       
                    case 'more-info-top':
                        jQuery('.wpsc-product-info').stop().css({'margin-top':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;           
                    case 'product-margin-bottom':
                        jQuery('.wpsc-single-product').stop().css({'margin-bottom':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart-bottom':
                        jQuery('.wpsc-addtocart').stop().css({'margin-bottom':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;       
                    case 'more-info-bottom':
                        jQuery('.wpsc-product-info').stop().css({'margin-bottom':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;                     
                    case 'product-margin-left':
                        jQuery('.wpsc-single-product').stop().css({'margin-left':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart-left':
                        jQuery('.wpsc-addtocart').stop().css({'margin-left':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;       
                    case 'more-info-left':
                        jQuery('.wpsc-product-info').stop().css({'margin-left':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;                     
                    case 'product-margin-right':
                        jQuery('.wpsc-single-product').stop().css({'margin-right':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart-right':
                        jQuery('.wpsc-addtocart').stop().css({'margin-right':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;       
                    case 'more-info-right':
                        jQuery('.wpsc-product-info').stop().css({'margin-right':jQuery("#wpsc_slider6").mbgetVal()+'px'});
                    break;                     
                }                                        

            }
        });               
        
        
        jQuery("#wpsc_ex6 .mb_slider").mbSlider({
            minVal:0, 
            maxVal:32,
            onSlide:function(){
                switch(jQuery("#wpsc-select-edit-padding").val()) {
                    case 'product-padding-top':
                        jQuery('.wpsc-single-product').stop().css({'padding-top':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart-top':
                        jQuery('.wpsc-addtocart').stop().css({'padding-top':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;       
                    case 'more-info-top':
                        jQuery('.wpsc-product-info').stop().css({'padding-top':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;           
                    case 'product-padding-bottom':
                        jQuery('.wpsc-single-product').stop().css({'padding-bottom':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart-bottom':
                        jQuery('.wpsc-addtocart').stop().css({'padding-bottom':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;       
                    case 'more-info-bottom':
                        jQuery('.wpsc-product-info').stop().css({'padding-bottom':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;                     
                    case 'product-padding-left':
                        jQuery('.wpsc-single-product').stop().css({'padding-left':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart-left':
                        jQuery('.wpsc-addtocart').stop().css({'padding-left':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;       
                    case 'more-info-left':
                        jQuery('.wpsc-product-info').stop().css({'padding-left':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;                     
                    case 'product-padding-right':
                        jQuery('.wpsc-single-product').stop().css({'padding-right':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;      
                    case 'add-to-cart-right':
                        jQuery('.wpsc-addtocart').stop().css({'padding-right':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;       
                    case 'more-info-right':
                        jQuery('.wpsc-product-info').stop().css({'padding-right':jQuery("#wpsc_slider7").mbgetVal()+'px'});
                    break;                     
                }                                        

            }
        });         




        jQuery('#wpsc-select-edit-padding').change(function() {
                wpscpaddingsize = null;
                switch(jQuery(this).val()) {
                    case 'product-padding-top':
                        wpscpaddingsize = jQuery('.wpsc-single-product').css('padding-top');
                    break;        
                    case 'add-to-cart-top':
                        wpscpaddingsize = jQuery('.wpsc-addtocart').css('padding-top');
                    break;           
                    case 'more-info-top':
                        wpscpaddingsize = jQuery('.wpsc-product-info').css('padding-top');
                    break;            
                    case 'product-padding-bottom':
                        wpscpaddingsize = jQuery('.wpsc-single-product').css('padding-bottom');
                    break;        
                    case 'add-to-cart-bottom':
                        wpscpaddingsize = jQuery('.wpsc-addtocart').css('padding-bottom');
                    break;           
                    case 'more-info-bottom':
                        wpscpaddingsize = jQuery('.wpsc-product-info').css('padding-bottom');
                    break;                
                    case 'product-padding-left':
                        wpscpaddingsize = jQuery('.wpsc-single-product').css('padding-left');
                    break;        
                    case 'add-to-cart-left':
                        wpscpaddingsize = jQuery('.wpsc-addtocart').css('padding-left');
                    break;           
                    case 'more-info-left':
                        wpscpaddingsize = jQuery('.wpsc-product-info').css('padding-left');
                    break;                      
                    case 'product-padding-right':
                        wpscpaddingsize = jQuery('.wpsc-single-product').css('padding-right');
                    break;        
                    case 'add-to-cart-right':
                        wpscpaddingsize = jQuery('.wpsc-addtocart').css('padding-right');
                    break;           
                    case 'more-info-right':
                        wpscpaddingsize = jQuery('.wpsc-product-info').css('padding-right');
                    break;                      
                }     
                wpscpaddingsize = wpscpaddingsize.replace(/\D/g,'');
                jQuery("#wpsc_slider7").mbsetVal(wpscpaddingsize);
        });

        jQuery('#wpsc-select-edit-margin').change(function() {
                wpscmarginsize = null;
                switch(jQuery(this).val()) {
                    case 'product-margin-top':
                        wpscmarginsize = jQuery('.wpsc-single-product').css('margin-top');
                    break;        
                    case 'add-to-cart-top':
                        wpscmarginsize = jQuery('.wpsc-addtocart').css('margin-top');
                    break;           
                    case 'more-info-top':
                        wpscmarginsize = jQuery('.wpsc-product-info').css('margin-top');
                    break;            
                    case 'product-margin-bottom':
                        wpscmarginsize = jQuery('.wpsc-single-product').css('margin-bottom');
                    break;        
                    case 'add-to-cart-bottom':
                        wpscmarginsize = jQuery('.wpsc-addtocart').css('margin-bottom');
                    break;           
                    case 'more-info-bottom':
                        wpscmarginsize = jQuery('.wpsc-product-info').css('margin-bottom');
                    break;                
                    case 'product-margin-left':
                        wpscmarginsize = jQuery('.wpsc-single-product').css('margin-left');
                    break;        
                    case 'add-to-cart-left':
                        wpscmarginsize = jQuery('.wpsc-addtocart').css('margin-left');
                    break;           
                    case 'more-info-left':
                        wpscmarginsize = jQuery('.wpsc-product-info').css('margin-left');
                    break;                      
                    case 'product-margin-right':
                        wpscmarginsize = jQuery('.wpsc-single-product').css('margin-right');
                    break;        
                    case 'add-to-cart-right':
                        wpscmarginsize = jQuery('.wpsc-addtocart').css('margin-right');
                    break;           
                    case 'more-info-right':
                        wpscmarginsize = jQuery('.wpsc-product-info').css('margin-right');
                    break;                      
                }     
                wpscmarginsize = wpscmarginsize.replace(/\D/g,'');
                jQuery("#wpsc_slider6").mbsetVal(wpscmarginsize);
        });

        jQuery('#wpsc-select-edit-border').change(function() {
                wpscbordersize = null;
                switch(jQuery(this).val()) {
                    case 'product-border':
                        wpscbordersize = jQuery('.wpsc-single-product').css('border-left-width');
                    break;        
                    case 'add-to-cart':
                        wpscbordersize = jQuery('.wpsc-addtocart').css('border-left-width');
                    break;           
                    case 'more-info':
                        wpscbordersize = jQuery('.wpsc-product-info').css('border-left-width');
                    break;                                            
                }     
                wpscbordersize = wpscbordersize.replace(/\D/g,'');
                jQuery("#wpsc_slider5").mbsetVal(wpscbordersize);

        });


        jQuery('#wpsc-select-edit-font').change(function() {
                wpscfontsize = null;
                switch(jQuery(this).val()) {
                    case 'title-font':
                        wpscfontsize = jQuery('.wpsc-list-item-name').css('font-size');
                    break;        
                    case 'add-to-cart':
                        wpscfontsize = jQuery('.wpsc-addtocart').css('font-size');
                    break;           
                    case 'more-info':
                        wpscfontsize = jQuery('.wpsc-product-info').css('font-size');
                    break;      
                    case 'intro-font':
                         wpscfontsize = jQuery('.wpsc-single-intro').css('font-size');
                    break;  
                    case 'description-font':
                         wpscfontsize = jQuery('.wpsc-single-description').css('font-size');
                    break;  
                    case 'strike-font':
                         wpscfontsize = jQuery('.wpsc-oldprice').css('font-size');
                    break;  
                    case 'price-font':
                         wpscfontsize = jQuery('.wpsc-product-price').css('font-size');
                    break;                        
                    
                    
                }     
                wpscfontsize = wpscfontsize.replace(/\D/g,'');
                jQuery("#wpsc_slider4").mbsetVal(wpscfontsize);

        });

        jQuery('#wpsc-select-edit-color').change(function() {
                wpschex = null;
                switch(jQuery(this).val()) {
                    case 'box-background':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-single-product').css('backgroundColor'));
                    break;        
                    case 'title-font':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-list-item-name').css('color'));
                    break;                                       
                    case 'box-border':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-single-product').css('borderColor'));
                    break;     
                    case 'addtocart_button':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-addtocart').css('backgroundColor'));
                    break;             
                    case 'addtocart_border':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-addtocart').css('borderColor'));
                    break;   
                    case 'addtocart_text':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-addtocart').css('color'));
                    break;                                        
                    case 'moreinfo_button':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-product-info').css('backgroundColor'));
                    break;             
                    case 'moreinfo_border':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-product-info').css('borderColor'));
                    break;    
                    case 'moreinfo_text':
                        wpschex = wpscCssColorToHEX(jQuery('.wpsc-product-info').css('color'));
                    break;                                               
                }     
                jQuery("#colorValueHex").val(wpschex);
                wpscrgb = wpscHEXtoRGB(wpschex);
                jQuery('.miniColors-trigger').css('backgroundColor',wpschex);
                jQuery("#R").mbsetVal(wpscrgb[0]);
                jQuery("#G").mbsetVal(wpscrgb[1]);
                jQuery("#B").mbsetVal(wpscrgb[2]); 

        });

        function wpscChangeColor(HEX) {
            switch(jQuery("#wpsc-select-edit-color").val()) {
                case 'box-background':
                    jQuery('.wpsc-single-product').css('backgroundColor',HEX);
                break;        
                case 'title-font':
                    jQuery('.wpsc-list-item-name').css('color',HEX);
                break;                                       
                case 'box-border':
                    jQuery('.wpsc-single-product').css('borderColor',HEX);
                break;     
                case 'addtocart_button':
                    jQuery('.wpsc-addtocart').css('backgroundColor',HEX);
                break;             
                case 'addtocart_border':
                    jQuery('.wpsc-addtocart').css('borderColor',HEX);
                break;         
                case 'addtocart_text':
                    jQuery('.wpsc-addtocart').css('color',HEX);
                break;                                     
                case 'moreinfo_button':
                    jQuery('.wpsc-product-info').css('backgroundColor',HEX);
                break;             
                case 'moreinfo_border':
                    jQuery('.wpsc-product-info').css('borderColor',HEX);
                break;                                     
                case 'moreinfo_text':
                    jQuery('.wpsc-product-info').css('color',HEX);
                break;                                          
            }                                 
        }

        function changeColor(R,G,B){
            var HEX=wpscRGBtoHEX([R,G,B]);
            wpscChangeColor(HEX);
            jQuery("#colorValueHex").val(HEX);
            jQuery('.miniColors-trigger').css('backgroundColor',HEX);


        }
        /************************************************************************************************************/                            

        jQuery("#colorValueHex").miniColors({
            change: function(hex, rgb) {
                jQuery("#R").mbsetVal(rgb.r);
                jQuery("#G").mbsetVal(rgb.g);
                jQuery("#B").mbsetVal(rgb.b);
                wpscChangeColor(hex);
                jQuery("#colorValueHex").val(hex);                                    
            }
        });

        jQuery("#wpsc-window-closer").button().click(function(){
            jQuery("#wpsc-tabdlg").tabs().dialog("close");
        });

        jQuery("#wpsc-tabdlg").tabs().dialog({
            autoOpen: true,
            width: 465,
            height: 555,
            show: "blind",
            hide: "explode",   
            resizable: false,
            draggable: false, // disable the dialogs default drag we will be using the tabs titlebar instead
            modal: false,
            dialogClass: 'wpsc-designer-window',
            select: function() {
                wpscRefreshAllControls();
            },
            show: function() {
                wpscRefreshAllControls();
            },            
            open: function() {
                jQuery('.ui-dialog-titlebar').hide(); // hide the default dialog titlebar
                jQuery('.wpsc-single-product').css({'cursor':'move'});
                
                wpscRefreshAllControls();
                
              
            },
            close: function() {
                jQuery('.ui-dialog-titlebar').show(); // in case you have other ui-dialogs on the page, show the titlebar
                jQuery('.wpsc-single-product').css({'cursor':'auto'});
            }
        }).parent().draggable({handle: ".ui-tabs-nav"}); // the ui-tabs element (#wpsc-tabdlg) is the object parent, add these allows the tab to drag the dialog around with it


        jQuery("#wpsc-tabopener").click(function(){           
            jQuery("#wpsc-tabdlg").tabs().dialog("open");           
        });             
                
        
});