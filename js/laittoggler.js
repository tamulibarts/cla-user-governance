// Liberal Arts Information Technology
// Toggler Plugin
// GPL-2.0+
// Zachary Watkins, zwatkins2@tamu.edu
// Example markup: <a class="info-icon toggler" href="#more-info" data-target=".c-toggle__tooltip:d-hidden(1ms),.c-toggle__tooltip:hidden(0ms|500ms)">Link</a>
(function ($) {

	$.fn.laittoggler = function (options) {

		var opts = $.extend({
			index: 0,
			swing: false,
			dir: 'right'
		}, options);

		return this.each(function () {
			var $toggler = $(this);
			opts.targets = $toggler.attr("data-target").split(",");
			$toggler.data( 'laittoggler', opts );
			$toggler.on( "click", $.fn.laittoggler.toggle.bind($toggler) );
		});

	};

	$.fn.laittoggler.toggle = function (e) {
		console.clear();
		e.preventDefault();
		// Execute chain of class changes.
		var changeClass = $.fn.laittoggler.changeClass.bind(this);
		changeClass();

	};

	$.fn.laittoggler.changeClass = function(){
		console.log($(this).data('laittoggler'));
		var $elem = this,
			opts = $elem.data('laittoggler'),
			targets = opts.targets,
			item = targets[opts.index],
			pair = item.split(':'),
			tar = pair[0],
			tclass = pair.length > 1 ? pair[1].replace(/^i?\+?/,'') : "hidden",
			fchar = pair.length > 1 ? pair[1].charAt(0) : "hidden",
			timeout_pattern = new RegExp("\\([\\d.]\+m\?s\\)$"),
			timeout = tclass.match( timeout_pattern ),
			$target = jQuery(tar);
		// If the first character is + or - then the user is forcing the class to be added or removed.
		if ( timeout ) {
			timeout = timeout[0].slice(1,-1);
			var factor = 'm' === timeout.charAt( timeout.length - 2 ) ? 1 : 1000;
			timeout = parseFloat(timeout) * factor;
			tclass = tclass.replace( timeout_pattern, '' );
		}

		if ( fchar.match(/[a-zA-Z]/) === null && ( '+' === fchar || '-' === fchar ) ) {
			if ( '+' === fchar ) {
				// Add the class.
				tclass = tclass.slice(1);
				$target.addClass(tclass);
			} else if ( '-' === fchar ) {
				// Remove the class.
				tclass = tclass.slice(1);
				$target.removeClass(tclass);
			}
		} else {
			$target.toggleClass(tclass);
		}

		var proceed = false;
		if ( opts.swing && 'left' === opts.dir ) {
			if ( opts.index > 0 ) {
				proceed = true;
				opts.index -= 1;
			} else {
				opts.dir = 'right';
			}
		} else {
			if ( opts.index < targets.length - 1 ) {
				proceed = true;
				opts.index += 1;
			} else {
				if ( opts.swing ) {
					opts.dir = 'left';
				} else {
					opts.index = 0;
				}
			}
		}

		if ( proceed ) {
			if ( timeout ) {
				window.setTimeout( $.fn.laittoggler.changeClass.bind(this), timeout );
			} else {
				var changeClass = $.fn.laittoggler.changeClass.bind(this);
				changeClass();
			}
		} else {
			// If a specific entry point is set, focus on that.
			if ($target.hasClass(tclass)) {
				$elem.removeClass("active").removeClass("hide-cla-title-el");
				if (jQuery.contains($target[0], $elem[0])) {
					if ($elem.attr("data-entry-point")) {
						jQuery($elem.attr("data-entry-point")).focus();
					} else {
						$elem.focus();
					}
				}
			} else {
				$elem.addClass("active").addClass("hide-cla-title-el");
			}
		}
		$elem.data('laittoggler', opts);
	};

})(jQuery);
