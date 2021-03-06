<?php
/**
 * Core file
 *
 * @author Vince Wooll <sales@jomres.net>
 * @version Jomres 7
 * @package Jomres
 * @copyright    2005-2013 Vince Wooll
 * Jomres (tm) PHP, CSS & Javascript files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly.
 **/


// ################################################################
defined( '_JOMRES_INITCHECK' ) or die( '' );
// ################################################################

class j16000showplugins
	{
	function j16000showplugins()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		if ( function_exists( 'jr_import' ) )
			{
			jr_import( 'minicomponent_registry' );
			$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
			}
		else
		global $MiniComponents;
		if ( isset( $_REQUEST[ 'purchase' ] ) )
			{
			$items    = "&items=" . jomresGetParam( $_REQUEST, 'items', '' );
			$username = "&username=" . jomresGetParam( $_REQUEST, 'username', '' );
			$password = "&password=" . jomresGetParam( $_REQUEST, 'password', '' );
			jomresRedirect( JOMRES_SITEPAGE_URL_ADMIN . '&task=purchase_plugins' . $username . $password . $items, "" );
			exit;
			}
		$registry = new minicomponent_registry( true );
		$registry->regenerate_registry();
		jomres_cmsspecific_addheaddata( "javascript", 'jomres/javascript/', "shop.js", false );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = false;

			return;
			}

		include( JOMRESCONFIG_ABSOLUTE_PATH . JRDS . 'jomres' . JRDS . 'jomres_config.php' );
		$this_jomres_version = explode( ".", $mrConfig[ 'version' ] );

		$installed_plugins   = array ();
		$jrePath             = JOMRESCONFIG_ABSOLUTE_PATH . JRDS . 'jomres' . JRDS . 'remote_plugins' . JRDS;
		$third_party_plugins = array ();
		if ( !is_dir( $jrePath ) )
			{
			if ( !@mkdir( $jrePath ) )
				{
				echo "Error, unable to make folder " . $jrePath . " automatically therefore cannot install plugins. Please create the folder manually and ensure that it's writable by the web server";

				return;
				}
			}

		$jrcPath             = JOMRESCONFIG_ABSOLUTE_PATH . JRDS . 'jomres' . JRDS . 'core-plugins' . JRDS;
		$third_party_plugins = array ();
		if ( !is_dir( $jrcPath ) )
			{
			if ( !@mkdir( $jrcPath ) )
				{
				echo "Error, unable to make folder " . $jrcPath . " automatically therefore cannot install plugins. Please create the folder manually and ensure that it's writable by the web server";

				return;
				}
			}

		jr_import( 'jomres_check_support_key' );
		$key_validation  = new jomres_check_support_key( JOMRES_SITEPAGE_URL_ADMIN . "&task=showplugins" );
		$this->key_valid = $key_validation->key_valid;

		//$this->key_valid = false; // for testing

		$developer_user = false;

		$siteConfig = jomres_singleton_abstract::getInstance( 'jomres_config_site_singleton' );
		$jrConfig   = $siteConfig->get();

		$current_licenses = array ();

		if ( $this->key_valid ) 
			{
			$developer_user = true;
			}
		else
			{
			$request  = "request=get_license_numbers&username=" . $jrConfig[ 'license_server_username' ] . "&password=" . $jrConfig[ 'license_server_password' ];
			$response = query_shop( $request );
			if ( $response->success )
				{
				foreach ( $response->licenses as $license )
					{

					$current_licenses[ $license->name ] = $license->license_key;
					}

				}
			}

		$remote_plugins_data = queryUpdateServer( "", "r=dp&format=json&cms=" . _JOMRES_DETECTED_CMS . "&key=" . $key_validation->key_hash );

		$rp_array = json_decode( $remote_plugins_data );
		foreach ( $rp_array as $rp )
			{
			$price_known = true;
			if ( !isset( $rp->price ) ) $price_known = false;

			$remote_plugins[ trim( jomres_sanitise_string( @$rp->name ) ) ] = array ( "name" => trim( jomres_sanitise_string( @$rp->name ) ), "version" => (float) @$rp->version, "lastupdate" => jomres_sanitise_string( @$rp->lastupdate ), "description" => jomres_sanitise_string( @$rp->description ), "type" => jomres_sanitise_string( @$rp->type ), "min_jomres_ver" => jomres_sanitise_string( @$rp->min_jomres_ver ), "price" => @$rp->price, "manual_link" => jomres_sanitise_string( @$rp->manual_link ), "change_log" => jomres_sanitise_string( @$rp->change_log ), "highlight" => jomres_sanitise_string( @$rp->highlight ), "image" => jomres_sanitise_string( @$rp->image ), "demo_url" => addslashes( @$rp->demo_url ), );
			}

		$d = @dir( $jrePath );
		if ( $d )
			{
			while ( false !== ( $entry = $d->read() ) )
				{
				$filename = $entry;
				if ( substr( $entry, 0, 1 ) != '.' )
					{
					if ( file_exists( $jrePath . $entry . JRDS . "plugin_info.php" ) )
						{
						include_once( $jrePath . $entry . JRDS . "plugin_info.php" );
						$cname = "plugin_info_" . $entry;
						if ( class_exists( $cname ) )
							{
							$info                                       = new $cname();
							$installed_plugins[ $info->data[ 'name' ] ] = $info->data;
							}
						}
					}
				}
			foreach ( $installed_plugins as $key => $val )
				{
				if ( !array_key_exists( $key, $remote_plugins ) )
					{
					$third_party_plugins[ $key ] = $val;
					}
				}
			}

		$d = @dir( $jrcPath );
		if ( $d )
			{
			while ( false !== ( $entry = $d->read() ) )
				{
				$filename = $entry;
				if ( substr( $entry, 0, 1 ) != '.' )
					{
					if ( file_exists( $jrcPath . $entry . JRDS . "plugin_info.php" ) )
						{
						include_once( $jrcPath . $entry . JRDS . "plugin_info.php" );
						$cname = "plugin_info_" . $entry;
						if ( class_exists( $cname ) )
							{
							$info = new $cname();
							// When developing it's easy to incorrectly rename a plugin info.php file. Uncomment the next line and run showplugins again to see which was the last plugin info that was called correctly before the script crashed
							// echo $cname."<br/>";
							$installed_plugins[ $info->data[ 'name' ] ] = $info->data;
							}
						}
					}
				}

			foreach ( $installed_plugins as $key => $val )
				{
				if ( !array_key_exists( $key, $remote_plugins ) )
					{
					$third_party_plugins[ $key ] = $val;
					}
				}
			}

		$output     = array ();
		$pageoutput = array ();
		//////////////////////////////////////////////////////

		$output[ 'PAGETITLE' ] = 'Jomres Plugin Manager';

		$bronze_users = array ();
		if ( !$developer_user && $jrConfig[ 'license_server_username' ] == "" )
			{
			if ( $jrConfig[ 'license_server_username' ] == "" ) // With the required vars setting in the template, if we don't make these at least spaces the appropriate cart section will not show.
				{
				$jrConfig[ 'license_server_username' ] = " ";
				$jrConfig[ 'license_server_password' ] = " ";
				}
			$bronze_users[ 0 ][ 'license_server_username' ] = $jrConfig[ 'license_server_username' ];
			$bronze_users[ 0 ][ 'license_server_password' ] = $jrConfig[ 'license_server_password' ];
			}

		////////////////////////////////////////////////////// Third party plugins
		if ( $developer_user )
			{
			$bronze_users[ 0 ][ 'dummy' ] = " ";
			}

		$uninstall_text      = "Uninstall";
		$externalPluginTypes = array ( "component", "module", "mambot" );
		$this->set_main_plugins();
		$thirdpartyplugins = array ();
		foreach ( $third_party_plugins as $tpp )
			{
			$type              = $tpp[ 'type' ];
			$n                 = $tpp[ 'name' ];
			$row_class         = 'availablefordownload';
			$installAction     = $install_text;
			$uninstallAction   = " ";
			$already_installed = false;
			$uninstallLink     = "";
			if ( array_key_exists( $n, $installed_plugins ) )
				{
				$already_installed = true;
				$uninstallAction   = $uninstall_text;
				$installAction     = $reinstall_text;
				$row_class         = 'alreadyinstalled';
				$uninstallLink     = JOMRES_SITEPAGE_URL_ADMIN . '&task=removeplugin&no_html=1&plugin=' . $n;
				}

			$local_version = $installed_plugins[ $n ][ 'version' ];
			if ( !array_key_exists( $n, $installed_plugins ) ) $local_version = "N/A";

			$r                    = array ();
			$r[ 'UNINSTALL' ]     = $uninstallAction;
			$r[ 'ROWCLASS' ]      = $row_class;
			$r[ 'NAME' ]          = $tpp[ 'name' ];
			$r[ 'LOCALVERSION' ]  = $local_version;
			$r[ 'DESCRIPTION' ]   = stripslashes( $tpp[ 'description' ] );
			$r[ 'AUTHOR' ]        = stripslashes( $tpp[ 'author' ] );
			$r[ 'AUTHOREMAIL' ]   = stripslashes( $tpp[ 'authoremail' ] );
			$r[ 'UNINSTALLLINK' ] = $uninstallLink;

			$thirdpartyplugins[ ] = $r;
			}


		////////////////////////////////////////////////////// Remote plugins
		$span = 12;
		if ( $developer_user ) $span = 11;
		$output[ 'SPAN' ] = $span;

		$install_text        = "Install";
		$reinstall_text      = "Reinstall";
		$upgrade_text        = "Upgrade";
		$uninstall_text      = "Uninstall";
		$externalPluginTypes = array ( "component", "module", "mambot" );

		$jomresdotnet_plugins = array ();

		$plugins_needing_upgrading = array ();
		
		$button_disabled_text = "";
		if (!$this->key_valid)
			{
			if ( using_bootstrap() )
				{
				$button_disabled_text = " disabled ";
				}
			else
				{
				$button_disabled_text = " ui-state-disabled ";
				}
			
			
			}
		foreach ( $remote_plugins as $rp )
			{
			$r = array ();

			$type        = $rp[ 'type' ];
			$plugin_name = $rp[ 'name' ];
			if ( $developer_user ) $n = $rp[ 'name' ];
			elseif ( array_key_exists( $plugin_name, $current_licenses ) ) $n = $plugin_name . "&plugin_key=" . $current_licenses[ $plugin_name ];
			else
			$n = $rp[ 'name' ];

			$min_jomres_ver = explode( ".", $rp[ 'min_jomres_ver' ] );

			$row_class       = '';
			$installAction   = $install_text;
			$uninstallAction = " ";
			if ( array_key_exists( $rp[ 'name' ], $installed_plugins ) )
				{
				$uninstallAction       = $uninstall_text;
				$installAction         = $reinstall_text;
				$row_class             = 'ui-state-highlight';
				$action                = "Reinstall";
				$uninstall             = "<a href=\"" . $uninstallLink . "\">" . $uninstallText . "</a>";
				$r[ 'UNINSTALL_LINK' ] = $uninstallLink;
				$r[ 'UNINSTALL_TEXT' ] = $uninstallText;
				if ( $rp[ 'version' ] > $installed_plugins[ $plugin_name ][ 'version' ] )
					{
					$plugins_needing_upgrading[ ] = $plugin_name;
					$installAction                = $upgrade_text;
					$row_class                    = 'ui-state-error';
					}
				}

			$r[ 'INSTALL_LINK' ] = '';
			$r[ 'INSTALL_TEXT' ] = '';
			//if ( array_key_exists( $plugin_name, $current_licenses ) || $developer_user )
			//	{
				$r[ 'INSTALL_LINK' ] = JOMRES_SITEPAGE_URL_ADMIN . '&task=addplugin&plugin=' . $n;
				$r[ 'INSTALL_TEXT' ] = $installAction;
			//	}

			$r[ 'HPLUGINPRICE' ] = '';
			if ( !$developer_user )
				{
				$r[ 'HPLUGINPRICE' ] = "Plugin price<br/>(Click to add to your cart)";
				}

			$r[ 'UNINSTALL_LINK' ] = '';
			$r[ 'UNINSTALL_TEXT' ] = '';
			$r[ 'UNINSTALL' ]      = '';
			if ( !in_array( $rp[ 'type' ], $externalPluginTypes ) && array_key_exists( $rp[ 'name' ], $installed_plugins ) )
				{
				$r[ 'UNINSTALL_LINK' ] = JOMRES_SITEPAGE_URL_ADMIN . '&task=removeplugin&no_html=1&plugin=' . $n;
				$r[ 'UNINSTALL_TEXT' ] = $uninstallAction;
				if ( using_bootstrap() ) $r[ 'UNINSTALL' ] = '<a href="' . $r[ 'UNINSTALL_LINK' ] . '" class="btn btn-danger" >' . $r[ 'UNINSTALL_TEXT' ] . '</a>';
				else
				$r[ 'UNINSTALL' ] = '<a href="' . $r[ 'UNINSTALL_LINK' ] . '" class="fg-button ui-state-default ui-corner-all">' . $r[ 'UNINSTALL_TEXT' ] . '</a>';
				}

			$local_version = $installed_plugins[ $plugin_name ][ 'version' ];
			if ( !array_key_exists( $plugin_name, $installed_plugins ) ) $local_version = "N/A";

			$style = "";

			if ( $rp[ 'price' ] == 0 && $row_class == '' )
				{
				if ( !using_bootstrap() )
					{
					$row_class = '';
					// $style = 'style="border-style:solid;border-color:#00ff00;border-width:1px;" '; // Shop now disabled
					$style = '';
					}
				else
					{
					//$row_class='freeplugin'; // Shop now disabled
					$row_class = '';
					$style     = '';
					}
				}

			$r[ 'MANUAL_LINK' ]  = '';
			$r[ 'MANUAL_TEXT' ]  = '';
			$r[ 'MANUAL_CLASS' ] = '';
			if ( isset( $rp[ 'manual_link' ] ) && $rp[ 'manual_link' ] != '' )
				{
				$r[ 'MANUAL_LINK' ]  = 'http://manual.jomres.net/' . $rp[ 'manual_link' ] . '.html';
				$r[ 'MANUAL_TEXT' ]  = 'Manual';
				$r[ 'MANUAL_CLASS' ] = 'btn';
				}

			$r[ 'DEMO_LINK' ]  = '';
			$r[ 'DEMO_TEXT' ]  = '';
			$r[ 'DEMO_CLASS' ] = '';
			if ( isset( $rp[ 'demo_url' ] ) && $rp[ 'demo_url' ] != '' )
				{
				$r[ 'DEMO_LINK' ]  = $rp[ 'demo_url' ];
				$r[ 'DEMO_TEXT' ]  = 'Demo';
				$r[ 'DEMO_CLASS' ] = 'btn';
				}

			if ( !using_bootstrap() )
				{
				$r[ 'CHANGELOG' ]         = '';
				$r[ 'CHANGELOG_TOOLTIP' ] = '';
				if ( $rp[ 'change_log' ] != '' )
					{
					$r[ 'CHANGELOG' ]         = $rp[ 'change_log' ];
					$r[ 'CHANGELOG_TOOLTIP' ] = jomres_makeTooltip( $rp[ 'name' ], $hover_title = "", $rp[ 'change_log' ], $rp[ 'change_log' ], $class = "", $type = "infoimage" );
					}
				$r[ 'HIGHLIGHT' ]         = '';
				$r[ 'HIGHLIGHT_TOOLTIP' ] = '';
				if ( $rp[ 'highlight' ] != '' )
					{
					$r[ 'HIGHLIGHT' ]         = $rp[ 'highlight' ];
					$r[ 'HIGHLIGHT_TOOLTIP' ] = jomres_makeTooltip( $rp[ 'name' ] . "_warning", $hover_title = "", $rp[ 'highlight' ], $rp[ 'highlight' ], $class = "", $type = "warning" );
					}
				}
			else
				{
				$r[ 'CHANGELOG' ] = '';
				if ( $rp[ 'change_log' ] != '' )
					{
					$r[ 'CHANGELOG' ] = '<script> jomresJquery(function () { jomresJquery("#changelog_' . $rp[ 'name' ] . '").popover({trigger: \'hover\'}); }); </script><a class="btn" id="changelog_' . $rp[ 'name' ] . '" rel="popover" title="Changelog" data-content="' . $rp[ 'change_log' ] . '">Changelog</a>';
					}
				$r[ 'HIGHLIGHT' ] = '';
				if ( $rp[ 'highlight' ] != '' )
					{
					$r[ 'HIGHLIGHT' ] = '<script> jomresJquery(function () { jomresJquery("#highlight_' . $rp[ 'name' ] . '").popover({trigger: \'hover\'}); }); </script><a class="btn btn-warning" id="highlight_' . $rp[ 'name' ] . '" rel="popover" title="Warning" data-content="' . $rp[ 'highlight' ] . '">Warning</a>';
					}
				}
			$readable_name        = ucwords( " " . str_replace( "_", " ", $rp[ 'name' ] ) );
			$r[ 'READABLE_NAME' ] = $readable_name;

			$r[ 'IMAGE' ] = $rp[ 'image' ];

			$r[ 'PLUGIN_NAME' ]    = $rp[ 'name' ];
			$r[ 'MIN_JOMRES_VER' ] = $rp[ 'min_jomres_ver' ];
			$r[ 'LOCAL_VER' ]      = $local_version;
			$r[ 'REMOTE_VER' ]     = $rp[ 'version' ];
			$r[ 'PLUGIN_DESC' ]    = stripslashes( $rp[ 'description' ] );
			$r[ 'LASTUPDATE' ]     = $rp[ 'lastupdate' ];

			$min_major_version = $min_jomres_ver[ 0 ];
			$min_minor_version = $min_jomres_ver[ 1 ];
			$min_revis_version = $min_jomres_ver[ 2 ];

			$current_major_version = $this_jomres_version[ 0 ];
			$current_minor_version = $this_jomres_version[ 1 ];
			$current_revis_version = $this_jomres_version[ 2 ];

			$r[ 'LATERVERSION' ] = "Requires a later version of Jomres";

			if ( $current_major_version >= $min_major_version && $current_minor_version >= $min_minor_version && $current_revis_version >= $min_revis_version ) $condition = 1;
			elseif ( $current_major_version >= $min_major_version && $current_minor_version > $min_minor_version ) $condition = 1;
			elseif ( $current_major_version > $min_major_version ) $condition = 1;
			else
			$condition = 0;

			if ( $condition == 1 ) $r[ 'LATERVERSION' ] = "";

			// The following condition is no longer needed, as we no longer sell plugins individually, but we'll keep it in place in case that policy changes.
			//if ( $condition == 1 && ( array_key_exists( $rp[ 'name' ], $current_licenses ) || $developer_user ) )
			//	{
				if ( using_bootstrap() ) 
					{
					$r[ 'INSTALL' ] = '<a href="' . $r[ 'INSTALL_LINK' ] . '" class="btn btn-primary" '.$button_disabled_text.' >' . $r[ 'INSTALL_TEXT' ] . '</a>';
					}
				else
					{
					$r[ 'INSTALL' ] = '<a href="' . $r[ 'INSTALL_LINK' ] . '" class="fg-button ui-state-default ui-corner-all '.$button_disabled_text.'"   >' . $r[ 'INSTALL_TEXT' ] . '</a>';
					}
			//	}


			$r[ 'PRICE' ] = $rp[ 'price' ];
			if ( !array_key_exists( $rp[ 'name' ], $installed_plugins ) && !array_key_exists( $rp[ 'name' ], $current_licenses ) )
				{
				if ( $rp[ 'price' ] == 0 )
					{
					$rp[ 'price' ] = 0.00;
					}
				$r[ 'PRICE' ]    = $rp[ 'price' ];
				$r[ 'ADD_TEXT' ] = "Add to cart";
				}

			if ( ( array_key_exists( $rp[ 'name' ], $installed_plugins ) || array_key_exists( $rp[ 'name' ], $current_licenses ) ) || $developer_user )
				{
				$r[ 'PRICE' ]              = '';
				$r[ 'ADD_TEXT' ]           = "";
				$r[ 'ADD_TO_CART_BUTTON' ] = "";
				}
			else
				{
				if ( $price_known )
					{
					if ( using_bootstrap() )
						{
						$btn_emphasis = "btn-success";
						if ( $r[ 'PRICE' ] == 0 ) $btn_emphasis = "btn-inverse";

						$r[ 'ADD_TO_CART_BUTTON' ] = '<button id="' . $r[ 'PLUGIN_NAME' ] . '" class="btn ' . $btn_emphasis . '" onClick="addToCart(\'' . $r[ 'PLUGIN_NAME' ] . '\',\'' . $r[ 'PRICE' ] . '\');">&pound;' . number_format( $r[ 'PRICE' ], 2 ) . '</button>';
						}
					else
					$r[ 'ADD_TO_CART_BUTTON' ] = '<button id="' . $r[ 'PLUGIN_NAME' ] . '" class="fg-button ui-state-default ui-corner-all" onClick="addToCart(\'' . $r[ 'PLUGIN_NAME' ] . '\',\'' . $r[ 'PRICE' ] . '\');" >&pound;' . number_format( $r[ 'PRICE' ], 2 ) . '</button>';
					}
				else
				$r[ 'ADD_TO_CART_BUTTON' ] = 'Price unknown, unable to query remote server.';
				}

			if ( using_bootstrap() )
				{
				switch ( $row_class )
				{
					case 'ui-state-highlight':
						$row_class = 'alert alert-success';
						break;
					case 'ui-state-error':
						$row_class = 'alert alert-error';
						break;
					case 'freeplugin':
						$row_class = 'alert alert-info';
						break;
					default :
						$row_class = '';
						break;
				}

				}
			$r[ 'ROWCLASS' ] = $row_class;

			$r[ 'STYLE' ] = $style;

			$jomresdotnet_plugins[ ] = $r;
			}


		$output[ 'PLUGINS_TO_UPGRADE' ] = implode( ",", $plugins_needing_upgrading );

		if ( $this->key_valid ) $plugins_require_upgrade[ ][ 'upgrade_text' ] = 'Upgrade all Core plugins';


		$pageoutput[ ] = $output;
		$tmpl          = new patTemplate();
		$tmpl->setRoot( JOMRES_TEMPLATEPATH_ADMINISTRATOR );
		$tmpl->addRows( 'pageoutput', $pageoutput );
		$tmpl->addRows( 'bronze_users', $bronze_users );
		$tmpl->addRows( 'thirdpartyplugins', $thirdpartyplugins );
		$tmpl->addRows( 'jomresdotnet_plugins', $jomresdotnet_plugins );
		$tmpl->addRows( 'plugins_require_upgrade', $plugins_require_upgrade );

		$tmpl->readTemplatesFromInput( 'plugin_manager.html' );
		$tmpl->displayParsedTemplate();
		}

	function set_main_plugins()
		{
		$this->main_plugins    = array ();
		$this->main_plugins[ ] = "advanced_micromanage_tariff_editing_modes";
		$this->main_plugins[ ] = "black_bookings";
		$this->main_plugins[ ] = "book_guest_in_out";
		$this->main_plugins[ ] = "commission";
		$this->main_plugins[ ] = "core_gateway_paypal";
		$this->main_plugins[ ] = "coupons";
		$this->main_plugins[ ] = "custom_fields";
		$this->main_plugins[ ] = "guest_types";
		$this->main_plugins[ ] = "lastminute_config_tab";
		$this->main_plugins[ ] = "optional_extras";
		$this->main_plugins[ ] = "partners";
		$this->main_plugins[ ] = "property_creation_plugins";
		$this->main_plugins[ ] = "sms_clickatell";
		$this->main_plugins[ ] = "subscriptions";
		$this->main_plugins[ ] = "template_editing";
		$this->main_plugins[ ] = "wiseprice_config_tab";
		$this->main_plugins[ ] = "alternative_init";
		$this->main_plugins[ ] = "jomres_asamodule";
		}

	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}
