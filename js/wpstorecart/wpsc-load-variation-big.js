


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
            jQuery(".wpstorecart-item-id").val(XreturnedData.primkey);
            jQuery(".wpstorecart-item-primkey").val(XreturnedData.primkey);

            //case "name":
            if(XreturnedData.name != wpscParentName) {
                jQuery(".wpsc-list-item-name").html(wpscParentName + ' - ' + XreturnedData.name);
                jQuery(".wpstorecart-item-name").val(wpscParentName + ' - ' + XreturnedData.name);
            } else {
                jQuery(".wpsc-list-item-name").html(wpscParentName);
                jQuery(".wpstorecart-item-name").val(wpscParentName);                
            }
            
            //case "introdescription":
            jQuery(".wpsc-single-intro").html(XreturnedData.introdescription);

            //case "description":
            jQuery(".wpsc-single-description").html(XreturnedData.description);

            //case "thumbnail":
            jQuery(".wpstorecart-item-img").val(XreturnedData.thumbnail);
            jQuery(".wpsc-product-img").attr("src", XreturnedData.thumbnail);

            //case "price":
            wpscStoreRegularPrice = XreturnedData.price;

            //case "shipping":
            jQuery(".wpstorecart-item-shipping").val(XreturnedData.shipping);

            //case "inventory":
            wpscStoreInventory = XreturnedData.inventory;

            //case "discountprice":
            wpscStoreDiscountPrice = XreturnedData.discountprice;

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
                jQuery(".wpsc-price").html(wpscCurrencySymbol + wpscStoreRegularPrice + wpscCurrencySymbolRight);
                jQuery(".wpsc-oldprice").hide();
                
            } else if (wpscStoreDiscountPrice > 0 && (wpscStoreDiscountPrice < wpscStoreRegularPrice)) {
                jQuery(".wpsc-oldprice").show();
                jQuery(".wpstorecart-item-price").val(wpscStoreDiscountPrice);
                jQuery(".wpsc-price").html(wpscCurrencySymbol + wpscStoreDiscountPrice + wpscCurrencySymbolRight);
                jQuery(".wpsc-oldprice").html('<strike>'+wpscCurrencySymbol + wpscStoreRegularPrice + wpscCurrencySymbolRight+'</strike>');
            }
            
        }
    });
}

function wpscLoadProductAttributes() {
    
}