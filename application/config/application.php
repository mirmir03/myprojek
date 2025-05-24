<?php defined('BASEPATH') || exit('No direct script access allowed');
//------------------------------------------------------------------------------
// !ASSETS
//------------------------------------------------------------------------------

// The names of the directories for the various assets.
//  'base' is relative to public, all others are relative to 'base', except
//  'module', which defines a directory name within both 'css' and 'js'.
// Trailing and preceding slashes are removed
$config['assets.directories'] = array(
    'base'   => 'assets',
    'cache'  => 'cache',
    'css'    => 'css',
    'image'  => 'images',
    'js'     => 'js',
    'module' => 'module',
);

// The 'assets.js_opener' and 'assets.js_closer' strings are used to wrap all
// inline scripts. By default, these are setup to work with jQuery.
$config['assets.js_opener'] = "$(document).ready(function() {";
$config['assets.js_closer'] = "});";

// The 'assets.css_combine' and 'assets.js_combine' settings tell the Assets
// library whether css and js files, respectively, should be combined.
$config['assets.css_combine'] = false;
$config['assets.js_combine']  = false;

// The 'assets.css_minify' and 'assets.js_minify' settings are used to tell the
// Assets library to minify the combined css and js files, respectively
$config['assets.css_minify'] = true;
$config['assets.js_minify']  = true;

// The 'assets.encrypt' setting will mask the app structure by encrypting the
// filename of the combined files.
// If false the filename would be in the format...
//      theme_module_controller_method
// If true, it would be an md5 hash of the above filename.
$config['assets.encrypt_name'] = false;

// The 'assets.encode' setting is used to specify whether the assets should be
// encoded based on the HTTP_ACCEPT_ENCODING value.
$config['assets.encode'] = false;

/**
* Showing Data
*/
$config['list_limit'] = 25;