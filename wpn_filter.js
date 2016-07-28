jQuery(document).ready(function($){
	
	$('select#wpn_filter').on('change', function(e){		
		window.location.href='product/' + $(this).val();		
	});
	
});
