


function wpscLoadProductVariation(wpscVarKey, wpscPluginsUrl, wpscParentKey, wpscParentName, wpscCurrencySymbol, wpscCurrencySymbolRight) {
    jQuery.ajax({        
        type: "POST",
        url: wpscPluginsUrl + "/wpstorecart/wpstorecart/products/loadvariation.php",
        data: { wpscVarKey : wpscVarKey },
        success: function(XreturnedData) {
            var wpscStoreRegularPrice = 0;
            var wpscStoreDiscountPrice = 0;
            var wpscStoreInventory = 0;
            var wpscStoreUseInventory = 0;
            
            //case "primkey":
            jQuery("#wpstorecart-item-id-" + wpscParentKey).val(XreturnedData.primkey);
            jQuery("#wpstorecart-item-primkey-" + wpscParentKey).val(XreturnedData.primkey);

            //case "name":
            if(XreturnedData.name != wpscParentName) {
                jQuery(".wpsc-list-item-name-" + wpscParentKey).html(wpscParentName + ' - ' + XreturnedData.name);
                jQuery("#wpstorecart-item-name-" + wpscParentKey).val(wpscParentName + ' - ' + XreturnedData.name);
            } else {
                jQuery(".wpsc-list-item-name-" + wpscParentKey).html(wpscParentName);
                jQuery("#wpstorecart-item-name-" + wpscParentKey).val(wpscParentName);                
            }
            
            //case "introdescription":
            jQuery(".wpsc-single-intro-" + wpscParentKey).html(XreturnedData.introdescription);

            //case "description":
            jQuery(".wpsc-single-description-" + wpscParentKey).html(XreturnedData.description);

            //case "thumbnail":
            jQuery("#wpstorecart-item-img-" + wpscParentKey).val(XreturnedData.thumbnail);
            jQuery(".wpsc-product-img-" + wpscParentKey).attr("src", XreturnedData.thumbnail);

            //case "price":
            wpscStoreRegularPrice = parseFloat(XreturnedData.price, 10);

            //case "shipping":
            jQuery("#wpstorecart-item-shipping-" + wpscParentKey).val(XreturnedData.shipping);

            //case "inventory":
            wpscStoreInventory = XreturnedData.inventory;

            //case "discountprice":
            wpscStoreDiscountPrice = parseFloat(XreturnedData.discountprice, 10);

            //case "useinventory":
            wpscStoreUseInventory = XreturnedData.useinventory;

            if(wpscStoreUseInventory==1) { // If using inventory
                if(wpscStoreInventory<1) {
                    jQuery('#wpsc-addtocart-primkey-' + wpscParentKey).hide();
                } else {
                    jQuery('#wpsc-addtocart-primkey-' + wpscParentKey).show();
                }
            } else {
                jQuery('#wpsc-addtocart-primkey-' + wpscParentKey).show();
            }
            
            if(wpscStoreDiscountPrice==0 || wpscStoreDiscountPrice=='0.00') {
                jQuery("#wpstorecart-item-price-" + wpscParentKey).val(wpscStoreRegularPrice);
                jQuery(".wpsc-price-" + wpscParentKey).html(wpscCurrencySymbol + (wpscStoreRegularPrice).toFixed(2)  + wpscCurrencySymbolRight);
                jQuery(".wpsc-oldprice-" + wpscParentKey).hide();
                
            } else if (wpscStoreDiscountPrice > 0 && (wpscStoreDiscountPrice < wpscStoreRegularPrice)) {
                jQuery(".wpsc-oldprice-" + wpscParentKey).show();
                jQuery("#wpstorecart-item-price-" + wpscParentKey).val(wpscStoreDiscountPrice);
                jQuery(".wpsc-price-" + wpscParentKey).html(wpscCurrencySymbol + (wpscStoreDiscountPrice).toFixed(2) + wpscCurrencySymbolRight);
                jQuery(".wpsc-oldprice-" + wpscParentKey).html('<strike>'+wpscCurrencySymbol + (wpscStoreRegularPrice).toFixed(2)  + wpscCurrencySymbolRight+'</strike>');
            }
            
        }
    });
}





function wpscLoadProductAttribute(wpscPluginsUrl, wpscParentKey, wpscParentName, wpscCurrencySymbol, wpscCurrencySymbolRight) {
    
    var wpscAttributeKey = '';
    jQuery('.wpsc-product-attribute-options :selected').each(function(i, selected){ 
        wpscAttributeKey = wpscAttributeKey + jQuery(selected).val(); 
    });    
    
    jQuery.ajax({        
        type: "POST",
        url: wpscPluginsUrl + "/wpstorecart/wpstorecart/products/loadattribute.php",
        data: { wpscAttributeKey : wpscAttributeKey },
        success: function(XreturnedData) {
            var wpscStoreRegularPrice = 0;
            var wpscStoreDiscountPrice = 0;
            var wpscStoreInventory = 0;
            var wpscStoreUseInventory = 0;
            
            //case "primkey":
            jQuery("#wpstorecart-item-id-" + wpscParentKey).val(XreturnedData.primkey);
            jQuery("#wpstorecart-item-primkey-" + wpscParentKey).val(XreturnedData.primkey);

            //case "name":
            if(XreturnedData.name != wpscParentName) {
                jQuery(".wpsc-list-item-name-" + wpscParentKey).html(wpscParentName + ' - ' + XreturnedData.name);
                jQuery("#wpstorecart-item-name-" + wpscParentKey).val(wpscParentName + ' - ' + XreturnedData.name);
            } else {
                jQuery(".wpsc-list-item-name-" + wpscParentKey).html(wpscParentName);
                jQuery("#wpstorecart-item-name-" + wpscParentKey).val(wpscParentName);                
            }
            
            //case "price":
            wpscStoreRegularPrice = parseFloat(XreturnedData.price, 10);

            //case "inventory":
            wpscStoreInventory = XreturnedData.inventory;

            //case "discountprice":
            wpscStoreDiscountPrice = parseFloat(XreturnedData.discountprice, 10);


            //case "useinventory":
            wpscStoreUseInventory = XreturnedData.useinventory;

            if(wpscStoreUseInventory==1) { // If using inventory
                if(wpscStoreInventory<1) {
                    jQuery('#wpsc-addtocart-primkey-' + wpscParentKey).hide();
                } else {
                    jQuery('#wpsc-addtocart-primkey-' + wpscParentKey).show();
                }
            } else {
                jQuery('#wpsc-addtocart-primkey-' + wpscParentKey).show();
            }
            
            if(wpscStoreDiscountPrice==0 || wpscStoreDiscountPrice=='0.00') {
                jQuery(".wpstorecart-item-price").val(wpscStoreRegularPrice);
                jQuery(".wpsc-price-" + wpscParentKey).html(wpscCurrencySymbol + (wpscStoreRegularPrice).toFixed(2) + wpscCurrencySymbolRight);
                jQuery(".wpsc-oldprice-" + wpscParentKey).hide();
                
                
            } 
            if (wpscStoreDiscountPrice > 0 && (wpscStoreDiscountPrice < wpscStoreRegularPrice)) {
                jQuery(".wpsc-oldprice-" + wpscParentKey).show();
                jQuery("#wpstorecart-item-price-" + wpscParentKey).val(wpscStoreDiscountPrice);
                jQuery(".wpsc-price-" + wpscParentKey).html(wpscCurrencySymbol + (wpscStoreDiscountPrice).toFixed(2) + wpscCurrencySymbolRight);
                jQuery(".wpsc-oldprice-" + wpscParentKey).html('<strike>'+wpscCurrencySymbol + (wpscStoreRegularPrice).toFixed(2) + wpscCurrencySymbolRight+'</strike>');
            }
            
        }
    });
}

