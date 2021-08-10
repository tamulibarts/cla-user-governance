<?php ?><script type="text/javascript">(function(){
	// Detect the admin bar default text color.
	// Zachary Watkins, zwatkins2@tamu.edu
	// The HTML markup for this switch is immutable due to accessibility requirements.
	// This solution was more future-proof than other alternatives available at the time (8/10/2021).
	// If WordPress admin themes ever register default text colors in their admin color themes, we can
	// use that instead of using JS to detect it.
	var test_item = document.createElement('li');
	test_item.id = 'wp-admin-bar-wpug-sample-text-color';
	test_item.className = 'menupop';
	test_item.style.cssText = 'width:0;visibility:hidden;';
	test_item.innerHTML = '<div class="ab-item ab-empty-item">WPUG</div>';
	var wp_logo = document.querySelector('#wpadminbar #wp-admin-bar-wp-logo');
	wp_logo.parentNode.insertBefore(test_item, wp_logo);
	var styles = window.getComputedStyle(test_item.querySelector('.ab-item'));
	var default_text_color = styles.getPropertyValue('color');
	test_item.parentNode.removeChild(test_item);
	var css_node = document.getElementById('wp-user-governance-network-sb-live-styles-inline-css').childNodes[0];
	var css = css_node.textContent.replace('{}', '{\n  color: ' + default_text_color + ';\n}');
	css_node.textContent = css;
})();</script>
