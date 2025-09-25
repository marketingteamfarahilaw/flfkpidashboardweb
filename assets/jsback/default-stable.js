(function($){
  'use strict';

  $("#mobile-menu").mmenu({
  		navbar: {
  		    title: 'Food Blog'
  		}
   });

	var API = $("#mobile-menu").data( "mmenu" );

  $( window ).resize(function() {
    	API.close();
	});

  $(document).on('click','.cube__face--left',function(){
    if( !$('.cube-scene > .cube').hasClass('show-left') ){
        
        //open right face card
        $('.cube-scene > .cube .cube__face--right').removeClass('right-close');


        if( $(this).hasClass('left-close') ){
          $(this).removeClass('left-close');
        }else{
          $(this).addClass('left-close');
        }
    }else{
        //rotate card to left
        $('.cube-scene > .cube').removeClass('show-left');

    }
  });

  $(document).on('click','.cube__face--right',function(){
    //open left face card
    $('.cube-scene > .cube .cube__face--left').removeClass('left-close');

    if( $('.cube-scene > .cube').hasClass('show-left') ){
        if( $(this).hasClass('right-close') ){
          $(this).removeClass('right-close');
        }else{
          $(this).addClass('right-close');
        }
    }else{
        //rotate card to right
        $('.cube-scene > .cube').addClass('show-left');
    }
  });

})(jQuery);