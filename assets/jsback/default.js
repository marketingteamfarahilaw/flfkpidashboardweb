(function($){
  'use strict';

  includeHTML();
  scrolled();   

  $( window ).on('scroll',function(){
     scrolled();   
  });

  $( window ).on('load',function(){
    $('.main-preloader').remove();
  });

  function scrolled(){
    var scroll_top = $( window ).scrollTop();
      if ( scroll_top > 0 ){
        $('.header').css('box-shadow','0 0 14px -3px');
        $('.header').css('background','rgba(255,255,255,1)');
      }else{
        $('.header').css('box-shadow','0 0 0 0');
        $('.header').css('background','rgba(255,255,255,0.85)');
      }
  }

  $("#mobile-menu").mmenu({
    navbar: {
      title: 'MENU'
    }
  });

  var API = $("#mobile-menu").data( "mmenu" );

  $( window ).resize(function() {
   API.close();
 });

$('.owl-carousel').owlCarousel({
    stagePadding: 30,
    loop:true,
    margin:20,
    nav:true,
    navText: ["<img src='./images/icons/arrow_left.png'>","<img src='./images/icons/arrow_right.png'>"],
    responsive:{
        0:{
            items:1
        },
        768:{
            items:2
        },
        1000:{
            center:true,
            items:3
        }
    }
})

function includeHTML() {
  var z, i, elmnt, file, xhttp;
  /*loop through a collection of all HTML elements:*/
  z = document.getElementsByTagName("*");
  for (i = 0; i < z.length; i++) {
    elmnt = z[i];
    /*search for elements with a certain atrribute:*/
    file = elmnt.getAttribute("w3-include-html");
    if (file) {
      /*make an HTTP request using the attribute value as the file name:*/
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
          if (this.status == 200) {elmnt.innerHTML = this.responseText;}
          if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
          /*remove the attribute, and call this function once more:*/
          elmnt.removeAttribute("w3-include-html");
          includeHTML();
        }
      }
      xhttp.open("GET", file, true);
      xhttp.send();
      /*exit the function:*/
      return;
    }
  }
}

 
})(jQuery);