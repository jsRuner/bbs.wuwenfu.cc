/*模板巴士版权所有*/
jQuery(function (){        
	setInterval(function () {
		jQuery('#bus_textroll dl:last').hide().insertBefore(jQuery("#bus_textroll dl:first")).slideDown(1000);
	  }, 6000);
});

