<?php ?><div class="switch-a">
	<fieldset aria-label="switch between the live website and the sandbox website" role="radiogroup">
		<!-- 	<legend><h1>Live and Sandbox Website Switching Toggle</h1></legend> -->
		<div class="c-toggle">
			<label for="wpug_env_live" title="You are on the Live Site">Live Site</label><span class="c-toggle__wrapper"><input type="radio" name="environment" id="wpug_env_live" checked><input type="radio" name="environment" id="wpug_env_sandbox"><span aria-hidden="true" class="c-toggle__background"><svg version="1.1" width="56" height="26" viewBox="0 0 56 26">
						<rect x="3" y="3" width="50" height="20" fill="none" stroke="white" stroke-width="2" ry="10" rx="10" />
					</svg></span><span aria-hidden="true" class="c-toggle__switcher"></span></span><label for="wpug_env_sandbox" title="Click to go to the Sandbox Site">Go to Sandbox Site</label>
			<div class="q-button"><a class="info-icon toggler cla-title" href="#more-info" data-target=".c-toggle__tooltip"><svg id="svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0, 0, 22, 22">
						<circle cx="11" cy="11" r="8" fill="none" stroke="white" stroke-width="2" /><text style="font: bold 14px sans-serif;" x="6.75" y="16" class="small" fill="white">?</text>
					</svg><span class="cla-title-el">Click for more information</span></a><span class="c-toggle__tooltip hidden">
					<div class="nowrap">What is a Sandbox Site?</div>
					<div class="nowrap">It's like a <span class="big">private classroom</span> where you can learn</div>
					<div class="nowrap">and experiment. Don't worry, it's easy to fix.</div>
					<div class="actions" style="text-align:center;"><a class="text action toggler" data-target=".long-desc:hidden,.q-button:expand" href="#moreinfo">Tell Me More!</a> &nbsp;&nbsp;&nbsp;&nbsp;<a class="text action gigem toggler" href="#gotit" data-target=".c-toggle__tooltip" data-entry-point=".info-icon">Got it! <?php echo file_get_contents( WP_USER_GOV_DIR_PATH . 'img/gigem.svg' ); ?></a>
					</div>
					<div class="long-desc hidden">
						<h3>How do Sandbox sites work?</h3>
						<ul>
							<li>It is a copy of your current website.</li>
							<li>It can only be seen by logged in users.</li>
							<li>Updates to the Sandbox are typically at 8am Monday.</li>
							<li>User trainings are done using Sandbox sites.</li>
							<li>Web support will not move content from a Sandbox site to a Live site.</li>
							<li>It is identified by a yellow background in the top toolbar.</li>
						</ul>
						<h3>I need help!</h3>
						<div>If you are having trouble with this feature, please submit a helpdesk ticket to <a class="text" href="mailto:liberalartsit@tamu.edu">LiberalartsIT@tamu.edu</a>.</div>
						<h3>I want to give feedback!</h3>
						<div>This is a new feature and we would love your feedback! Please let us know what you think by sending an email here: (link). This feature is subject to change and we will let you know via email and in the dashboard when it does.</div>
					</div></span></div></div></fieldset></div>
