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

/**
#
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
#
 *
 * @package Jomres
#
 */
class j00010reception_option_08_bookaguestout
	{

	/**
	#
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	#
	 */
	function j00010reception_option_08_bookaguestout( $componentArgs )
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = false;

			return;
			}
		$property_uid = getDefaultProperty();
		$mrConfig     = getPropertySpecificSettings( $property_uid );
		if ( $mrConfig[ 'is_real_estate_listing' ] == 1 ) return;
		$this->cpanelButton = jomres_mainmenu_option( jomresURL( JOMRES_SITEPAGE_URL . "&task=bookGuestOut" ), 'BookGuestOut.png', $mrConfig[ 'wholeday_booking' ] == "1" ? jr_gettext( '_JOMRES_FRONT_MR_MENU_ADMIN_BOOKAGUESTOUT_WHOLEDAY', _JOMRES_FRONT_MR_MENU_ADMIN_BOOKAGUESTOUT_WHOLEDAY, false, false ) : jr_gettext( '_JOMRES_FRONT_MR_MENU_ADMIN_BOOKAGUESTOUT', _JOMRES_FRONT_MR_MENU_ADMIN_BOOKAGUESTOUT, false, false ), null, jr_gettext( "_JOMRES_CUSTOMCODE_JOMRESMAINMENU_RECEPTION_PMS", _JOMRES_CUSTOMCODE_JOMRESMAINMENU_RECEPTION_PMS, false, false ), false, true );
		}


	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return $this->cpanelButton;
		}
	}

?>