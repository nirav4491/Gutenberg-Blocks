/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	var container, button, menu, links, i, len;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	button = container.getElementsByTagName( 'button' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );
	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

	// Get all the link elements within the menu.
	links    = menu.getElementsByTagName( 'a' );

	// Each time a menu link is focused or blurred, toggle focus.
	for ( i = 0, len = links.length; i < len; i++ ) {
		links[i].addEventListener( 'focus', toggleFocus, true );
		links[i].addEventListener( 'blur', toggleFocus, true );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		var self = this;

		// Move up through the ancestors of the current link until we hit .nav-menu.
		while ( -1 === self.className.indexOf( 'nav-menu' ) ) {

			// On li elements toggle the class .focus.
			if ( 'li' === self.tagName.toLowerCase() ) {
				if ( -1 !== self.className.indexOf( 'focus' ) ) {
					self.className = self.className.replace( ' focus', '' );
				} else {
					self.className += ' focus';
				}
			}

			self = self.parentElement;
		}
	}

	/**
	 * Toggles `focus` class to allow submenu access on tablets.
	 */
	( function( container ) {
		var touchStartFn, i,
			parentLink = container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

		if ( 'ontouchstart' in window ) {
			touchStartFn = function( e ) {
				var menuItem = this.parentNode, i;

				if ( ! menuItem.classList.contains( 'focus' ) ) {
					e.preventDefault();
					for ( i = 0; i < menuItem.parentNode.children.length; ++i ) {
						if ( menuItem === menuItem.parentNode.children[i] ) {
							continue;
						}
						menuItem.parentNode.children[i].classList.remove( 'focus' );
					}
					menuItem.classList.add( 'focus' );
				} else {
					menuItem.classList.remove( 'focus' );
				}
			};

			for ( i = 0; i < parentLink.length; ++i ) {
				parentLink[i].addEventListener( 'touchstart', touchStartFn, false );
			}
		}
	}( container ) );
} )();


var $ = jQuery;

// Make external links open in a new window.
$("a[rel*=external]").click(function () {
    window.open(this.href);
    return false;
});

// Show/hide author profiles.
$('#authortoggle').click(function () {
    $.smoothScroll({
        scrollTarget: '#js-authorProfile'
    });
    return false;
});

// Fade in social sharing.
$('#socialfade, #socialftfade, .socialfade')
    .hide()
    .delay(1500)
    .fadeIn('slow');
    
// RANDOM HAMBERGER ANIMATION
// REMOVE ANIMATION CLASS IN HTML AND UNCOMMENT BELOW
// var classes = ['hamburger--3dx','hamburger--3dy','hamburger--arrowalt','hamburger--boring','hamburger--collapse','hamburger--elastic','hamburger--emphatic','hamburger--slider','hamburger--spin','hamburger--spring','hamburger--stand','hamburger--squeeze','hamburger--vortex']; //add as many classes as u want
// var randomnumber = Math.floor(Math.random()*classes.length);
// $('#js-navMegaMenu-openButton').addClass(classes[randomnumber]);

$('#js-navMegaMenu-openButton').click(function(event) {
    event.preventDefault();
    $(this).toggleClass("is-active"); // animate hamberger icon
    $("#site-navigation").toggleClass('navMegaMenu-open');
    // hide maincontent so cant see @ bottom
    $("#content").toggleClass('mainContent-hidden');
    // overflow hidden is required to make sure the full page height
    // isnt visible when in megamenu
    // the delay is required to preven the visible change to the sidebar
    // being visible before the animation has completed.
    if ($("#content").hasClass('mainContent-overflowHidden')) {
        $("#content").removeClass('mainContent-overflowHidden');
    } else {
        setTimeout(
          function()
          {
            $("#content").addClass('mainContent-overflowHidden');
            }, 300);
    }


}); 
// Slider target First Child
$('#gallery-carousel .carousel-inner .item:first-child').addClass("active");

// Navigation controls.
$(function() {
    "use strict";

    var $section_title = $(".primary-section-title");
    var $search_form = $("#searchfm");
    var $search_input = $("#searchinput");
    var $search_button = $("#searchbtn");



    function open_search() {
        $search_input.val("");
        $search_input.removeClass("searchstart");
        $('#js-tipoffCTA').addClass('fade');
        $('#js-gdpr').addClass('fade');
    }

    function close_search() {
        $search_input.addClass("searchstart");
        $('#js-tipoffCTA').removeClass('fade');
        $('#js-gdpr').removeClass('fade');
    }

    // Hide popout navigation things when clicking somewhere else.
    $(document).click(function(event) {
        var $target = $(event.target);

        var in_search = $target.parents(".largesearch").length === 1;

        // // May be useful if we add stuff to header
        // // and need to fade out responsively for search field focus.
        // if (!in_search) {
        //     // Bring the event thing back in when we click off our expanding
        //     // things.
        //     $section_title.removeClass("fade");
        // } else {
        //     // Otherwise fade that thing.
        //     $section_title.addClass("fade");
        // }

        if (!in_search) {
            // Close search when clicking away from it.
            close_search();

        } 
    });

    $search_input.focus(function(event) {
        if ($search_input.hasClass("searchstart")) {
            open_search();
        }
    });

    $search_button.click(function(event) {
        if ($search_input.hasClass('searchstart')) {
            // Focus the input to show it.
            $search_input.focus();
        } else {
            $search_form.submit();
        }
    });

    // MEGA SEARCH
    function open_mega_search() {
        $('#megasearchinput').val("");
        $('#megasearchinput').removeClass("searchstart");
    }

    $('#megasearchinput').focus(function(event) {
        if ($('#megasearchinput').hasClass("searchstart")) {
            open_mega_search();
        }
    });

    $('#megasearchbtn').click(function(event) {
        if ($('#megasearchinput').hasClass('searchstart')) {
            // Focus the input to show it.
            $('#megasearchinput').focus();
        } else {
            $('#megaSearchForm').submit();
        }
    });

 
});