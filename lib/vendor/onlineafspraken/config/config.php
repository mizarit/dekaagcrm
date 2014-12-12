<?php
/*
 * This file is part of the OnlineAfspraken.nl Widget package
 * (c) 2011 OnlineAfspraken <info@onlineafspraken.nl>
 * 
 * @author Ricardo Matters <ricardo.matters@mizar-it.nl>
 * 
 */
$options = get_option('dekaagcrm_plugin_options');

if ($_SESSION['company'] == 2) {
  define('API_URL', 			isset($options['plugin_oaapi_url2']) ? $options['plugin_oaapi_url2'] : 'https://agenda.onlineafspraken.nl/APIREST');
  define('API_KEY', 			isset($options['plugin_oaapi_key2']) ? $options['plugin_oaapi_key2'] : 'bldk25woqu91-blza01');
  define('API_SECRET', 		isset($options['plugin_oaapi_secret2']) ? $options['plugin_oaapi_secret2'] : '1071b3303a40dc574a18d2ec6f780024f9b3681b');
}
else {
  define('API_URL', 			isset($options['plugin_oaapi_url1']) ? $options['plugin_oaapi_url1'] : 'http://onlineafspraken.dev.mizar-it.nl/APIREST');
  define('API_KEY', 			isset($options['plugin_oaapi_key1']) ? $options['plugin_oaapi_key1'] : 'fhlg83culd13-bzld03');
  define('API_SECRET', 		isset($options['plugin_oaapi_secret1']) ? $options['plugin_oaapi_secret1'] : '22571c6007f22bbb9d3d9dbaf5a4b7e2a976fea3');
}

// The relative URL of widget.php, accessible with your browser, after any mod-rewriting
//
// If the script is accessible at http://example.com/widget/lib/widget.php
// then the widget URL is /widget/lib/widget.php
//
// If the script is accessible at http://example.com/online-booking, which is internally mod-rewritten to the widget.php script,
// then the WIDGET_URL = /online-booking
//define('WIDGET_URL',  	'/wp-content/plugins/dekaagcrm/lib/vendor/onlineafspraken/lib/widget.php');
define('WIDGET_URL',  	'/widgetAjax');

// The absolute URL of widget.php within your web root WITHOUT any mod-rewriting
//
// For example, if your websites index.html is located in:
//
// /var/www/htdocs/example.com
//
// And the widget is installed here:
//
// /var/www/htdocs/example.com/widget/lib/widget.php
//
// The WIDGET_REAL_URL would be:
//
// /widget/lib/widget.php

define('WIDGET_REAL_URL', '/wp-content/plugins/dekaagcrm/lib/vendor/onlineafspraken/lib/widget.php');


// The options below specify how the widget looks
// To configure a multi-language widget, use something similar to:

// $culture = 'nl_NL'; // Get this setting from your website session, _GET var, etc.
// if ($culture == 'nl_NL') {
//  define('APP_LANGUAGE', 	'nl');
//  define('APP_CULTURE', 	'nl_NL.UTF-8');
//  define('DAY_OFFSET',    1);
//  define('APP_TITLE', 		'Mijn Agenda');
//}
//else {
//  define('APP_LANGUAGE', 	'en');
//  define('APP_CULTURE', 	'en_US');
//  define('DAY_OFFSET',    0);
//  define('APP_TITLE', 		'My Calendar');
//}

// For a single language widget, just use the defines as specified below:

// The title of the widget, typically your company name
define('APP_TITLE', 		'Mijn Agenda');

// The language of the widget
// The language file must be available under /i18n/language.php
// For instance, if you need to add German, create a file /i18n/de.php, and copy the contents from en.php which you can translate
// Then, set this value to de
define('APP_LANGUAGE', 	'nl');

// The culture of the widget, also known as locale
// Locales are used to format date strings, like "Wednesday 20 march 2011" and are server-wide setups
// So your server must have at least one locale installed ( typically en_US )
define('APP_CULTURE', 	'nl_NL.UTF-8');

// Offset of the calendar. For Dutch, set this to 1 ( week starts on monday ), for English, set this to 0 ( week starts on sunday )
define('DAY_OFFSET',    1);

// Theme
define('APP_THEME',     'default');


// Facebook
// To allow Facebook login, make sure you register your App first with Facebook at:
// http://developers.facebook.com/setup/
define('FB_USE_LOGIN',   false);
define('FB_APP_ID',      'xxxxxxxxxxxxxxx');
define('FB_APP_SECRET',  'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
