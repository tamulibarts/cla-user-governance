// Liberal Arts Information Technology
// Switch Plugin
// GPL-2.0+
// Zachary Watkins, zwatkins2@tamu.edu
(function ($) {
	$.fn.laitswitch = function (options) {
		var opts = $.extend({}, options);
		return this.each(function () {
			var $witch = $(this),
				$toggle_wrap = $witch.find(".c-toggle__wrapper"),
				$radios = $witch.find(".c-toggle__wrapper > input[type=radio]"),
				$labels = $witch.find(".c-toggle-label");

			var changeLabels = function () {
				var input = $radios[0],
					label_index = 0,
					status_index = input.checked ? 0 : 1,
					text_opt = opts.text[label_index][status_index],
					$first_label = $labels.eq(label_index).find("label");
				$first_label.html(text_opt[0]);
				if (text_opt.length > 1) {
					$first_label.title;
					$first_label.attr("title", text_opt[1]);
				}
				// Update the other element.
				var other_status_index = status_index === 0 ? 1 : 0,
					other_label_index = label_index === 0 ? 1 : 0,
					other_text_opt = opts.text[other_label_index][other_status_index],
					$second_label = $labels.eq(other_label_index).find("label");
				$second_label.html(other_text_opt[0]);
				if (other_text_opt.length > 1) {
					$second_label.attr("title", other_text_opt[1]);
				}
			};

			var switchIt = function () {
				if (opts.hasOwnProperty("text")) {
					changeLabels();
				}
				// Optional callback.
				if (opts.hasOwnProperty("callback")) {
					var $checked_input = $radios.filter(":checked");
					opts["callback"]($checked_input);
				}
			};

			var delaySwitchIt = function () {
				window.setTimeout(switchIt, opts.animation);
			};

			var focusRadio = function (e) {
				var index = this.previousSibling ? "input-focus-right" : "input-focus-left";
				$witch.addClass(index);
			};

			var blurRadio = function (e) {
				var index = this.previousSibling ? "input-focus-right" : "input-focus-left";
				$witch.removeClass(index);
			};

			var enterRadio = function (e) {
				var index = this.previousSibling ? "input-enter-right" : "input-enter-left";
				$witch.addClass(index);
			};

			var leaveRadio = function (e) {
				var index = this.previousSibling ? "input-enter-right" : "input-enter-left";
				$witch.removeClass(index);
			};

			var enterLabel = function (e) {
				var index = this.previousSibling ? "label-enter-right" : "label-enter-left";
				$witch.addClass(index);
			};

			var leaveLabel = function (e) {
				var index = this.previousSibling ? "label-enter-right" : "label-enter-left";
				$witch.removeClass(index);
			};

			// If the plugin uses label switching at all, add the event handlers.
			if (opts.hasOwnProperty("text") || opts.hasOwnProperty("callback")) {
				if (opts.animation > 0) {
					$radios.on("change", delaySwitchIt);
				} else {
					$radios.on("change", switchIt);
				}
			}

			// Add radio button event listeners.
			$radios.on("focus", focusRadio);
			$radios.on("blur", blurRadio);
			$radios.hover(enterRadio, leaveRadio);
			$labels.hover(enterLabel, leaveLabel);
		});
	};
})(jQuery);

// Liberal Arts Information Technology
// Toggler Plugin
// GPL-2.0+
// Zachary Watkins, zwatkins2@tamu.edu
(function ($) {
	$.fn.laittoggler = function (options) {
		var opts = $.extend({}, options);
		return this.each(function () {
			var $toggler = $(this);
			var toggle = function (e) {
				e.preventDefault();

				var $elem = $(this);
				if (!$elem.is(opts.sel)) {
					$elem = $elem.closest(opts.sel);
				}
				var targets = $elem.attr("data-target").split(",");
				var num_targets = targets.length;
				var changeClass = function(){
					var item = targets.shift(),
						pair = item.split(':'),
						tar = pair[0],
						tclass = pair.length > 1 ? pair[1] : "hidden",
						fchar = tclass.charAt(0),
						timeout = tclass.match(/\(\d+m?s\)$/),
						$target = jQuery(tar);
					console.log(item);
					// If the first character is + or - then the user is forcing the class to be added or removed.
					if ( timeout ) {
						timeout = timeout[0].slice(1,-1);
						var factor = 'm' === timeout.charAt( timeout.length - 2 ) ? 1 : 1000;
						timeout = parseInt(timeout) * factor;
						tclass = tclass.replace(timeout, '');
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

					if ( targets.length > 0 ) {
						if ( timeout ) {
							window.setTimeout( changeClass, timeout );
						} else {
							changeClass();
						}
					}
				};

				// Execute chain of class changes.
				changeClass();
			};
			$toggler.on("click", toggle);
		});
	};
})(jQuery);

// Initialize use of the plugins.
(function($){
	var destination = undefined === wpugnsbdest ? false : wpugnsbdest;
	var $menu_node = $("#wp-admin-bar-wpug_network_sandbox_link");
	$menu_node.find(".toggler").laittoggler({ sel: ".toggler" });

	$menu_node.find(".switch-a")
		.laitswitch({
			text: [
				[
					["Live Site", "You are on the Live Site"],
					["Go back to the Live Site", "Click to go to the Live Site"]
				],
				[
					["Sandbox Site", "You are on the Sandbox Site"],
					["Go to Sandbox Site", "Click to go to the Sandbox Site"]
				]
			],
			animation: 400,
			callback: function (el) {
				if ( destination !== '' ) {
					document.location.href = destination;
				}
			}
		});
})(jQuery);
