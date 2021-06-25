# Page-level User Access

This plugin adds a settings page which allows a user (defined as a constant in wp-config.php) to restrict other users' access to posts and pages individually.

To maintain compatibility with the Wordpress Nested Pages plugin, we have made the following accommodations:
1. Any user limited by this plugin cannot use the quick edit options on Nested Pages list pages.

You may repurpose code from this repository for your own WordPress development since it uses a GPL-2.0+ license.

## WordPress Requirements

1. Advanced Custom Fields Pro

## Installation

1. Download this plugin
2. Upload it to your site via FTP

## Features

* Settings page for configuring user page access
* "CLA_USER_PAGE_ACCESS_MASTER_USER" global constant to define a master user name in wp-config.php

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

