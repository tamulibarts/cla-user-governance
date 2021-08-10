// Liberal Arts Information Technology
// Switch Plugin
// GPL-2.0+
// Zachary Watkins, zwatkins2@tamu.edu
(function ($) {
	$.fn.laitswitch = function (options) {
		var opts = $.extend({}, options);
		return this.each(function () {
			var $witch = $(this),
				$radios = $witch.find(".c-toggle__wrapper > input[type=radio]"),
				$labels = $witch.find("label");
			var changeLabels = function () {
				var input = $radios[0],
					label_index = 0,
					status_index = input.checked ? 0 : 1,
					text_opt = opts.text[label_index][status_index],
					$first_label = $labels.eq(label_index);
				$first_label.html(text_opt[0]);
				if (text_opt.length > 1) {
					$first_label.title
					$first_label.attr("title", text_opt[1]);
				}
				// Update the other element.
				var other_status_index = status_index === 0 ? 1 : 0,
					other_label_index = label_index === 0 ? 1 : 0,
					other_text_opt = opts.text[other_label_index][other_status_index],
					$second_label = $labels.eq(other_label_index);
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

			var focusRadio = function(e){
				var index = e.target.previousSibling ? 1 : 0;
				var $ctoggle = $radios.first().parent().parent();
				$ctoggle.find('label').eq(index).add('.c-toggle__wrapper').addClass('active');
			};

			var blurRadio = function(e){
				var index = e.target.previousSibling ? 1 : 0;
				var $ctoggle = $radios.first().parent().parent();
				$ctoggle.find('label').eq(index).add('.c-toggle__wrapper').removeClass('active');
			};

			var enterRadio = function(e){
				var index = e.target.previousSibling ? 1 : 0;
				var $label = $labels.eq(index);
				$label.addClass('active');
			};

			var leaveRadio = function(e){
				if ( false === $(e.target).is(":focus") ) {
					var index = e.target.previousSibling ? 1 : 0;
					var $label = $labels.eq(index);
					$label.removeClass('active');
				}
			};

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
				var $elem = $(this);
				if (!$elem.is(opts.sel)) {
					$elem = $elem.closest(opts.sel);
				}
				var targets = $elem.attr("data-target");
				targets = targets.split(",");
				for (var i = 0; i < targets.length; i++) {
					var pair = targets[i].split(":"),
						tar = pair[0],
						tclass = pair.length > 1 ? pair[1] : "hidden",
						$target = jQuery(tar);
					$target.toggleClass(tclass);
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
			};
			$toggler.on("click", toggle);
		});
	};
})(jQuery);

// Initialize use of the plugins.
(function($){
	var destination = undefined === wpugnsbdest ? false : wpugnsbdest;
	$("#wp-admin-bar-wpug_network_sandbox_link .toggler").laittoggler({ sel: ".toggler" });

	$(".switch-a")
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
