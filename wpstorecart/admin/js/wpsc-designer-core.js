// wpStoreCart Designer Core
// Copyright (c) 2012 wpStoreCart, LLC.  
// Part of the wpStoreCart project, licensed under the LGPL license.

//Function to get hex from rgb colour
function wpscRGBtoHEX(rgb) {
    var hex= function(x) {
        var hexDigits = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F");
        return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
    };
    return "#" + hex(rgb[0]) + hex(rgb[1]) + hex(rgb[2]);
}

// Convert a CSS color to HEX
function wpscCssColorToHEX(colorStr){
    var hex = '#';
    jQuery.each(colorStr.substring(4).split(','), function(i, str){
        var h = (jQuery.trim(str.replace(')',''))*1).toString(16);
        hex += (h.length == 1) ? "0" + h : h;
    });
    return hex;
}


//Function to get rgb from hex colour
function wpscHEXtoRGB(v){
    var val=(v.charAt(0)=="#") ? v.substring(1,7):"ffffff";
    var R=parseInt(val.substring(0,2),16);
    var G=parseInt(val.substring(2,4),16);
    var B=parseInt(val.substring(4,6),16);
    return [R,G,B];
}

function wpscTryConvertColorToHex(RGB) {
    if(RGB!='transparent' || RGB!='inherit') {
        try {
            return wpscCssColorToHEX(RGB);
        } catch(err) {
            return RGB;
        }
    } else {
        return RGB;
    }
}

function wpscListCSSAttributes(velement) {
        var output = ' ';
        output += velement + ' {';
        try {if(jQuery(velement).css('backgroundAttachment').length > 0) {output += 'background-attachment:'+jQuery(velement).css('backgroundAttachment')+';';} } catch(err) {}
        try {if(jQuery(velement).css('backgroundColor').length > 0 && wpscTryConvertColorToHex(jQuery(velement).css('backgroundColor'))!='#NaN') {output += 'background-color:'+wpscTryConvertColorToHex(jQuery(velement).css('backgroundColor'))+';';} } catch(err) {}
        try {if(jQuery(velement).css('backgroundImage').length > 0) {output += 'background-image:'+jQuery(velement).css('backgroundImage')+';';} } catch(err) {}
        try {if(jQuery(velement).css('backgroundPosition').length > 0) {output += 'background-position:'+jQuery(velement).css('backgroundPosition')+';';} } catch(err) {}
        try {if(jQuery(velement).css('backgroundRepeat').length > 0) {output += 'background-repeat:'+jQuery(velement).css('backgroundRepeat')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderBottom').length > 0 && jQuery(velement).css('borderBottom')!='0px' && jQuery(velement).css('borderBottom')!='0') {output += 'border-bottom:'+jQuery(velement).css('borderBottom')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderBottomColor').length > 0 && wpscTryConvertColorToHex(jQuery(velement).css('borderBottomColor'))!='#NaN' && jQuery(velement).css('borderBottom')!='0px' && jQuery(velement).css('borderBottom')!='0') {output += 'border-bottom-color:'+wpscTryConvertColorToHex(jQuery(velement).css('borderBottomColor'))+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderBottomStyle').length > 0 && jQuery(velement).css('borderBottom')!='0px' && jQuery(velement).css('borderBottom')!='0') {output += 'border-bottom-style:'+jQuery(velement).css('borderBottomStyle')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderBottomWidth').length > 0 && jQuery(velement).css('borderBottom')!='0px' && jQuery(velement).css('borderBottom')!='0') {output += 'border-bottom-width:'+jQuery(velement).css('borderBottomWidth')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderLeft').length > 0 && jQuery(velement).css('borderLeft')!='0px' && jQuery(velement).css('borderLeft')!='0') {output += 'border-left:'+jQuery(velement).css('borderLeft')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderLeftColor').length > 0 && wpscTryConvertColorToHex(jQuery(velement).css('borderLeftColor'))!='#NaN' && jQuery(velement).css('borderLeft')!='0px' && jQuery(velement).css('borderLeft')!='0') {output += 'border-left-color:'+wpscTryConvertColorToHex(jQuery(velement).css('borderLeftColor'))+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderLeftStyle').length > 0 && jQuery(velement).css('borderLeft')!='0px' && jQuery(velement).css('borderLeft')!='0') {output += 'border-left-style:'+jQuery(velement).css('borderLeftStyle')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderLeftWidth').length > 0 && jQuery(velement).css('borderLeft')!='0px' && jQuery(velement).css('borderLeft')!='0') {output += 'border-left-width:'+jQuery(velement).css('borderLeftWidth')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderRadius').length > 0 && jQuery(velement).css('borderRadius')!='0px') {output += 'border-radius:'+jQuery(velement).css('borderRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderRight').length > 0 && jQuery(velement).css('borderRight')!='0px' && jQuery(velement).css('borderRight')!='0') {output += 'border-right:'+jQuery(velement).css('borderRight')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderRightColor').length > 0 && wpscTryConvertColorToHex(jQuery(velement).css('borderRightColor'))!='#NaN' && jQuery(velement).css('borderRight')!='0px' && jQuery(velement).css('borderRight')!='0') {output += 'border-right-color:'+wpscTryConvertColorToHex(jQuery(velement).css('borderRightColor'))+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderRightStyle').length > 0 && jQuery(velement).css('borderRight')!='0px' && jQuery(velement).css('borderRight')!='0') {output += 'border-right-style:'+jQuery(velement).css('borderRightStyle')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderRightWidth').length > 0 && jQuery(velement).css('borderRight')!='0px' && jQuery(velement).css('borderRight')!='0') {output += 'border-right-width:'+jQuery(velement).css('borderRightWidth')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderTop').length > 0 && jQuery(velement).css('borderTop')!='0px'  && jQuery(velement).css('borderTop')!='0') {output += 'border-top:'+jQuery(velement).css('borderTop')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderTopColor').length > 0 && wpscTryConvertColorToHex(jQuery(velement).css('borderTopColor'))!='#NaN' && jQuery(velement).css('borderTop')!='0px' && jQuery(velement).css('borderTop')!='0') {output += 'border-top-color:'+wpscTryConvertColorToHex(jQuery(velement).css('borderTopColor'))+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderTopStyle').length > 0 && jQuery(velement).css('borderTop')!='0px' && jQuery(velement).css('borderTop')!='0') {output += 'border-top-style:'+jQuery(velement).css('borderTopStyle')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderTopWidth').length > 0 && jQuery(velement).css('borderTop')!='0px' && jQuery(velement).css('borderTop')!='0') {output += 'border-top-width:'+jQuery(velement).css('borderTopWidth')+';';} } catch(err) {}
        try {if(jQuery(velement).css('clear').length > 0) {output += 'clear:'+jQuery(velement).css('clear')+';';} } catch(err) {}
        try {if(jQuery(velement).css('color').length > 0 && wpscTryConvertColorToHex(jQuery(velement).css('color'))!='#NaN') {output += 'color:'+wpscTryConvertColorToHex(jQuery(velement).css('color'))+';';} } catch(err) {}
        try {if(jQuery(velement).css('display').length > 0) {output += 'display:'+jQuery(velement).css('display')+';';} } catch(err) {}
        try {if(jQuery(velement).css('filter').length > 0 && jQuery(velement).css('filter')!='none') {output += 'filter:'+jQuery(velement).css('filter')+';';} } catch(err) {}
        try {if(jQuery(velement).css('fontSize').length > 0) {output += 'font-size:'+jQuery(velement).css('fontSize')+';';} } catch(err) {}
        try {if(jQuery(velement).css('fontVariant').length > 0) {output += 'font-variant:'+jQuery(velement).css('fontVariant')+';';} } catch(err) {}
        try {if(jQuery(velement).css('fontWeight').length > 0) {output += 'font-weight:'+jQuery(velement).css('fontWeight')+';';} } catch(err) {}
        if(velement=='.wpsc-products') {
            try {if(jQuery(velement).css('height').length > 0) {output += 'height:'+jQuery(velement).css('height')+';';} } catch(err) {}
        }
        try {if(jQuery(velement).css('left').length > 0) {output += 'left:'+jQuery(velement).css('left')+';';} } catch(err) {}
        try {if(jQuery(velement).css('letterSpacing').length > 0) {output += 'letter-spacing:'+jQuery(velement).css('letterSpacing')+';';} } catch(err) {}
        try {if(jQuery(velement).css('lineHeight').length > 0) {output += 'line-height:'+jQuery(velement).css('lineHeight')+';';} } catch(err) {}
        try {if(jQuery(velement).css('listStyle').length > 0) {output += 'list-style:'+jQuery(velement).css('listStyle')+';';} } catch(err) {}
        try {if(jQuery(velement).css('listStyleImage').length > 0) {output += 'list-style-image:'+jQuery(velement).css('listStyleImage')+';';} } catch(err) {}
        try {if(jQuery(velement).css('listStylePosition').length > 0) {output += 'list-style-position:'+jQuery(velement).css('listStylePosition')+';';} } catch(err) {}
        try {if(jQuery(velement).css('listStyleType').length > 0) {output += 'list-style-type:'+jQuery(velement).css('listStyleType')+';';} } catch(err) {}
        try {if(jQuery(velement).css('marginBottom').length > 0 && jQuery(velement).css('marginBottom')!='0px') {output += 'margin-bottom:'+jQuery(velement).css('marginBottom')+';';} } catch(err) {}
        try {if(jQuery(velement).css('marginLeft').length > 0 && jQuery(velement).css('marginLeft')!='0px') {output += 'margin-left:'+jQuery(velement).css('marginLeft')+';';} } catch(err) {}
        try {if(jQuery(velement).css('marginRight').length > 0 && jQuery(velement).css('marginRight')!='0px') {output += 'margin-right:'+jQuery(velement).css('marginRight')+';';} } catch(err) {}
        try {if(jQuery(velement).css('marginTop').length > 0 && jQuery(velement).css('marginTop')!='0px') {output += 'margin-top:'+jQuery(velement).css('marginTop')+';';} } catch(err) {}
        try {if(jQuery(velement).css('overflow').length > 0) {output += 'overflow:'+jQuery(velement).css('overflow')+';';} } catch(err) {}
        try {if(jQuery(velement).css('paddingBottom').length > 0) {output += 'padding-bottom:'+jQuery(velement).css('paddingBottom')+';';} } catch(err) {}
        try {if(jQuery(velement).css('paddingLeft').length > 0) {output += 'padding-left:'+jQuery(velement).css('paddingLeft')+';';} } catch(err) {}
        try {if(jQuery(velement).css('paddingRight').length > 0) {output += 'padding-right:'+jQuery(velement).css('paddingRight')+';';} } catch(err) {}
        try {if(jQuery(velement).css('paddingTop').length > 0) {output += 'padding-top:'+jQuery(velement).css('paddingTop')+';';} } catch(err) {}
        try {if(jQuery(velement).css('position').length > 0) {output += 'position:'+jQuery(velement).css('position')+';';} } catch(err) {}
        try {if(jQuery(velement).css('styleFloat').length > 0) {output += 'float:'+jQuery(velement).css('styleFloat')+';';} } catch(err) {}
        try {if(jQuery(velement).css('textAlign').length > 0) {output += 'text-align:'+jQuery(velement).css('textAlign')+';';} } catch(err) {}
        try {if(jQuery(velement).css('textDecoration').length > 0) {output += 'text-decoration:'+jQuery(velement).css('textDecoration')+';';} } catch(err) {}
        try {if(jQuery(velement).css('textIndent').length > 0 && jQuery(velement).css('textIndent')!='0px') {output += 'text-indent:'+jQuery(velement).css('textIndent')+';';} } catch(err) {}
        try {if(jQuery(velement).css('textTransform').length > 0) {output += 'text-transform:'+jQuery(velement).css('textTransform')+';';} } catch(err) {}
        try {if(jQuery(velement).css('top').length > 0) {output += 'top:'+jQuery(velement).css('top')+';';} } catch(err) {}
        try {if(jQuery(velement).css('verticalAlign').length > 0) {output += 'vertical-align:'+jQuery(velement).css('verticalAlign')+';';} } catch(err) {}
        try {if(jQuery(velement).css('visibility').length > 0) {output += 'visibility:'+jQuery(velement).css('visibility')+';';} } catch(err) {}
        if(velement!='.wpsc-addtocart' && velement!='.wpsc-moreinfo') {
            try {if(jQuery(velement).css('width').length > 0) {output += 'width:'+jQuery(velement).css('width')+';';} } catch(err) {}
        }
        try {if(jQuery(velement).css('borderTopLeftRadius').length > 0 && jQuery(velement).css('borderTopLeftRadius')!='0px') {output += 'border-top-left-radius:'+jQuery(velement).css('borderTopLeftRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderTopRightRadius').length > 0 && jQuery(velement).css('borderTopRightRadius')!='0px') {output += 'border-top-right-radius:'+jQuery(velement).css('borderTopRightRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderBottomLeftRadius').length > 0 && jQuery(velement).css('borderBottomLeftRadius')!='0px') {output += 'border-bottom-left-radius:'+jQuery(velement).css('borderBottomLeftRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('borderBottomRightRadius').length > 0 && jQuery(velement).css('borderBottomRightRadius')!='0px') {output += 'border-bottom-right-radius:'+jQuery(velement).css('borderBottomRightRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('WebkitBorderTopLeftRadius').length > 0 && jQuery(velement).css('WebkitBorderTopLeftRadius')!='0px') {output += '-webkit-border-top-left-radius:'+jQuery(velement).css('WebkitBorderTopLeftRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('WebkitBorderTopRightRadius').length > 0 && jQuery(velement).css('WebkitBorderTopRightRadius')!='0px') {output += '-webkit-border-top-right-radius:'+jQuery(velement).css('WebkitBorderTopRightRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('WebkitBorderBottomLeftRadius').length > 0 && jQuery(velement).css('WebkitBorderBottomLeftRadius')!='0px') {output += '-webkit-border-bottom-left-radius:'+jQuery(velement).css('WebkitBorderBottomLeftRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('WebkitBorderBottomRightRadius').length > 0 && jQuery(velement).css('WebkitBorderBottomRightRadius')!='0px') {output += '-webkit-border-bottom-right-radius:'+jQuery(velement).css('WebkitBorderBottomRightRadius')+';';} } catch(err) {}
        try {if(jQuery(velement).css('MozBorderRadius').length > 0 && jQuery(velement).css('borderTopLeftRadius')!='0px') {output += '-moz-border-radius:'+jQuery(velement).css('MozBorderRadius')+';';} } catch(err) {}        
        output += "} \n\ \n\ ";
        return output;    
}

function wpscListTextCSSAttributes(velement) {
        var output = ' ';
        output += velement + ' {';
        try {if(jQuery(velement).css('color').length > 0 && wpscTryConvertColorToHex(jQuery(velement).css('color'))!='#NaN') {output += 'color:'+wpscTryConvertColorToHex(jQuery(velement).css('color'))+';';} } catch(err) {}
        try {if(jQuery(velement).css('display').length > 0) {output += 'display:'+jQuery(velement).css('display')+';';} } catch(err) {}
        try {if(jQuery(velement).css('fontSize').length > 0) {output += 'font-size:'+jQuery(velement).css('fontSize')+';';} } catch(err) {}
        try {if(jQuery(velement).css('fontVariant').length > 0) {output += 'font-variant:'+jQuery(velement).css('fontVariant')+';';} } catch(err) {}
        try {if(jQuery(velement).css('fontWeight').length > 0) {output += 'font-weight:'+jQuery(velement).css('fontWeight')+';';} } catch(err) {} 
        try {if(jQuery(velement).css('left').length > 0) {output += 'left:'+jQuery(velement).css('left')+';';} } catch(err) {}
        try {if(jQuery(velement).css('letterSpacing').length > 0) {output += 'letter-spacing:'+jQuery(velement).css('letterSpacing')+';';} } catch(err) {}       
        try {if(jQuery(velement).css('lineHeight').length > 0) {output += 'line-height:'+jQuery(velement).css('lineHeight')+';';} } catch(err) {}
        try {if(jQuery(velement).css('marginBottom').length > 0 && jQuery(velement).css('marginBottom')!='0px') {output += 'margin-bottom:'+jQuery(velement).css('marginBottom')+';';} } catch(err) {}
        try {if(jQuery(velement).css('marginLeft').length > 0 && jQuery(velement).css('marginLeft')!='0px') {output += 'margin-left:'+jQuery(velement).css('marginLeft')+';';} } catch(err) {}
        try {if(jQuery(velement).css('marginRight').length > 0 && jQuery(velement).css('marginRight')!='0px') {output += 'margin-right:'+jQuery(velement).css('marginRight')+';';} } catch(err) {}
        try {if(jQuery(velement).css('marginTop').length > 0 && jQuery(velement).css('marginTop')!='0px') {output += 'margin-top:'+jQuery(velement).css('marginTop')+';';} } catch(err) {}
        try {if(jQuery(velement).css('overflow').length > 0) {output += 'overflow:'+jQuery(velement).css('overflow')+';';} } catch(err) {}
        try {if(jQuery(velement).css('paddingBottom').length > 0) {output += 'padding-bottom:'+jQuery(velement).css('paddingBottom')+';';} } catch(err) {}
        try {if(jQuery(velement).css('paddingLeft').length > 0) {output += 'padding-left:'+jQuery(velement).css('paddingLeft')+';';} } catch(err) {}
        try {if(jQuery(velement).css('paddingRight').length > 0) {output += 'padding-right:'+jQuery(velement).css('paddingRight')+';';} } catch(err) {}
        try {if(jQuery(velement).css('paddingTop').length > 0) {output += 'padding-top:'+jQuery(velement).css('paddingTop')+';';} } catch(err) {}
        try {if(jQuery(velement).css('position').length > 0) {output += 'position:'+jQuery(velement).css('position')+';';} } catch(err) {}        
        try {if(jQuery(velement).css('styleFloat').length > 0) {output += 'float:'+jQuery(velement).css('styleFloat')+';';} } catch(err) {}
        try {if(jQuery(velement).css('textAlign').length > 0) {output += 'text-align:'+jQuery(velement).css('textAlign')+';';} } catch(err) {}
        try {if(jQuery(velement).css('textDecoration').length > 0) {output += 'text-decoration:'+jQuery(velement).css('textDecoration')+';';} } catch(err) {}
        try {if(jQuery(velement).css('textIndent').length > 0 && jQuery(velement).css('textIndent')!='0px') {output += 'text-indent:'+jQuery(velement).css('textIndent')+';';} } catch(err) {}
        try {if(jQuery(velement).css('textTransform').length > 0) {output += 'text-transform:'+jQuery(velement).css('textTransform')+';';} } catch(err) {} 
        try {if(jQuery(velement).css('top').length > 0) {output += 'top:'+jQuery(velement).css('top')+';';} } catch(err) {}
        try {if(jQuery(velement).css('verticalAlign').length > 0) {output += 'vertical-align:'+jQuery(velement).css('verticalAlign')+';';} } catch(err) {}
        try {if(jQuery(velement).css('visibility').length > 0) {output += 'visibility:'+jQuery(velement).css('visibility')+';';} } catch(err) {}        
        output += "} \n\ \n\ ";
        return output;    
}


function wpscAnimateRoundedBordersOn(velement, vsize) {
    jQuery(velement)
    .css({
        borderTopLeftRadius: 0, 
        borderTopRightRadius: 0, 
        borderBottomLeftRadius: 0, 
        borderBottomRightRadius: 0,
        WebkitBorderTopLeftRadius: 0, 
        WebkitBorderTopRightRadius: 0, 
        WebkitBorderBottomLeftRadius: 0, 
        WebkitBorderBottomRightRadius: 0, 
        MozBorderRadius: 0 
    })
    .animate({
        borderTopLeftRadius: vsize, 
        borderTopRightRadius: vsize, 
        borderBottomLeftRadius: vsize, 
        borderBottomRightRadius: vsize,
        WebkitBorderTopLeftRadius: vsize, 
        WebkitBorderTopRightRadius: vsize, 
        WebkitBorderBottomLeftRadius: vsize, 
        WebkitBorderBottomRightRadius: vsize, 
        MozBorderRadius: vsize 
    }, 600);     
}


function wpscAnimateRoundedBordersOff(velement, vsize) {
    jQuery(velement)
    .css({
        borderTopLeftRadius: vsize, 
        borderTopRightRadius: vsize, 
        borderBottomLeftRadius: vsize, 
        borderBottomRightRadius: vsize,
        WebkitBorderTopLeftRadius: vsize, 
        WebkitBorderTopRightRadius: vsize, 
        WebkitBorderBottomLeftRadius: vsize, 
        WebkitBorderBottomRightRadius: vsize, 
        MozBorderRadius: vsize 
    })
    .animate({
        borderTopLeftRadius: 0, 
        borderTopRightRadius: 0, 
        borderBottomLeftRadius: 0, 
        borderBottomRightRadius: 0,
        WebkitBorderTopLeftRadius: 0, 
        WebkitBorderTopRightRadius: 0, 
        WebkitBorderBottomLeftRadius: 0, 
        WebkitBorderBottomRightRadius: 0, 
        MozBorderRadius: 0 
    }, 600);     
}

jQuery(".wpsc-addtocart").click(function(event){    
    event.preventDefault();
    return false;           
}); 