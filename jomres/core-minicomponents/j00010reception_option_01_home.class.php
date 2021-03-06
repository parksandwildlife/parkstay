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
class j00010reception_option_01_home
	{

	/**
	#
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	#
	 */
	function j00010reception_option_01_home( $componentArgs )
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = true;

			return;
			}
		$this->cpanelButton = jomres_mainmenu_option( JOMRES_SITEPAGE_URL . "&task=dashboard", 'Home.png', jr_gettext( '_JOMRES_FRONT_MR_MENU_ADMIN_HOME', _JOMRES_FRONT_MR_MENU_ADMIN_HOME, false, false ), null, jr_gettext( "_JOMRES_CUSTOMCODE_JOMRESMAINMENU_RECEPTION_HOME", _JOMRES_CUSTOMCODE_JOMRESMAINMENU_RECEPTION_HOME, false, false ) );
		}

	function touch_template_language()
		{
		$output = array ();

		$output[ ] = jr_gettext( "_JOMRES_CUSTOMCODE_JOMRESMAINMENU_RECEPTION_HOME", _JOMRES_CUSTOMCODE_JOMRESMAINMENU_RECEPTION_HOME );

		foreach ( $output as $o )
			{
			echo $o;
			echo "<br/>";
			}
		}

	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return $this->cpanelButton;
		}
	}

?>