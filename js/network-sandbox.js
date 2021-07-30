(function(){
	var current_home = document.location.origin + '/';
	if ( current_home.indexOf('.local') > 0 ) {
		var live_home = 'http://liberalarts.local/';
		var sandbox_home = 'http://libartstest.local/';
	} else {
		var live_home = 'https://liberalarts.tamu.edu/';
		var sandbox_home = 'https://libartstest.wpengine.com/';
	}
	var environment = current_home === sandbox_home ? 'sandbox' : 'live';
var live = "Live Site";
var live_slug = "wpug_env_live";
var sandbox = "Sandbox Site";
var sandbox_slug = "wpug_env_sandbox";

// Add event listeners.
jQuery(".switch-a input[id^='" + live_slug + "']").on("change", function (e) {
	var input = this;
	var live_label = this.parentNode.parentNode.querySelector(
		"label[for^=" + live_slug + "]"
	);
	var sandbox_label = this.parentNode.parentNode.querySelector(
		"label[for^=" + sandbox_slug + "]"
	);
	window.setTimeout(function () {
		if (input.checked) {
			live_label.innerHTML = live;
			live_label.title = "";
			sandbox_label.innerHTML = "Go to " + sandbox;
			sandbox_label.title = "Click to go to the Sandbox Site";
		} else {
			sandbox_label.innerHTML = sandbox;
			sandbox_label.title = "";
			live_label.innerHTML = "Go to " + live;
			live_label.title = "Click to go to the Live Site";
		}
	}, 400);
});
jQuery(".switch-a input[id^='" + sandbox_slug + "']").on(
	"change",
	function (e) {
		var input = this;
		var live_label = this.parentNode.parentNode.querySelector(
			"label[for^=" + live_slug + "]"
		);
		var sandbox_label = this.parentNode.parentNode.querySelector(
			"label[for^=" + sandbox_slug + "]"
		);
		window.setTimeout(function () {
			if (input.checked) {
				live_label.innerHTML = "Go Back to " + live;
				live_label.title = "Click to go back to the Live Site.";
				sandbox_label.innerHTML = sandbox;
				sandbox_label.title = "";
			} else {
				live_label.innerHTML = live;
				live_label.title = "";
				sandbox_label.innerHTML = "Go to " + sandbox;
				sandbox_label.title = "Click to go to the Sandbox Site";
			}
		}, 400);
	}
);
jQuery(".switch-a .toggler").on("click", function (e) {
	var $toggler = jQuery(this);
	if (!$toggler.is(".toggler")) {
		$toggler = $toggler.closest(".toggler");
	}
	var targets = $toggler.attr("data-target");
	targets = targets.split(",");
	for (var i = 0; i < targets.length; i++) {
		var pair = targets[i].split(":");
		var tar = pair[0];
		var tclass = pair.length > 1 ? pair[1] : "hidden";
		var $target = jQuery(tar);
		$target.toggleClass(tclass);
		// If a specific entry point is set, focus on that.
		if ($target.hasClass(tclass)) {
			$toggler.removeClass("active");
			if (jQuery.contains($target[0], $toggler[0])) {
				if ($toggler.attr("data-entry-point")) {
					jQuery($toggler.attr("data-entry-point")).focus();
				} else {
					$toggler.focus();
				}
			}
		} else {
			$toggler.addClass("active");
		}
	}
});


})();
