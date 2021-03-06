<?php
/**
* Core file
* @author Vince Wooll <sales@jomres.net>
* @version Jomres 7
* @package Jomres
* @copyright	2005-2012 Vince Wooll
* Jomres (tm) PHP files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly, however all images, css and javascript which are copyright Vince Wooll are not GPL licensed and are not freely distributable. 
**/


// ################################################################
defined( '_JOMRES_INITCHECK' ) or die( '' );
// ################################################################


class j00005ajax_search_asamodule_report {
	function j00005ajax_search_asamodule_report($componentArgs)
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return 
		$MiniComponents =jomres_singleton_abstract::getInstance('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=false; return;
			}
		if (get_showtime('task') != "asamodule_report")
			return;
		$showtime = jomres_singleton_abstract::getInstance('showtime');
		
		$asamodule_plugin_information = get_showtime('asamodule_plugin_information');
		
		$asamodule_plugin_information['j06000ajax_search'] = 
			array(
				"asamodule_task"=>"ajax_search",
				"asamodule_info"=>"Interface to the Jomres ajax search plugin. Mainly this is a supporting component for Ajax search composite, but it can be used on it's own. Requires there to be a div on the page with the id of 'asamodule_search_results' for it to populate the results with.",
				"asamodule_example_link"=>JOMRES_SITEPAGE_URL_NOSEF.'&tmpl=component&topoff=1&task=ajax_search',
				"asamodule_manual_link"=>''
				);
		set_showtime('asamodule_plugin_information',$asamodule_plugin_information);
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