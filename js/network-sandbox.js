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

	// Reset the radio button selection.
	jQuery('#wp-admin-bar-wpug_network_sandbox_style_switcher').on('click', 'input', function(e){
		var newClass = 'toggle-' + e.target.value;
		jQuery('#wp-admin-bar-wpug_network_sandbox_link')
			.attr('class','wpug-network-sandbox-link-to-sandbox')
			.addClass(newClass);
		jQuery('#wp-admin-bar-wpug_network_sandbox_link > .ab-empty-item')
			.html(getSwitchOutput(e.target.value, environment));
	});

	// Version B Handlers.
	jQuery('#wp-admin-bar-wpug_network_sandbox_link').on('click', '.switch-b input', function(e){
		var new_href = '';
		if ( true === e.target.checked ) {
			new_href = e.target.getAttribute('data-sandbox');
		} else {
			new_href = e.target.getAttribute('data-live');
		}
		window.setTimeout(function(){
			document.location.href = new_href;
		}, 400);
	});

	// Version B Handlers.
	jQuery('#wp-admin-bar-wpug_network_sandbox_link').on('click', '.switch-d input', function(e){
		var new_href = '';
		if ( true === e.target.checked ) {
			new_href = e.target.getAttribute('data-sandbox');
		} else {
			new_href = e.target.getAttribute('data-live');
		}
		window.setTimeout(function(){
			document.location.href = new_href;
		}, 400);
	});

	// Gutenberg adoption:
	function getSwitchOutput( style, environment ) {
		var output = 'Not found';
		switch ( style ) {
			case 'a':
				if ( 'sandbox' === environment ) {
					var link = live_home;
					var icon = '<svg id="live-icon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 28 22"><rect id="browser" x="0" y="0" width="28" height="22" fill="#f0f6fc" /><circle cx="3.5" cy="2.5" r="1.5" /><circle cx="7.5" cy="2.5" r="1.5" /><circle cx="11.5" cy="2.5" r="1.5" /><rect id="browser-screen" x="2" y="5" width="24" height="15" /><path id="globe" transform="translate(7 6) scale(0.7)" d="M9,0 C4.02943725,2.22044605e-16 6.66133815e-16,4.02943725 0,9 C-6.66133815e-16,13.9705627 4.02943725,18 9,18 C13.9705627,18 18,13.9705627 18,9 C18,4.02943725 13.9705627,6.66133815e-16 9,0 Z M12.46,11.95 C12.46,13.42 11.66,15.25 8.4,16.65 C8.7,12.48 5.88,12.96 5.2,11.65 C5.32585594,10.5486495 6.00438361,9.58740195 7,9.1 C5.44816932,8.83382651 4.00092548,8.14136534 2.82,7.1 C2.8697862,7.57063002 3.09899651,8.00398077 3.46,8.31 C2.67791156,8.01519945 2.00215848,7.49270996 1.52,6.81 C2.49711943,3.58500598 5.40348457,1.32806312 8.77,1.18 C7.93,2.56 7.27,5.31 8.77,6.75 C7.23,7 6.26,5 5.41,5.79 C4.28,6.85 5.74,8.3 8.83,8.87 C12.12,9.46 12.49,10.45 12.46,11.95 Z M13.8,7.95 C13.48,6.84 14.42,5.72 15.49,4.81 C16.8462909,6.76503346 17.1600342,9.26003943 16.33,11.49 C15.56,9.6 14.16,9.17 13.8,7.92 L13.8,7.95 Z" fill="#0F0"></path></svg>';
					var title = 'Live';
				} else {
					var link = sandbox_home;
					var icon = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 28 22"><rect id="browser" x="0" y="0" width="28" height="22" fill="#f0f6fc" /><circle cx="3.5" cy="2.5" r="1.5" /><circle cx="7.5" cy="2.5" r="1.5" /><circle cx="11.5" cy="2.5" r="1.5" /><rect id="browser-screen" x="2" y="5" width="24" height="15" /><path id="lightbulb" transform="translate(7 5.5) scale(0.7)" d="M10 1c3.11 0 5.63 2.52 5.63 5.62 0 1.84-2.030 4.58-2.030 4.58-0.33 0.44-0.6 1.25-0.6 1.8v1c0 0.55-0.45 1-1 1h-4c-0.55 0-1-0.45-1-1v-1c0-0.55-0.27-1.36-0.6-1.8 0 0-2.020-2.74-2.020-4.58 0-3.1 2.51-5.62 5.62-5.62zM7 16.87v-0.87h6v0.87c0 0.62-0.13 1.13-0.75 1.13h-0.25c0 0.62-0.4 1-1.020 1h-2c-0.61 0-0.98-0.38-0.98-1h-0.25c-0.62 0-0.75-0.51-0.75-1.13z" fill="#FFFF00"></path></svg>';
					var title = 'Sandbox';
				}
				output = '<a class="switch-a ab-item" href="' + link + 'wp-admin/">' + icon + ' Switch To The ' + title + ' Site</a>';
				break;
			case 'b':
				if ( 'sandbox' === environment ) {
					output = '<div class="switch-b" data-checked="true"><span class="env" title="Your public website">Live site</span> <label class="switch"><input type="checkbox" checked data-live="'+live_home+'wp-admin/" data-sandbox="'+sandbox_home+'wp-admin/"><div class="slider round"></div></label> <span class="env" title="Your private website for testing and learning">Sandbox site</span></div>';
				} else {
					output = '<div class="switch-b" data-checked="false"><span class="env" title="Your public website">Live site</span> <label class="switch"><input type="checkbox" data-live="'+live_home+'wp-admin/" data-sandbox="'+sandbox_home+'wp-admin/"><div class="slider round"></div></label> <span class="env" title="Your private website for testing and learning">Sandbox site</span></div>';
				}
				break;
			case 'c':
				if ( 'sandbox' === environment ) {
					output = '<div class="switch-c"><div class="onoffswitch"><input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" tabindex="0" checked><label class="onoffswitch-label" for="myonoffswitch"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div><span class="super-label">Switch Sites</span></div>';
				} else {
					output = '<div class="switch-c"><div class="onoffswitch"><input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" tabindex="0" checked><label class="onoffswitch-label" for="myonoffswitch"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div><span class="super-label">Switch Sites</span></div>';
				}
				break;
			case 'd':
				if ( 'sandbox' === environment ) {
					output = '<div class="switch-d switch-button"><input class="switch-button-checkbox" type="checkbox" data-live="'+live_home+'wp-admin/" data-sandbox="'+sandbox_home+'wp-admin/" checked></input><label class="switch-button-label" for=""><span class="switch-button-label-span">Live Site</span></label></div>';
				} else {
					output = '<div class="switch-d switch-button"><input class="switch-button-checkbox" type="checkbox" data-live="'+live_home+'wp-admin/" data-sandbox="'+sandbox_home+'wp-admin/"></input><label class="switch-button-label" for=""><span class="switch-button-label-span">Live Site</span></label></div>';
				}
				break;
			default:
				break;
		}
		return output;
	}
})();
