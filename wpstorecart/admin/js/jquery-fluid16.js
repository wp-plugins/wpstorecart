var fluid = {
Ajax : function(){
	jQuery("#loading").hide();
	var content = jQuery("#ajax-content").hide();
	jQuery("#toggle-ajax").bind("click", function(e) {
        if ( jQuery(this).is(".hidden") ) {
            jQuery("#ajax-content").empty();

            jQuery("#loading").show();
            jQuery("#ajax-content").load("/fluid960gs/data/ajax-response.html", function() {
            	jQuery("#loading").hide();
            	content.slideDown();
            });
        }
        else {
            content.slideUp();
        }
        if (jQuery(this).hasClass('hidden')){
            jQuery(this).removeClass('hidden').addClass('visible');
        }
        else {
            jQuery(this).removeClass('visible').addClass('hidden');
        }
        e.preventDefault();
    });
},
Toggle : function(){
	var default_hide = {"grid": true, "navigator": true };
	jQuery.each(
		["grid", "paragraphs", "blockquote", "list-items", "list", "section-menu", "tables", "forms", "login-forms", "search", "articles", "articlez", "accordion","navigator"],
		function() {
			var el = jQuery("#" + (this == 'accordon' ? 'accordion-block' : this) );
			if (default_hide[this]) {
				el.hide();
				jQuery("[id='toggle-"+this+"']").addClass("hidden")
			}
			jQuery("[id='toggle-"+this+"']")
			.bind("click", function(e) {
				if (jQuery(this).hasClass('hidden')){
					jQuery(this).removeClass('hidden').addClass('visible');
					el.slideDown();
				} else {
					jQuery(this).removeClass('visible').addClass('hidden');
					el.slideUp();
				}
				e.preventDefault();
			});
		}
	);
},
SectionMenu : function(){
	jQuery("#section-menu")
        .accordion({
            "header": "a.menuitem"
        })
        .bind("accordionchangestart", function(e, data) {
            data.newHeader.next().andSelf().addClass("current");
            data.oldHeader.next().andSelf().removeClass("current");
        })
        .find("a.menuitem:first").addClass("current")
        .next().addClass("current");
},
Accordion: function(){
	jQuery("#accordion").accordion({
        'header': "h3.atStart"
    }).bind("accordionchangestart", function(e, data) {
        data.newHeader.css({
            "font-weight": "bold",
            "background": "#fff"
        });

        data.oldHeader.css({
            "font-weight": "normal",
            "background": "#eee"
        });
    }).find("h3.atStart:first").css({
        "font-weight": "bold",
        "background": "#fff"
    });
}
}
jQuery(function ($) {
	if(jQuery("#accordion").length){fluid.Accordion();}
	if(jQuery("[id$='ajax']").length){fluid.Ajax();}
	if(jQuery("[id^='toggle']").length){fluid.Toggle();}
	if(jQuery("#section-menu").length){fluid.SectionMenu();}
});