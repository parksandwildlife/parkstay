<?php
/**
 * Core file
 * @author Vince Wooll <sales@jomres.net>
 * @version Jomres 4 
* @package Jomres
* @copyright	2005-2011 Vince Wooll
* Jomres (tm) PHP files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly, however all images, css and javascript which are copyright Vince Wooll are not GPL licensed and are not freely distributable. 
**/

// ################################################################
defined( '_JOMRES_INITCHECK' ) or die( '' );
// ################################################################

/**
#
 * Configuration panel for the paypal gateway
 #
* @package Jomres
#
 */
class j00510paypal {
	/**
	#
	 * Constructor: collects data for and displays the paypal gateway configuration popup
	#
	 */
	function j00510paypal()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return 
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=true; return;
			}
		$plugin="paypal";
		$button="<IMG SRC=\"".get_showtime('eLiveSite')."j00510".$plugin.".gif\" border=\"0\">";
		$defaultProperty=getDefaultProperty();

		// Default settings
		$settingArray=array();
		$settingArray['active']="0";
		$settingArray['usesandbox']="1";
		$settingArray['useipn']="1";
		$settingArray['pendingok']="0";
		$settingArray['receiveIPNemail']="";
		$settingArray['currencycode']="GBP";
		$settingArray['paypalemail']="";

		$yesno = array();
		$yesno[] = jomresHTML::makeOption( '0', jr_gettext('_JOMRES_COM_MR_NO',_JOMRES_COM_MR_NO,FALSE) );
		$yesno[] = jomresHTML::makeOption( '1', jr_gettext('_JOMRES_COM_MR_YES',_JOMRES_COM_MR_YES,FALSE) );

		
		$query="SELECT setting,value FROM #__jomres_pluginsettings WHERE prid = '".(int)$defaultProperty."' AND plugin = '$plugin' ";
		$settingsList=doSelectSql($query);
		foreach ($settingsList as $set)
			{
			$settingArray[$set->setting]=$set->value;
			}
//var_dump($settingsList);
		$output['GATEWAYNAME']=ucwords($plugin);
		$output['GATEWAYLOGO']=$button;
		$output['GATEWAY']=$plugin;
		$output['JR_GATEWAY_CONFIG_ACTIVE']=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_ACTIVE'.$plugin,"Active");
		$output['ACTIVE'] = jomresHTML::selectList( $yesno, 'active', 'class="inputbox" size="1"', 'value', 'text', $settingArray['active'] );

		$output['JR_GATEWAY_CONFIG_SANDBOX']=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_SANDBOX'.$plugin,'Use sandbox?');
		$output['JR_GATEWAY_CONFIG_SANDBOX_DESC']=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_SANDBOX_DESC'.$plugin,'Set this to Yes if you want to use the paypal sandbox');
		$output['JR_GATEWAY_CONFIG_PAYPAL_EMAIL']=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_PAYPAL_EMAIL'.$plugin,'Your paypal email address');
		$output['JR_GATEWAY_CONFIG_NOTES']=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_NOTES'.$plugin,'Note: To use paypal you must go to your paypal account & disable Autoreturn. (Profile/Website Payment Preferences), and set IPN (Profile/Instant Payment Notification Preferences)to on URL of:<b>&nbsp;').JOMRES_SITEPAGE_URL_NOSEF."&task=completebk&plugin=paypal&action=ipn</b>";
		$output['JR_GATEWAY_CONFIG_PAYPAL_USEIPN']=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_PAYPAL_USEIPN'.$plugin,'Use IPN?');

		$output['JR_GATEWAY_CONFIG_PAYPAL_PENDINGOK']=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_PENDINGOK'.$plugin,'Pending ok?');
		$output['JR_GATEWAY_CONFIG_PAYPAL_PENDINGOK_DESC']=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_PENDINGOK_DESC'.$plugin,'In some instances it is acceptable to receive a payment status of "Pending". ');
		$output['JR_GATEWAY_CONFIG_PAYPAL_IPNEMAIL']=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_IPNEMAIL'.$plugin,'Receive IPN email from Jomres?');
		$output['JR_GATEWAY_CONFIG_PAYPAL_IPNEMAIL_DESC']=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_IPNEMAIL_DESC'.$plugin,'Set this to Yes to receive an IPN email with a dump of the transaction. Note, only for servers that use the php mail function.');
		$output['JR_GATEWAY_CONFIG_PAYPAL_CURRENCYCODE']=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_CURRENCYCODE'.$plugin,'Currency code (eg EUR) ');

		$output['ACTIVE'] = jomresHTML::selectList( $yesno, 'active', 'class="inputbox" size="1"', 'value', 'text', $settingArray['active'] );
		$output['USESANDBOX'] = jomresHTML::selectList( $yesno, 'usesandbox', 'class="inputbox" size="1"', 'value', 'text', $settingArray['usesandbox'] );
		$output['PAYPALEMAIL'] = $settingArray['paypalemail'];
		$output['USEIPN'] = jomresHTML::selectList( $yesno, 'useipn', 'class="inputbox" size="1"', 'value', 'text', $settingArray['useipn'] );
		$output['PENDINGOK'] = jomresHTML::selectList( $yesno, 'pendingok', 'class="inputbox" size="1"', 'value', 'text', $settingArray['pendingok'] );
		$output['RECEIVEIPNEMAIL'] = jomresHTML::selectList( $yesno, 'receiveIPNemail', 'class="inputbox" size="1"', 'value', 'text', $settingArray['receiveIPNemail'] );
		$output['CURRENCYCODE'] = $settingArray['currencycode'] ;

		$pageoutput[]=$output;
		$tmpl = new patTemplate();
		$tmpl->setRoot( get_showtime('ePointFilepath') );
		$tmpl->readTemplatesFromInput( 'j00510'.$plugin.'.html' );
		$tmpl->addRows( 'edit_gateway', $pageoutput );
		$tmpl->displayParsedTemplate();
		}

	function touch_template_language()
		{
		$output=array();
		$plugin="paypal";
		
		$output[]		=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_ACTIVE'.$plugin,"Active");
		$output[]		=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_SANDBOX'.$plugin,'Use sandbox?');
		$output[]		=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_SANDBOX_DESC'.$plugin,'Set this to Yes if you want to use the paypal sandbox');
		$output[]		=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_PAYPAL_EMAIL'.$plugin,'Your paypal email address');
		$output[]		=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_NOTES'.$plugin,'Note: To use paypal you must go to your paypal account & disable Autoreturn. (Profile/Website Payment Preferences), and set IPN (Profile/Instant Payment Notification Preferences)to on URL of:');
		$output[]		=jr_gettext('_JOMRES_CUSTOMTEXT_GATEWAY_CONFIG_PAYPAL_USEIPN'.$plugin,'Use IPN?');
		$output[]		=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_PENDINGOK'.$plugin,'Pending ok?');
		$output[]		=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_PENDINGOK_DESC'.$plugin,'In some instances it is acceptable to receive a payment status of "Pending". ');
		$output[]		=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_IPNEMAIL'.$plugin,'Receive IPN email from Jomres?');
		$output[]		=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_IPNEMAIL_DESC'.$plugin,'Set this to Yes to receive an IPN email with a dump of the transaction. Note, only for servers that use the php mail function.');
		$output[]		=jr_gettext('_JOMRES_JR_GATEWAY_CONFIG_PAYPAL_CURRENCYCODE'.$plugin,'Currency code (eg EUR) ');

		foreach ($output as $o)
			{
			echo $o;
			echo "<br/>";
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