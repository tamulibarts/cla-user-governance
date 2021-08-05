<?php ?><div class="switch-a"><fieldset aria-label="switch between the live website and the sandbox website" role="radiogroup"><!-- 	<legend><h1>Live and Sandbox Website Switching Toggle</h1></legend> --><div class="c-toggle"><label for="wpug_env_live" title="You are on the Live Site">Live Site</label><span class="c-toggle__wrapper"><input type="radio" name="environment" id="wpug_env_live" checked><input type="radio" name="environment" id="wpug_env_sandbox"><span aria-hidden="true" class="c-toggle__background"><svg version="1.1" width="56" height="26" viewBox="0 0 56 26"><rect x="3" y="3" width="50" height="20" fill="none" stroke="white" stroke-width="2" ry="10" rx="10" /></svg></span><span aria-hidden="true" class="c-toggle__switcher"></span></span><label for="wpug_env_sandbox" title="Click to go to the Sandbox Site">Go to Sandbox Site</label><?php

	ob_start();
	include dirname( __FILE__ ) . '/network-sb-q-panel.php';
	$switch_title = ob_get_clean();
	$switch_title = preg_replace( '/[\s\n]*$/', '', $switch_title );

?></div></fieldset></div>
