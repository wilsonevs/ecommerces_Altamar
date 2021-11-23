$(document).ready(function(){

  // $("#slider").owlCarousel({
  //   singleItem : true,
  //   loop:true,
  //   autoPlay: 6000,
  //     autoplayTimeout:6000,
  //     autoplayHoverPause:true,
  //   transitionStyle : "fade"
  //
  // });

	var imgLeft = window.site_url+"/public/paginas/img/flecha-izquierda.png";
	var imgRight = window.site_url+"/public/paginas/img/flecha-derecha.png";


	$("#imagen-amp").owlCarousel({

		singleItem: true,
		loop: true,
		autoPlay: false,
		autoplayTimeout:5000,
		autoplayHoverPause:false,
		pagination : false,
		navigation: true,
		navigationText: ['<img src="'+imgLeft+'"/>', '<img src="'+imgRight+'"/>']

	});
	var owl = $("#imagen-amp").data('owlCarousel');

	$( "#thumbs" ).delegate( "img", "click", function() {
		var img = $(this).attr("id");
		owl.goTo(img);
	});

  $( "#thumbs-small" ).delegate( "img", "click", function() {
		var img = $(this).attr("id");
		owl.goTo(img);
	});

});
