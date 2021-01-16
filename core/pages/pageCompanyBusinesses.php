<?php
function ShowBusinessSubpage($company, $subpage, $subtype, $subid, $action, $lang, $MyDB, $businesses){
	$content = '';
	$content .= '<div class="block">';

	//Left Box
	$content .= '<div class="col-xs-12 col-sm-5 col-md-5 nopadding">';
	$content .= '<ul style="padding:0 10px; list-style-type: none;">';
	$currentBusiness=null;
	
	for ($i=0;$i<count($businesses);$i++){
		$content .= BusinessListEntry($businesses[$i]);
		if($businesses[$i]['busid']== $subid)
				$currentBusiness = $businesses[$i];
	}
	$content .= '</ul>';
	$content .= '</div>';
	
	//Right Box
	$content .= '<div class="col-xs-12 col-sm-7 col-md-7 nopadding">';
	$content .= '<div style="padding:0 10px;">';

	//Top Entry
	$content .= '<div class="block">';
	
	$content .= '<div class="block">';
	$content .= '<div style="float: left;">';
	$content .= '<h3>'.$lang['bus_'.$subtype].'</h3>';
	$content .= '<div style="font-size: 11px;">'.$lang['running_cost'].': '.GetPriceNumber(12456).'</div>';
	$content .= '</div>';

	//Area
	$content .= '<div style="margin-top: 4px;float: right; font-size: 11px;">';
	$content .= '<div class="right" style="margin-bottom: -2px;">'.$lang['area_total'].'</div>';
	$content .= '<div class="right">'.GetArea(2420).'</div>';
	$content .= '<div class="right" style="margin-top: 2px; margin-bottom: -2px;">'.$lang['area_free'].'</div>';
	$content .= '<div class="right">'.GetArea(20).'</div>';
	$content .= '</div>';
	
	$content .= '</div>';	//Block

	//Details
	$content .= '<div class="block" style="margin-top:8px;">';
	$content .= '<div style="float: left;">';
	$content .=  ($currentBusiness==null?$lang['bus_total']:$lang['bus_'.$currentBusiness['type']]);
	$content .= '<div class="highlighted">PKW</div>';
	$content .= '<form action="'.GetLink($company, $subpage, $subtype, $subid, 'area_rebuild').'" method="post">
				<div style="font-size:9px;"><input class="form-nobutton" type="submit" name="'.$lang['rebuild'].'" value="'.$lang['rebuild'].'" /></div>
				</form>';
	$content .= '</div>';
	
	$content .= '<div style="float: right; margin-top:10px;">';
	$content .= '<form action="'.GetLink($company, $subpage, $subtype, $subid, 'area_change').'" method="post">';
	$content .= '<label style="float: right;" for="area_expand">';
	$content .= '<input style="text-align: right; width: 60px; margin-right: 6px;" class="form-input-small" name="area_expand" type = "number" min="0" max="99999"><span class="default">'.GetArea(null).'</span>';
	$content .= '</label>';
	$content .= '<div style="font-size:9px;">'.(
			'<input class="form-nobutton" type="submit" name="expand" value="'.$lang['expand'].'" /> <input class="form-nobutton" type="submit" name="reduce" value="'.$lang['reduce'].'" />'
			).'</div>';
	$content .= '</form>';
	$content .= '</div>';
	$content .= '</div>';
	
	$content .= '</div>';	//Block Top Entry
	
	if(isset($action))
	$content .= ShowAreaSubinfo($company, $subpage, $subtype, $subid, $action);	
	
	$content .= '</div>';
	$content .= '</div>';

	$content .= '</div>'; //Block
	return $content;
}

function GetLink($company, $subpage, $subtype, $subid, $action=''){
	$link = 'game.php?comid='.$company['comid'].'&subpage='.$subpage.'&subtype='.$subtype.'&subid='.$subid;
	if($action!=null)
		$link .= '&action='.$action;
	return $link;	
}

function ShowAreaSubinfo($company, $subpage, $subtype, $subid, $action){
	global $lang;
	
	$out='';
	$out .= '<hr>';	//------------------------------------------------------
	
	$method = '';
	if(isset($_POST['expand']))
		$method = 'expand';
	else if(isset($_POST['reduce']))
		$method = 'reduce';
	
	$valid = true;
	if($method=='')
		$valid = false;
	//Second Entry
	$out .= '<div class="block">';
		if($action=='area_change'){
			if(!isset($_POST['area_expand']))
				$valid = false;
			else{
			$size = validate_input($_POST['area_expand']);
			if($size<=0 && $size>99999)
				$valid = false;
			}
			
			if($valid){
				$out .= '<h3>'.($lang['area_'.$method.'_by'].' '.GetArea($size)).'</h3>';
				$out .= '<table>';
				$out .= '<tr><th>Sch&auml;tzung</th><th>Ver&auml;nderung</th><th>Gesamt</th></tr>';
				$out .= '<tr><td>Produktivit&auml;t</td><td>Laufende Kosten</td><td>Gesamt</td></tr>';
				$out .= '<tr><td>Laufende Kosten</td><td>Laufende Kosten</td><td>Gesamt</td></tr>';
				$out .= '</table>';
				$out .= '<a href="" style="float: right; margin-top:10px;">Angebote einholen</a>';	
			}
		}
	
	$out .= '</div>';	//Block Second Entry
	return $out;
}

function BusinessListEntry($business){
	$out = '';
	$out .= '<a href="#"><li>';
	$out .= '<span>'.$business['name'].'</span>';
	$out .= '<span style="float:right">'.GetArea($business['size']).'</span>';
	$out .= '</li></a>';
	return $out;
}