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

jr_import( 'jomres_dashboard' );


class j00013dashboard
	{
	function j00013dashboard()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = true;

			return;
			}

		$property_uid = getDefaultProperty();
		$result       = $MiniComponents->specificEvent( '06001', 'dashboard', array ( 'property_uid' => $property_uid, 'show_legend' => true, 'show_date_dropdown' => true ) );
		echo $result;
		}


	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}

?>