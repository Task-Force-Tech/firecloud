$(document).ready(function () {
	"use strict";


	//Scroll Spy
	$('body').scrollspy({ target: '#main-nav' });

	//Smooth Scrool
	// smoothScroll.init();


	//Scroll Spy
	$('body').scrollspy({ target: '#main-nav' });


	//Preloader
	$(window).load(function() { // makes sure the whole site is loaded
		$('#box,#hill').fadeOut(); // will first fade out the loading animation
		$('#loader,.preloader').delay(350).fadeOut('fast'); // will fade out the white DIV that covers the website.
		$('body').delay(350).css({'overflow':'visible'});
	})


	//WOW JS
	new WOW().init();

	// removing the navbar animations since they're problematic on mobile.
	$('.navbar-collapse.collapse').on('show.bs.collapse hide.bs.collapse', function(e) {
		e.preventDefault();
	});

	// TODO: Figure out what we actually want this to do now that we're going to be
	// including accordions.
	$('[data-toggle="collapse"]').on('click', function(e) {
		e.preventDefault();
		$($(this).data('target')).toggleClass('in');
	});

});