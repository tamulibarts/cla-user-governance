# User Governance Plugin for WordPress

Web Team > Information Technology > Dean's Office > College of Liberal Arts > Texas A&M University

A GNU GPL 2.0 (or later) WordPress Plugin to provide some useful user-centric tools for a WordPress single or multisite network.

This is a Customer Support plugin originally co-authored by Zachary Watkins <zwatkins2@tamu.edu> and Marcy Heathman <heathman@tamu.edu>. The project is still in development - *some features are not fully implemented*. It is provided here for other WordPress developers who may want to use features which are considered ready for production or who are otherwise interested in the code we are developing.

## Features

### Page Access Restriction

This plugin adds a settings page which allows a user (defined as a constant in wp-config.php) to restrict other users' access to posts and pages individually.

To maintain compatibility with the Wordpress Nested Pages plugin, we have removed the Quick Edit feature from post list page views provided by the Nested Pages plugin for users who have their page access restricted by the User Governance plugin.

* Settings page for configuring user page access
* Global constant `CLA_USER_GOV_MASTER_USER` to define a master user name in wp-config.php

### Sandbox Site

The sandbox site is where users can get hands-on experience with WordPress core features and any plugins or theme features enabled on the site.

Before upgrading this installation, you must reset the Sandbox site to its default state.

## WordPress Requirements

1. Advanced Custom Fields Pro

## Installation

1. Download this plugin
2. Upload it to your site via FTP

## Development Installation

1. Copy this repo to the desired location.
2. In your terminal, navigate to the plugin location 'cd /path/to/the/plugin'.
3. Run "npm start" to configure your local copy of the repo, install dependencies, and build files for a production environment.
4. Or, run "npm start -- develop" to configure your local copy of the repo, install dependencies, and build files for a development environment.

## Development Notes

When you stage changes to this repository and initiate a commit, they must pass PHP and Sass linting tasks before they will complete the commit step. Release tasks can only be used by the repository's owners.

## Todo
1. Remove dependency on Advanced Custom Fields.
2. Allow restricting users based on taxonomy.

### Admin Settings Page Notes
`add_options_page` puts a menu/link in the “Settings” menu
`add_menu_page` puts a menu/link at the same level as “Dashboard”, “Posts”, “Media”, etc.
`add_submenu_page` puts a menu/link as a child underneath “Dashboard”, “Posts”, “Media”, etc.

## Development Tasks

1. Run "grunt develop" to compile the css when developing the plugin.
2. Run "grunt watch" to automatically compile the css after saving a *.scss file.
3. Run "grunt" to compile the css when publishing the plugin.
4. Run "npm run checkwp" to check PHP files against WordPress coding standards.

## Development Requirements

* A shell environment variable for `RELEASE_KEY` that contains your Github release key.
* Node: http://nodejs.org/
* NPM: https://npmjs.org/
* Ruby: http://www.ruby-lang.org/en/, version >= 2.0.0p648
* Ruby Gems: http://rubygems.org/
* Ruby Sass: version >= 3.4.22

## Credits

1. The original co-authors are Zachary Watkins <zwatkins2@tamu.edu> and Marcy Heathman <heathman@tamu.edu>, the entire Web Team for the Information Technology Division of the Dean's Office at the College of Liberal Arts at Texas A&M University.
2. Zachary Watkins and Marcy Heathman collaborated on technology, accessibility, and communication and training strategies involved with the features which were implemented into this plugin.
3. Zachary Watkins provided programming and evaluated technology and UI/UX solutions while collaborating with Marcy Heathman to ensure a best-fit approach for optimal user experience.
