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
 * Configuration panel for gateways
#
 *
 * @package Jomres
#
 */
class j00501gateways
	{
	/**
	#
	 * Constructor: Outputs configuration options for gateways
	#
	 */
	function j00501gateways( $componentArgs )
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = false;

			return;
			}
		$mrConfig = getPropertySpecificSettings();
		if ( $mrConfig[ 'is_real_estate_listing' ] == 1 ) return;
		$configurationPanel = $componentArgs[ 'configurationPanel' ];
		$paypal_settings    = jomres_singleton_abstract::getInstance( 'jrportal_paypal_settings' );
		$paypal_settings->get_paypal_settings();

		if ( $paypal_settings->paypalConfigOptions[ 'override' ] == "0" )
			{
			$lists        = $componentArgs[ 'lists' ];
			$gatewayNames = $componentArgs[ 'gatewayNames' ];

			$configurationPanel->startPanel( jr_gettext( "_JOMRES_COM_A_GATEWAYLIST", _JOMRES_COM_A_GATEWAYLIST, false ) );

			$configurationPanel->setleft( jr_gettext( "_JOMRES_COM_A_GATEWAY_USEGATEWAYS", _JOMRES_COM_A_GATEWAY_USEGATEWAYS, false ) );
			$configurationPanel->setmiddle( $lists[ 'useOnlinepayment' ] );
			$configurationPanel->setright( jr_gettext( "_JOMRES_COM_A_GATEWAY_USEGATEWAYS_DESC", _JOMRES_COM_A_GATEWAY_USEGATEWAYS_DESC, false ) );
			$configurationPanel->insertSetting();

			$configurationPanel->setleft();
			$configurationPanel->setmiddle();
			$configurationPanel->setright( jr_gettext( "_JOMRES_COM_A_GATEWAY_ENABLED", _JOMRES_COM_A_GATEWAY_ENABLED, false ) );
			$configurationPanel->insertSetting();

			$MiniComponents->triggerEvent( '00509', $componentArgs );

			$outputArray = $MiniComponents->miniComponentData[ '00509' ];
			foreach ( $outputArray as $gw )
				{
				$configurationPanel->setleft( $gw[ 'button' ] );
				$configurationPanel->setmiddle( $gw[ 'link' ] );
				$configurationPanel->setright( $gw[ 'active' ] );
				$configurationPanel->insertSetting();
				}
			$configurationPanel->endPanel();
			}
		}

	/**
	#
	 * Must be included in every mini-component
	#
	 * Returns any settings the the mini-component wants to send back to the calling script. In addition to being returned to the calling script they are put into an array in the mcHandler object as eg. $mcHandler->miniComponentData[$ePoint][$eName]
	#
	 */
	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}

?>