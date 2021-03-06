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
 *
 #
* @package Jomres
#
 */
class j02310regprop2 {
	/**
	#
	 * Constructor:
	#
	 */
	function j02310regprop2()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=true; return;
			}
		$thisJRUser=jomres_getSingleton('jr_user');
		$mrConfig=getPropertySpecificSettings();
		$siteConfig = jomres_getSingleton('jomres_config_site_singleton');
		$jrConfig=$siteConfig->get();
		if (!subscribers_checkUserHasSubscriptionsToCreateNewProperty() && !$thisJRUser->superPropertyManager && $jrConfig['useSubscriptions']=="1" )
			jomresRedirect( JOMRES_SITEPAGE_URL."&task=list_subscription_packages","");

		if ($jrConfig['selfRegistrationAllowed']=="0" && !$thisJRUser->superPropertyManager)
			return;

		$output=array();
		$output['APIKEY']=$jrConfig['google_maps_api_key'];
		$property_region				= jomresGetParam( $_POST, 'region', 0 );
		$property_country				= jomresGetParam( $_POST, 'country', "" );
		if (isset($_REQUEST['xyz']))
			$property_country				= jomresGetParam( $_POST, 'xyz', "" );

		$realestate						= jomresGetParam( $_POST, 'management_process', '' );
		$property_type					= intval(jomresGetParam( $_POST, 'propertyType', "" ));
		
		$propertyFeatures="";
		
		
		$query = "SELECT roomtype_id FROM #__jomres_roomtypes_propertytypes_xref WHERE propertytype_id = ".(int)$property_type;
		$rts = doSelectSql($query);
		$room_types = array();
		if (count($rts)>0)
			{
			foreach($rts as $rt)
				{
				$room_types[]=$rt->roomtype_id;
				}
			}
		$gor=genericOr($room_types,'room_classes_uid');
		$query = "SELECT room_classes_uid,room_class_abbv FROM #__jomres_room_classes WHERE property_uid = 0 AND `srp_only` = '1' AND ".$gor." ORDER BY room_class_abbv ";
		$roomClasses=doSelectSql($query);
		$dropDownList ="<select class=\"inputbox\" name=\"roomClass\">";
		$dropDownList .= "<option value=\"\"></option>";
		foreach ($roomClasses as $roomClass)
			{
			$selected="";
			$room_classes_uid=$roomClass->room_classes_uid;
			$room_class_abbv=jr_gettext('_JOMRES_CUSTOMTEXT_ROOMTYPES_ABBV'.$roomClass->room_classes_uid,$roomClass->room_class_abbv,false,false ); 
			$dropDownList .= "<option ".$selected." value=\"".$room_classes_uid."\">".$room_class_abbv."</option>";
			}
		$dropDownList.="</select>";

		$output['ISTHISANMRP_DROPDOWN']='<select id="mySelect" name="mrpsrp">
			<option value="MRP" selected>MRP</option>
			<option value="showIfClicked">SRP</option>
			</select>
		<div id="showIfClicked">
			'.$dropDownList.'
		</div>';
		
		$output['ISTHISANMRP']=jr_gettext('_JOMRES_COM_ISTHISANMRP',_JOMRES_COM_ISTHISANMRP,false);
		$output['ISTHISANMRP_DESC']=jr_gettext('_JOMRES_COM_ISTHISANMRP_DESC',_JOMRES_COM_ISTHISANMRP_DESC,false);
		
		$output['LAT']=$jrConfig['default_lat'];
		$output['LONG']=$jrConfig['default_long'];

		$componentArgs=array('property_uid'=>999999999,"width"=>'400',"height"=>'400',"editing_map"=>true);
		$MiniComponents->specificEvent('01050','x_geocoder',$componentArgs);
		$output['MAP'] = $MiniComponents->miniComponentData['01050']['x_geocoder'];

		$starsDropDownList="<select name=\"stars\">";
		for ($i=0, $n=7; $i <= $n; $i++)
			{
			$starsDropDownList .= "<option value=\"".$i."\">".$i."</option>";
			}
		$starsDropDownList.="</select>";


		//$propertyFeaturesArray=explode(",",$propertyFeatures);
		$globalPfeatures=array();
		
		$query = "SELECT hotel_features_uid,hotel_feature_abbv,hotel_feature_full_desc,image,ptype_xref FROM #__jomres_hotel_features WHERE property_uid = '0' ORDER BY hotel_feature_abbv ";
		$propertyFeaturesList=doSelectSql($query);
		if (count($propertyFeaturesList)>0)
			{
			foreach($propertyFeaturesList as $propertyFeature)
				{
				if (is_numeric($propertyFeature->ptype_xref))
					{
					if ($propertyFeature->ptype_xref == $property_type || $propertyFeature->ptype_xref == 0)
						{
						$r=array();
						$r['PID']=$propertyFeature->hotel_features_uid;
						//if (in_array($propertyFeature->hotel_features_uid,$propertyFeaturesArray) )
							//$r['ischecked']="checked";
						//$r['FEATURE']=makeFeatureImages($propertyFeature->image,$propertyFeature->hotel_feature_abbv,$propertyFeature->hotel_feature_full_desc,$retString=TRUE);
						
						$feature_abbv = jr_gettext('_JOMRES_CUSTOMTEXT_FEATURES_ABBV'.(int)$propertyFeature->hotel_features_uid,$propertyFeature->hotel_feature_abbv,false,false);
						$feature_desc = jr_gettext('_JOMRES_CUSTOMTEXT_FEATURES_DESC'.(int)$propertyFeature->hotel_features_uid,$propertyFeature->hotel_feature_full_desc,false,false);
					
						$r['FEATURE']=jomres_makeTooltip($feature_abbv,$feature_abbv,$feature_desc,$propertyFeature->image,"","property_feature",array());
						$globalPfeatures[]=$r;
						}
					}
				else
					{
					$ptype_xref=unserialize($propertyFeature->ptype_xref);
					if (count($ptype_xref)>0)
						{
						if (in_array ($property_type, $ptype_xref))
							{
							$r=array();
							$r['PID']=$propertyFeature->hotel_features_uid;
							//if (in_array($propertyFeature->hotel_features_uid,$propertyFeaturesArray) )
								//$r['ischecked']="checked";
							//$r['FEATURE']=makeFeatureImages($propertyFeature->image,$propertyFeature->hotel_feature_abbv,$propertyFeature->hotel_feature_full_desc,$retString=TRUE);
							
							$feature_abbv = jr_gettext('_JOMRES_CUSTOMTEXT_FEATURES_ABBV'.(int)$propertyFeature->hotel_features_uid,$propertyFeature->hotel_feature_abbv,false,false);
							$feature_desc = jr_gettext('_JOMRES_CUSTOMTEXT_FEATURES_DESC'.(int)$propertyFeature->hotel_features_uid,$propertyFeature->hotel_feature_full_desc,false,false);
						
							$r['FEATURE']=jomres_makeTooltip($feature_abbv,$feature_abbv,$feature_desc,$propertyFeature->image,"","property_feature",array());
							$globalPfeatures[]=$r;
							}
						}
					}
				}
			}
		
		//if (isset($listTxt))
			//$output['FEATURES']=$listTxt;

		$output['STARSDROPDOWN']=$starsDropDownList;

		$output['PROPERTYTYPE']						=$property_type;
		$output['PROPERTY_NAME']					="";
		$output['PROPERTY_STREET']					="";
		$output['PROPERTY_TOWN']					="";
		$output['PROPERTY_POSTCODE']				="";
		$output['PROPERTY_TEL']						="";
		$output['PROPERTY_FAX']						="";
		$output['PROPERTY_EMAIL']					="";
		$output['PROPERTY_MAPPINGLINK']				="";
		
		if ($jrConfig['allowHTMLeditor']=="1")
			{
			$hiddenField="property_description";
			$width="95%";
			$height="250";
			$col="20";
			$row="10";
			$output['PROPERTY_DESCRIPTION']=editorAreaText( 'property_description',$property_description, 'property_description', $width, $height, $col, $row );
			$output['PROPERTY_CHECKIN_TIMES']=editorAreaText( 'property_checkin_times', $property_checkintimes, 'property_checkin_times', $width, $height, $col, $row );
			$output['PROPERTY_AREA_ACTIVITIES']=editorAreaText( 'property_area_activities', $property_areaactivities, 'property_area_activities', $width, $height, $col, $row );
			$output['PROPERTY_DRIVING_DIRECTIONS']=editorAreaText( 'property_driving_directions', $property_drivingdirections, 'property_driving_directions', $width, $height, $col, $row );
			$output['PROPERTY_AIRPORTS']=editorAreaText( 'property_airports', $property_airports, 'property_airports', $width, $height, $col, $row );
			$output['PROPERTY_OTHERTRANSPORT']=editorAreaText( 'property_othertransport', $property_othertransport, 'property_othertransport', $width, $height, $col, $row );
			$output['PROPERTY_POLICIES_DISCLAIMERS']=editorAreaText( 'property_policies_disclaimers', $property_policiesdisclaimers, 'property_policies_disclaimers', $width, $height, $col, $row );
			}
		else
			{
			$output['PROPERTY_DESCRIPTION']='<textarea class="inputbox" cols="70" rows="5" name="property_description">'.$property_description.'</textarea>';
			$output['PROPERTY_CHECKIN_TIMES']='<textarea class="inputbox" cols="70" rows="5" name="property_checkin_times">'.$property_checkintimes.'</textarea>';
			$output['PROPERTY_AREA_ACTIVITIES']='<textarea class="inputbox" cols="70" rows="5" name="property_area_activities">'.$property_areaactivities.'</textarea>';
			$output['PROPERTY_DRIVING_DIRECTIONS']='<textarea class="inputbox" cols="70" rows="5" name="property_driving_directions">'.$property_drivingdirections.'</textarea>';
			$output['PROPERTY_AIRPORTS']='<textarea class="inputbox" cols="70" rows="5" name="property_airports">'.$property_airports.'</textarea>';
			$output['PROPERTY_OTHERTRANSPORT']='<textarea class="inputbox" cols="70" rows="5" name="property_othertransport">'.$property_othertransport.'</textarea>';
			$output['PROPERTY_POLICIES_DISCLAIMERS']='<textarea class="inputbox" cols="70" rows="5" name="property_policies_disclaimers">'.$property_policiesdisclaimers.'</textarea>';
			}
		
		$output['PROPERTY_REGION']					=$property_region;
		$output['PROPERTY_COUNTRY']					=$property_country;
		$output['HPRICE']							=jr_gettext('_JOMRES_COM_MR_EXTRA_PRICE',_JOMRES_COM_MR_EXTRA_PRICE,false);
		$output['_JOMRES_REGISTRATION_STEP_2_OF_2']=jr_gettext('_JOMRES_REGISTRATION_STEP_2_OF_2',_JOMRES_REGISTRATION_STEP_2_OF_2,false);
		
		$output['INPUTBOXERRORBORDER']				=$mrConfig['inputBoxErrorBorder'];
		$output['INPUTBOXERRORBACKGROUND']			=$mrConfig['inputBoxErrorBackground'];
		$output['JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_2']				=jr_gettext('_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_2',_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_2,false);
		$output['JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_2_BUTTON']		=jr_gettext('_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_2',_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_2,false,false);
		$output['JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_1']				=jr_gettext('_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_1',_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_1,false);

		$output['HNAME']				=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_NAME',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_NAME,false);
		$output['HSTREET']				=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_STREET',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_STREET,false);
		$output['HTOWN']				=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TOWN',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TOWN,false);
		$output['HPOSTCODE']			=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POSTCODE',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POSTCODE,false);
		$output['HTELEPHONE']			=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TELEPHONE',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TELEPHONE,false);
		$output['HFAX']					=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FAX',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FAX,false);
		$output['HEMAIL']				=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_EMAIL',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_EMAIL,false);
		$output['HSTARS']				=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_STARS',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_STARS,false);
		$output['HFEATURES']			=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FEATURES',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FEATURES,false);
		$output['HMAPPINGLINK']			=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_MAPPINGLINK',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_MAPPINGLINK,false);
		$output['HPROPDESCRIPTION']		=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_PROPDESCRIPTION',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_PROPDESCRIPTION,false);
		$output['HCHECKINTIMES']		=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_CHECKINTIMES',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_CHECKINTIMES,false);
		$output['HAREAACTIVITIES']		=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_AREAACTIVITIES',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_AREAACTIVITIES,false);
		$output['HDRIVINGDIRECTIONS']	=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_DRIVINGDIRECTIONS',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_DRIVINGDIRECTIONS,false);
		$output['HAIRPORTS']			=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_AIRPORTS',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_AIRPORTS,false);
		$output['HOTHERTRANSPORT']		=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_OTHERTRANSPORT',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_OTHERTRANSPORT,false);
		$output['HPOLICIESDISCLAIMERS']	=jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POLICIESDISCLAIMERS',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POLICIESDISCLAIMERS,false);
		$output['MOSCONFIGLIVESITE']	=get_showtime('live_site');
		$output['LATLONG_DESC']			=jr_gettext('_JOMRES_LATLONG_DESC',_JOMRES_LATLONG_DESC,false);
		$output['PAGETITLE']			=jr_gettext('_JOMRES_COM_MR_VRCT_TAB_PROPERTYS',_JOMRES_COM_MR_VRCT_TAB_PROPERTYS,false);
		$output['JOMRES_SITEPAGE_URL']	=JOMRES_SITEPAGE_URL;
		$output['_JOMRES_REVIEWS_SUBMIT']=jr_gettext('_JOMRES_REVIEWS_SUBMIT',_JOMRES_REVIEWS_SUBMIT,false);
		
		$pageoutput[]=$output;
		$tmpl = new patTemplate();
		$tmpl->setRoot( JOMRES_TEMPLATEPATH_BACKEND );
		if ($realestate =='realestate')
			$tmpl->readTemplatesFromInput( 'register_property2_realestate.html');
		else
			$tmpl->readTemplatesFromInput( 'register_property2.html');
		$tmpl->addRows( 'pageoutput',$pageoutput);
		if ($jrConfig['useGlobalPFeatures']=="1")
			$tmpl->addRows( 'globalPfeatures',$globalPfeatures);
		$tmpl->displayParsedTemplate();
		}

	function touch_template_language()
		{
		$output=array();

		$output[]			= jr_gettext('_JOMRES_COM_ISTHISANMRP',_JOMRES_COM_ISTHISANMRP);
		$output[]			= jr_gettext('_JOMRES_COM_ISTHISANMRP_DESC',_JOMRES_COM_ISTHISANMRP_DESC);
		$output[]			= jr_gettext('_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_2',_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_2);
		$output[]			= jr_gettext('_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_1',_JOMRES_REGISTRATION_INSTRUCTIONS_STEP2_1);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_NAME',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_NAME);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_STREET',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_STREET);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TOWN',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TOWN);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POSTCODE',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POSTCODE);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TELEPHONE',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TELEPHONE);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FAX',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FAX);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_EMAIL',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_EMAIL);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_STARS',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_STARS);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FEATURES',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FEATURES);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_MAPPINGLINK',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_MAPPINGLINK);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_PROPDESCRIPTION',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_PROPDESCRIPTION);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_CHECKINTIMES',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_CHECKINTIMES);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_AREAACTIVITIES',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_AREAACTIVITIES);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_DRIVINGDIRECTIONS',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_DRIVINGDIRECTIONS);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_AIRPORTS',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_AIRPORTS);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_OTHERTRANSPORT',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_OTHERTRANSPORT);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POLICIESDISCLAIMERS',_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POLICIESDISCLAIMERS);
		$output[]			= jr_gettext('_JOMRES_COM_MR_VRCT_TAB_PROPERTYS',_JOMRES_COM_MR_VRCT_TAB_PROPERTYS);

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