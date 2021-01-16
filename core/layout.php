<?php

$STYLE_PATH = "/style";

function CreateMainMenu($MyDB, $page=''){
	global $lang;
	
	$out = '<div id="main-menu">';
	$out .=	'<ul class="mainmenu-container">
    ';
	if(isset($_SESSION['accid']) && $_SESSION['accid'] != ''){
		$out .=	mainmenu_entry('game', 'overview', $page);
		$out .=	mainmenu_entry('game', 'map', $page);
		$out .=	mainmenu_entry('game', 'market', $page, false);
		$out .=	mainmenu_entry('game', 'profile', $page);
		$out .=	mainmenu_entry('game', 'logout', $page,true, true);
		$out .=	mainmenu_infobox();
		
		if($_SESSION['usr_tutstate']==0)
			$out .= ShowCompanyList($page);
	}
	
	else{
		$out .=	mainmenu_text($lang['pagetitle']);
		//$out .=	mainmenu_entry('frontpage', 'login', $page);
		//$out .=	mainmenu_entry('frontpage', 'register', $page);
	}
	
	$out .=	'</ul">
			';
	
   	$out .='</div>
   			';
	echo $out;
	
}
function CreateCMSMenu($MyDB, $page=''){
	global $lang;

	$out = '<div id="main-menu">';
	$out .=	'<ul class="mainmenu-container">
    ';
	$out .=	mainmenu_entry('cms', 'vehicles', $page);
	$out .=	mainmenu_entry('game', 'logout', $page,true, true);
	$out .=	mainmenu_infobox();

	if($_SESSION['usr_tutstate']==0)
		$out .= ShowCompanyList($page);	

	$out .=	'</ul">
			';
	$out .='</div>
   			';
	echo $out;

}

function CreateTabs($target, $linkid, $data, $data_id, $data_title, $extralink='', $extraentry=''){
	global $lang, $MyDB;

	$out = '<ul class="tab-container">';	
	for($i=0;$i<count($data);$i++){
		$tag = '';
		if($i==0){
			$tag = '-active';
			$out .= '<a>';	
		}
		else 
			$out .= '<a href="#">';	
		$out .= '<li class="tab-entry'.$tag.'">'.$data[$i][$data_title].'</li>';
		$out .= '</a>';			
	}
	if($extraentry!=null)
		$out .= '<a href="#"><li class="tab-entry">'.$extraentry.'</li></a>';	
	$out .='</ul>
   			';
	return $out;
}

function ShowCompanyList($cpage){
	global $MyDB;
	if(!isset($_SESSION['usrid']))
		return 'No usrid';
	$sql = "SELECT `comid` FROM `game_company_owners` WHERE `ownid` = '".GetOwnerID($_SESSION['usrid'], 'user')."'";
	$companies = $MyDB-> QuerySQL($sql);
	if($companies==null)
		return 'No $companies: '.$sql;
	$comid = $companies[0]['comid'];
	$sql = "SELECT `name`,`firm` FROM `game_company` WHERE `comid` = '".$comid."'";
	$cur_company = $MyDB-> QuerySQLSingle($sql);
	if($cur_company==null)
		return 'No $cur_company: '.$sql;
	
	$page = 'company';
	$out = '<li class="mainmenu-entry-company'.($page==$cpage?'-active':'').'">';
	$out .= '<a href="game.php?page='.$page.'&id=' .$comid.'">';
	$out .= '<strong>'.GetCompanyName($cur_company).'</strong>';
	$out .= '</a>';
	$out .= '</li>';
	return $out;
}

function mainmenu_infobox()
{
	global $lang;
	$out =	'';
	$out .= "\n".'<li class="mainmenu-infobox">';
	$out .= '<div class="mainmenu-info-top">
			<div id="mainmenu-info-account">'.$lang['account_balance'].': 325.235.654.394</div>
			<div id="mainmenu-info-balance">'.$lang['balance'].': 125.658.349.340</div>
			</div>';
	$out .= '<div id="mainmenu-infotext">Hier wird es aktuelle informationen &uuml;ber Ereignisse geben</div>';
	$out .= '</li>'."\n";
	return $out;
}

function mainmenu_text($text, $size='', $right=false)
{
	global $lang;
	$tag = '';
	if($text!=null)
		$tag = ' style="font-size: '.$size.'px"';
	$out =	'';
	$out .= "\n".'<li class="mainmenu-entry'.($right?'-right':'').'">';
	$out .= '<strong'.$tag.'>'.$text.'</strong>';
	$out .= '</li>'."\n";
	return $out;
}

function mainmenu_entry($target, $page, $cpage, $active = true, $right=false)
{
	global $lang;
	return menu_entry($target.'.php?page='.$page,isset($lang[$page])?$lang[$page]:$page, $page==$cpage,$active, $right);
}

function menu_entry($url,$text,$iscurrent=false, $active = true, $right = false)
{
	$out =	'';
	$tag = $iscurrent?'-active':($active?($right?'-right':''):'-inactive');
	$out .= "\n".'<li class="mainmenu-entry'.$tag.'">';
	if($iscurrent||!$active)
		$out .= '<a>';
	else
		$out .= '<a href="'.$url.'">';
	$out .= '<strong>'.$text.'</strong>';
	$out .= '</a>';
	$out .= '</li>'."\n";
	return $out;
}

function showInfoBar($content, $headline = '', $highlighted = false, $tag=''){
	$out = '<div class="col-xs-12 '.$tag.'">
			<div class="page-infobox">';
	if($headline!='')
		$out .= '<div class="window-title">'.$headline.'</div>';
	$out .= '<p class="page-infotext'.($highlighted?'-highlighted':'').'">'.$content.'</p>';
	$out .= '</div>
			</div>';
	$out .= '<span class="block"></span>';
	return $out;
}

function showWindow($content, $headline = '', $highlighted = false, $tag=''){
	$out = '<div class="col-xs-12 '.$tag.'">
			<div class="page-infobox">';
	if($headline!='')
		$out .= '<div class="window-title">'.$headline.'</div>';
	$out .= '<div>'.$content.'</div>';
	$out .= '</div>
			</div>';
	$out .= '<span class="block"></span>';
	return $out;
}

function showWindowRaw($content, $header = '', $highlighted = false, $tag=''){
	$out = '<div class="col-xs-12 '.$tag.'">
			<div class="page-infobox">';
	if($header!='')
		$out .= '<div class="window-title-bar">'.$header.'</div>';
	$out .= '<div>'.$content.'</div>';
	$out .= '</div>
			</div>';
	$out .= '<span class="block"></span>';
	return $out;
}

function showWidget($grid_xs, $grid_sm, $grid_md, $content, $headline = '', $tag=''){
	$out = '<div class="col-xs-'.$grid_xs.' col-sm-'.$grid_sm.' col-md-'.$grid_md.' smallpadding">
			<div class="page-widget" '.$tag.'>';
	if($headline!='')
		$out .= '<div class="widget-title">'.$headline.'</div>';
	$out .= '<div class="widget-content">'.$content.'</div>';
	$out .= '</div>
			</div>';
	return $out;
}

function showFormError($text){
	$out = '<div class="alert alert-danger" role="alert">
	  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
	  <span class="sr-only">Error:</span>
	  '.$text.'
	</div>';
	return $out;
}

function CreateFormRow($label, $content){
	return '
			<div class="col-xs-12 form-row">
				<div class="col-xs-4 col-sm-4">'.$label.':</div>
					<div class="form-field col-xs-12 col-sm-8">
						'.$content.'
					</div>
			</div>';
}
function CreateFormRowField($label, $name, $placeholder, $type, $maxlength){
	return '
			<label for="password2" class="col-xs-12 form-row">
				<div class="col-xs-4 col-sm-4">'.$label.':</div>
					<div class="form-field col-xs-12 col-sm-8">
						<input class="form-control input-sm" name="'.$name.'" placeholder="'.$placeholder.'" type = "'.$type.'" maxlength = "'.$maxlength.'">
					</div>
			</label>';
}


function createlayout_header($subtitle=''){
	global $lang;
	$stylesheet = "default";
	$STYLE_PATH = "/style/".$stylesheet;
	$javascript = '<script src="./js/jquery-3.1.0.min.js"></script>';
	//$javascript = '<script src="https://code.jquery.com/jquery-1.11.3.js"></script>';	

	$title = $lang['pagetitle'];
	if($subtitle!=''&&isset($lang['pagetitle_'.$subtitle]))
		$title .= ' - '.$lang['pagetitle_'.$subtitle];
	
$out = '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";
$out .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
  <html>
  <head>
  <title>'.$title.'</title>
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="core/style/'.$stylesheet.'/style.css" />
  <link rel="SHORTCUT ICON" href="favicon.ico" />  		
  '.$javascript.'  		
  </head>
 ';

return $out;
}

function createlayout_start(){
	$out = '';
	$out .= createlayout_header('login');
	$out .= '<body>';
	$out .= '<div id="main-background-image-wrapper"><div id="main-background-image"></div></div>';
	$out .= '<div id="main-container">';

	echo $out;
}

function createlayout_end(){
	$out = '</div>';
	$out .= '
</body>
</html>';
	echo $out;
}

function createlayout_footer(){
	$out = '
			<div id="mainmenu-footer">
			<p>FOOTER</p>
			</div>';
	echo $out;
}
?>