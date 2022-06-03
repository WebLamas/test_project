@@include("./lib/jquery.fancybox.min.js")
@@include("./lib/wpcf7.js")
@@include("./lib/jquery.viewportchecker.js");
@@include("./lib/slick.js");
/*end of inludes*/
	

$(document).ready(function(){
	$('.advantages').viewportChecker({
			classToAdd: 'visible',
			offset: 100
		});
$('.steps__item-wr').viewportChecker({
			classToAdd: 'visible',
			offset: 100
		});
$(".reviews").slick({
			centerMode: true,
			centerPadding: 0,
			infinite:true,
			slidesToShow: 3,
			slidesToScroll: 1,
			draggable:true,
			autoplay:true,
			autoplaySpeed: 1500,
			speed: 1000,
			cssEase: "ease",
			pauseOnHover:true,
			pauseOnFocus:true,
			responsive: [ 
			{breakpoint: 1279,settings: {arrows: false,slidesToShow: 3}},
			{breakpoint: 1023,settings: {centerMode: false,arrows: false,slidesToShow: 2}},
			{breakpoint: 639,settings: {centerMode: false,arrows: false,slidesToShow: 1}}
			]
	});$(".orgs").slick({
			centerMode: true,
			centerPadding: 0,
			infinite:true,
			slidesToShow: 5,
			slidesToScroll: 1,
			draggable:true,
			autoplay:true,
			autoplaySpeed: 1500,
			speed: 1000,
			cssEase: "ease",
			pauseOnHover:true,
			pauseOnFocus:true,
			responsive: [ 
			{breakpoint: 1279,settings: {arrows: false,slidesToShow: 3}},
			{breakpoint: 1023,settings: {centerMode: false,arrows: false,slidesToShow: 2}},
			{breakpoint: 639,settings: {centerMode: false,arrows: false,slidesToShow: 1}}
			]
	});
	$(window).on("wl_resize",function(event,ww){
		console.log("some_code");
	});

}); 