<?php

function SetTutstate($usrid, $state){
	global $MyDB;
	$MyDB -> CallSQL("UPDATE `game_users` SET `tutstate` = '".$state."' WHERE `game_users`.`usrid` = '".$usrid."';");
	$_SESSION['usr_tutstate'] = $state;
}

function showTut1($user, $action){
	global $MyDB, $lang;

	if(isset($action)&& $action == 'tut1_submit'){
		if($user['tutstate']<=1){
			SetTutstate($user['usrid'], 2);
		}
		return 2;
	}

	$content = '';

	echo showInfoBar($lang['intro_text_1_1'], $lang['introduction']);

	//Form
	$content .= $lang['intro_text_1_2'];
	$content .= '<div class="block">';
	$content .= '<form action="game.php?page=setupgame&pid=1&action=tut1_submit" method="post">';
	$content .= '<button class="form-control form-submit" name="action" type="submit">'.$lang['page_continue'].'</button>';
	$content .= '</form>';
	$content .= '</div>';	//Col

	echo showInfoBar($content, $lang['your_advisor']);
	return 1;
}


function showTut2($user, $action){
	global $MyDB, $lang, $StaticData;

	$warning = '';

	if(isset($action)&& $action == 'tut2_submit'){
		$name = '';
		$ctyid = '';
		$firm = '';
		$isic = '';
		if(isset($_POST['tut_name']))
			$name = validate_input($_POST['tut_name']);
		if(isset($_POST['tut_city']))
			$ctyid = validate_input($_POST['tut_city']);
		if(isset($_POST['tut_firm']))
			$firm = validate_input($_POST['tut_firm']);
		if(isset($_POST['tut_isic']))
			$isic = validate_input($_POST['tut_isic']);

		$ready = true;

		if(strlen($name)<3 && strlen($name)>32){
			$warning = $lang['info_company_name_wrong'];
			$ready = false;
		}
		if(strlen($firm)<2 && strlen($firm)>8){
			$warning = $lang['info_company_firm_wrong'];
			$ready = false;
		}

		if($ready){
			$query = $MyDB -> QuerySQLSingle("SELECT * FROM `game_company` WHERE `name` = '".$name."'");
			if($query!=null){
				$ready = false;
				$warning = $lang['info_company_name_exists'];
			}
			else{
			include_once('./core/GameFactory.php');
			include_once('./core/CompanyFactory.php');
			$shares = 10000;
			$owntype = 'user';
			$firm = 'GmbH';
			$comid = CreateCompany($MyDB, $_SESSION['gamid'], $ctyid, $name, $firm, $isic, $shares);
			if($comid==null){
				$ready = false;
				$warning = 'Something went wrong.';
			}

			SetCompanyOwnership($MyDB, $comid, GetOwnerID($user['usrid'], $owntype), $owntype, $shares);

			GenerateCompanyContent($MyDB, $comid, $ctyid);
		}
		}
		if($ready){
			SetTutstate($user['usrid'], 3);
			return 3;
		}
	}

	$content = $lang['intro_text_2'];
	echo showInfoBar($content, $lang['introduction']);

	//Form
	$content = '<div class="block">';
	$content .= '<form action="game.php?page=setupgame&pid=2&action=tut2_submit" method="post">';

	if($warning!=null)
		$content .='<div class="col-xs-12">'.showFormError($warning).'</div>';

	$content .= CreateFormRowTutLabel($lang['seedcapital'],GetPriceNumber($StaticData -> world_default['seedcapital'], 0));

	$content .= CreateCitySelection($lang['city_selection_title']);

	//Firm
	$content .='
		<label for="password2" class="col-xs-12 form-row">
			<div class="col-xs-12 col-sm-4">'.$lang['company_name'].':</div>
				<div class="form-field col-xs-12 col-sm-8">
					<div class="form-field col-xs-7  nopadding">
						<input class="form-control input-sm" name="tut_name" type = "text" maxlength = "32">
					</div>
					<div class="col-xs-1 nopadding">
					</div>
					<div class="form-field col-xs-4 nopadding">';
	//$content .=		'<input class="form-control input-sm" name="tut_firm" value="GmbH" type = "text" maxlength = "32">';
	$content .=		'<span id="firm_title">GmbH</span>';
	$content .='	</div>
					'.CreateCaption($lang['tut_firminfo']).'
				</div>
		</label>';

	$content .= CreateIndustrySelection($lang['industrie_selection_title']);
	//$content .= CreateFormRowTutField($lang['company_name'], 'tut_firm', '', 'text', '32', $lang['tut_firminfo']);



	$content .= '<button class="form-control form-submit" name="action" type="submit">'.$lang['sign_business_registration'].'</button>';

	$content .= '</form>';
	$content .= '<form action="game.php?page=setupgame&pid=1" method="post">';
	$content .= '<button class="form-control form-submit" name="action" type="submit">'.$lang['page_back'].'</button>';
	$content .= '</form>';
	$content .= '</div>';	//Col

	echo showInfoBar($content, $lang['business_registration']);
		return 2;
}
function showTut3($user){
	global $MyDB;
	$out = 'Tutorial: 3';
	$ready = true;
	if($ready){
		SetTutstate($user['usrid'], 0);
		return 0;
	}
	echo $out;
		return 3;
}
function showTut4($user){
	$out = 'Tutorial: 4';
	echo $out;
		return 4;
}

function CreateFormRowTutLabel($label, $text, $caption=''){
	return '
		<label for="password2" class="col-xs-12 form-row">
			<div class="col-xs-12 col-sm-4">'.$label.':</div>
				<div class="form-field col-xs-12 col-sm-8">
					<div class="highlighted">'.$text.'</div>
					'.($caption!=''?CreateCaption($caption):'').'
				</div>
		</label>';
}
function CreateFormRowTutField($label, $name, $placeholder, $type, $maxlength, $caption=''){
	return '
		<label for="password2" class="col-xs-12 form-row">
			<div class="col-xs-12 col-sm-4">'.$label.':</div>
				<div class="form-field col-xs-12 col-sm-8">
					<input class="form-control input-sm" name="'.$name.'" placeholder="'.$placeholder.'" type = "'.$type.'" maxlength = "'.$maxlength.'">
					'.($caption!=''?CreateCaption($caption):'').'
				</div>
		</label>';
}
function CreateCitySelection($label){
	global $lang, $MyDB;
	$cities = $MyDB-> QuerySQL("SELECT * FROM `world_city` WHERE `gamid` = '".$_SESSION['gamid']."'");
	if($cities==null)
		return 'No cities found';
	$out ="<script>
			$(document).on('change', '#city-caption-toggle', function() {
		  var target = '#city_captions';
		  var show = $('option:selected', this).data('show');
		  $(target).children().addClass('hide');
		  $(show).removeClass('hide');
		});
		$(document).ready(function(){
		    $('#city-caption-toggle').trigger('change');
		});
			</script>";
	$out .= '
		<label for="tut_city" class="col-xs-12 form-row">
			<div class="col-xs-12 col-sm-4">'.$label.':</div>
				<div class="form-field col-xs-12 col-sm-8">
				<select name="tut_city" id="city-caption-toggle" style="width: 100%">';
	for ($i = 0; $i < count($cities); $i++) {
		$cty = $cities[$i];
		$out .= '<option value="'.$cty['ctyid'].'" data-show="#city_caption_'.$i.'">'.$cty['name'].'</option>';
	}
	$out .='
				</select>
			'.CreatecityCaptions($cities).'
			</div>
		</label>
		';
	return $out;
}
function CreateCityCaptions($cities){
	$randComp = ['Kaum','&Uuml;berf&uuml;llt','Ausgeglichen'];
	$randSat = ['Gering','Ausgeglichen','Ausgelastet'];
	global $lang;
	$out = '<div id="city_captions">';
	for ($i = 0; $i < count($cities); $i++) {
		$cty = $cities[$i];
		$out .= '
				<div class="info-caption hide" id="city_caption_'.$i.'" >
				<p>'.$lang['residents'].': '.GetNumber($cty['residents'],0).'</p>';

		$out .= '</div>';
	}
	$out .= '</div>';
	return $out;
}
function CreateIndustrySelection($label){
	global $lang, $StaticData;
	$out ="<script>
			$(document).on('change', '#industry-caption-toggle', function() {
		  var target = '#industry_captions';
		  var targetfirm = $(this).data('targetfirm');
		  var show = $('option:selected', this).data('show');
		  var firm = $('option:selected', this).data('firm');
		  $(target).children().addClass('hide');
		  $(show).removeClass('hide');
		  document.getElementById('firm_title').innerHTML = firm;
		});
		$(document).ready(function(){
		    $('#industry-caption-toggle').trigger('change');
		});
			</script>";
	$out .= '
		<label for="tut_isic" class="col-xs-12 form-row">
			<div class="col-xs-12 col-sm-4">'.$label.':</div>
				<div class="form-field col-xs-12 col-sm-8">
				<select name="tut_isic" id="industry-caption-toggle" style="width: 100%">';
	for ($i = 0; $i < count($StaticData -> game_industries); $i++) {
		$ind = $StaticData -> game_industries[$i];
		$firm = ($ind=='m'?'Einzelunternehmen':'GmbH');
		$out .= '<option '.($ind=='m'?'selected':'').' value="'.$ind.'" data-firm="'.$firm.'" data-show="#industry_caption_'.$i.'">'.$lang['industry_'.$ind].'</option>';
	}
	$out .='
				</select>
			'.CreateIndustryCaptions().'
			</div>
		</label>
		';
	return $out;
}
function CreateIndustryCaptions(){
	$randComp = ['Kaum','&Uuml;berf&uuml;llt','Ausgeglichen'];
	$randSat = ['Gering','Ausgeglichen','Ausgelastet'];
	global $lang, $StaticData;
	$out = '<div id="industry_captions">';
	for ($i = 0; $i < count($StaticData -> game_industries); $i++) {
		$ind = $StaticData -> game_industries[$i];
		$out .= '
				<div class="info-caption hide" id="industry_caption_'.$i.'" >
				<p >'.$lang['industry_desc_'.$ind].'</p>';

		if($ind!='m')
			$out .= '<p>
					<span class="highlighted">'.$lang['market_saturation'].': '.$randSat[rand(0,2)].'</span>
					<span> - </span>
					<span class="highlighted">'.$lang['competition'].': '.$randComp[rand(0,2)].'</span>
					<span>(Platzhalter, keine Auswirkung)</span>
					</p>';

		$out .= '</div>';
	}
	$out .= '</div>';
	return $out;
}
function CreateCaption($text){
	$out = '<p class="info-caption">'.$text.'</p>';
	return $out;
}
?>
