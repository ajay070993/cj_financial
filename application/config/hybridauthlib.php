<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

$config =
	array(
		// set on "base_url" the relative url that point to HybridAuth Endpoint
		'base_url' => '/hauth/endpoint/',

		"providers" => array (
			// openid providers
			// "OpenID" => array (
			// 	"enabled" => true
			// ),

			// "Yahoo" => array (
			// 	"enabled" => true,
			// 	"keys"    => array ( "id" => "", "secret" => "" ),
			// ),

			// "AOL"  => array (
			// 	"enabled" => true
			// ),

			// "Google" => array (
			// 	"enabled" => true,
			// 	"keys"    => array ( "id" => "", "secret" => "" ),
			// ),

			"Facebook" => array (
				"enabled" => true,
				"keys"    => array ( "id" =>"142415666416034", "secret" => "5ddb9918aa382b172465daa3f4034d61"),
				'scope' => 'email',
				'trustForwarded' => true
			),

			"Twitter" => array (
				"enabled" => true,
				"keys"    => array ( "key" => "Bj9mByZ8Lrvk90mgo3IStIHyD",
				 "secret" => "Bfx4CvtmHcbfwDZ7phO9KKZ7HnThCEoNeqRGVj4Xnlg4hJrnVg" ),
			),

			// windows liv
			// "Live" => array (
			// 	"enabled" => true,
			// 	"keys"    => array ( "id" => "", "secret" => "" )
			// ),

			// "MySpace" => array (
			// 	"enabled" => true,
			// 	"keys"    => array ( "key" => "", "secret" => "" )
			// ),

			"LinkedIn" => array (
				"enabled" => true,
				"keys"    => array ( "key" => "81wp6br5vxo0fo", "secret" => "QTvZZ9p9d518S9SA" ),
			),

			// "Foursquare" => array (
			// 	"enabled" => true,
			// 	"keys"    => array ( "id" => "", "secret" => "" )
			// ),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => (ENVIRONMENT == 'development'),

		"debug_file" => APPPATH.'/logs/hybridauth.log',
	);


/* End of file hybridauthlib.php */
/* Location: ./application/config/hybridauthlib.php */