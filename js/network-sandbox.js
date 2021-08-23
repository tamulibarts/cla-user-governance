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
})(jQuery);(function ($) {

	$.fn.laithelp = function (options) {

		var opts = $.extend({
			panel: '.help-panel',
			button: 'a',
			close: '.close',
			subpanel: false,
			subpanel_button: false
		}, options);

		return this.each( function() {
			var $parent = $(this),
			    $button = $parent.find( opts.button ).first();
			opts.button_el = $button;
			opts.panel_el = $parent.find( opts.panel ).first();
			opts.close_el = $parent.find( opts.close );
			$parent.data( 'laithelp', opts );
			opts.button_el.on( 'click', $.fn.laithelp.toggleTopPanel.bind( $parent ) );
			opts.close_el.on( 'click', $.fn.laithelp.closeTopPanel.bind( $parent ) );

			if ( opts.subpanel && opts.subpanel_button ) {
				opts.subpanel_els = opts.panel_el.find( opts.subpanel );
				opts.subpanel_button_els = opts.panel_el.find( opts.subpanel_button );
				opts.subpanel_button_els.on( 'click', $.fn.laithelp.toggleSubPanel.bind( $parent ) );
			}
		});

	};

	$.fn.laithelp.toggleTopPanel = function(e) {
		e.preventDefault();
		var opts = this.data('laithelp');
		opts.panel_el.toggleClass('hidden');
		opts.button_el.toggleClass('active');
		opts.button_el.toggleClass('hide-cla-title-el');
	};

	$.fn.laithelp.closeTopPanel = function(e) {
		e.preventDefault();
		var opts = this.data('laithelp');
		opts.panel_el.addClass('hidden');
		opts.button_el
			.removeClass('active')
			.removeClass('hide-cla-title-el')
			.focus()
			.blur();
	};

	$.fn.laithelp.toggleSubPanel = function(e) {
		e.preventDefault();
		var opts = this.data('laithelp');
		console.log(e);
	};

})(jQuery);


// Initialize use of the plugins.
(function($){
	var destination = undefined === wpugnsbdest ? false : wpugnsbdest;
	var $menu_node = $("#wp-admin-bar-wpug_network_sandbox_link");
	$menu_node.find(".help-button").laithelp({
		subpanel: '.help-subpanel',
		subpanel_button: '.actions .action'
	});

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
