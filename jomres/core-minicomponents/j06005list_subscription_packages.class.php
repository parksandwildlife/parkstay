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

class j06005list_subscription_packages
	{
	function j06005list_subscription_packages()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = false;

			return;
			}
		$thisJRUser = jomres_singleton_abstract::getInstance( 'jr_user' );
		$siteConfig = jomres_singleton_abstract::getInstance( 'jomres_config_site_singleton' );
		$jrConfig   = $siteConfig->get();
		$task       = get_showtime( 'task' );
		if ( !$thisJRUser->superPropertyManager && $jrConfig[ 'useSubscriptions' ] == "1" )
			{
			if ( $thisJRUser->accesslevel == 2 && ( strlen( $task ) == 0 || $task == "list_subscription_packages" || $task == "listyourproperties" || $task == "publishProperty" ) )
				{
				$allowedProperties  = subscribers_getAvailablePropertySlots( $thisJRUser->id );
				$existingProperties = subscribers_getManagersPublishedProperties( $thisJRUser->id );
				echo jr_gettext( '_JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES1', _JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES1, false ) . $allowedProperties . jr_gettext( '_JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES2', _JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES2, false );
				echo jr_gettext( '_JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES4', _JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES4, false ) . count( $existingProperties ) . jr_gettext( '_JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES5', _JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES5, false );
				if ( $allowedProperties == $existingProperties ) echo jr_gettext( '_JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES3', _JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES3, false );
				if ( $task != "listyourproperties" ) echo jr_gettext( '_JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES6', _JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES6, false );
				if ( $task != "list_subscription_packages" ) echo jr_gettext( '_JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES7', _JRPORTAL_SUBSCRIBERS_AVAILABLE_PROPERTIES7, false );
				}
			}
		$subscribeIcon = '<IMG SRC="' . get_showtime( 'live_site' ) . '/jomres/images/jomresimages/small/EditItem.png" border="0" alt="editicon">';
		$packages      = subscriptions_packages_getallpackages();
		$output        = array ();
		$pageoutput    = array ();
		$rows          = array ();

		$output[ 'PAGETITLE' ]      = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_TITLE', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_TITLE, false );
		$output[ 'HNAME' ]          = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_NAME', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_NAME, false );
		$output[ 'HDESCRIPTION' ]   = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_DESCRIPTION', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_DESCRIPTION, false );
		$output[ 'HPUBLISHED' ]     = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_PUBLISHED', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_PUBLISHED, false );
		$output[ 'HFREQUENCY' ]     = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_FREQUENCY', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_FREQUENCY, false );
		$output[ 'HTRIALPERIOD' ]   = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_TRIALPERIOD', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_TRIALPERIOD, false );
		$output[ 'HTRIALAMOUNT' ]   = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_TRIALAMOUNT', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_TRIALAMOUNT, false );
		$output[ 'HFULLAMOUNT' ]    = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_FULLAMOUNT', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_FULLAMOUNT, false );
		$output[ 'HROOMSLIMIT' ]    = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_ROOMSLIMIT', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_ROOMSLIMIT, false );
		$output[ 'HPROPERTYLIMIT' ] = jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_PROPERTYLIMIT', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_PROPERTYLIMIT, false );

		foreach ( $packages as $package )
			{
			$r                    = array ();
			$r[ 'ID' ]            = $package[ 'id' ];
			$r[ 'NAME' ]          = jr_gettext( '_JOMRES_CUSTOMTEXT_SUBSCRIPTIONPACKAGES_NAME' . $package[ 'id' ], stripslashes( $package[ 'name' ] ), false, false );
			$r[ 'DESCRIPTION' ]   = jr_gettext( '_JOMRES_CUSTOMTEXT_SUBSCRIPTIONPACKAGES_DESC' . $package[ 'id' ], stripslashes( $package[ 'description' ] ), false, false );
			$r[ 'PUBLISHED' ]     = $package[ 'published' ];
			$r[ 'FREQUENCY' ]     = $package[ 'frequency' ];
			$r[ 'TRIALPERIOD' ]   = $package[ 'trial_period' ];
			$r[ 'TRIALAMOUNT' ]   = output_price( $package[ 'trial_amount' ], $jrConfig[ 'globalCurrencyCode' ] );
			$r[ 'FULLAMOUNT' ]    = output_price( $package[ 'full_amount' ], $jrConfig[ 'globalCurrencyCode' ] );
			$r[ 'ROOMSLIMIT' ]    = $package[ 'rooms_limit' ];
			$r[ 'PROPERTYLIMIT' ] = $package[ 'property_limit' ];
			$r[ 'SUBSCRIBE' ]     = '<a href="' . JOMRES_SITEPAGE_URL . '&task=subscribe&id=' . $package[ 'id' ] . '">' . jr_gettext( '_JRPORTAL_SUBSCRIPTIONS_PACKAGES_SUBSCRIBE', _JRPORTAL_SUBSCRIPTIONS_PACKAGES_SUBSCRIBE, false ) . '</a>';
			$rows[ ]              = $r;
			}

		$pageoutput[ ] = $output;
		$tmpl          = new patTemplate();
		$tmpl->setRoot( JOMRES_TEMPLATEPATH_FRONTEND );
		$tmpl->readTemplatesFromInput( 'frontend_list_subscription_packages.html' );
		$tmpl->addRows( 'pageoutput', $pageoutput );
		$tmpl->addRows( 'rows', $rows );
		$tmpl->displayParsedTemplate();
		}


	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}