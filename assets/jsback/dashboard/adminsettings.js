(function($){
	$(document).on('click','.card-header .nav-link',function(){
		$('.btn-new-post').attr('href',$(this).data('link'));
	});
})(jQuery);