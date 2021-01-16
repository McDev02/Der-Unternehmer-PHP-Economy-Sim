<?php
class CompanyPage{
	public function ShowPage($action){
		global $MyDB, $lang;

		$comid = '';
		if(isset($_GET['comid'])){
			$comid = validate_input($_GET['comid']);
			$_SESSION['usr_comid'] = $comid;
		}
		else if(isset($_SESSION['usr_comid']))
			$comid = $_SESSION['usr_comid'];
		$subpage = '';
		if(isset($_GET['subpage'])){
			$subpage = validate_input($_GET['subpage']);
		}

		$subtype = '';
		if(isset($_GET['subtype'])){
			$subtype = validate_input($_GET['subtype']);
		}

		$subid = '';
		if(isset($_GET['subid'])){
			$subid = validate_input($_GET['subid']);
		}

		switch($action)
		{
			case 'delete': return $this->DeleteCompany($action,$comid, $subpage, $subtype, $subid);
			default: return $this->ShowCompany($action,$comid, $subpage, $subtype, $subid);
		}
	}

	function DeleteCompany($action, $comid){
		global $MyDB, $lang;
		$header = '';
		$content = '';
		$out = '';

		include_once('./core/GameFactory.php');
		DeleteCompany($MyDB, $comid);

		$header .= '<div class="window-title-content">Really delete company?</div>';
		$content .= '<div class="block">';
		$content .= 'Company deleted';
		$content .= '</div>';

		$out = showWindowRaw($content, $header);

		return $out;
		}

	function ShowCompany($action, $comid, $subpage, $subtype, $subid){
		global $MyDB, $lang;
				$company = $this -> GetCompany($comid);
				$subsidiaries = $this -> GetSubsidiaries($comid);
				$subsidiary = $subsidiaries[0];

				$header = '';
				$content = '';
				$out = '';

				$businesses = $MyDB -> QuerySQL("SELECT * FROM `game_business` WHERE `comid` = '".$company['comid']."' AND  `ctyid` = '".$subsidiary['ctyid']."'");

				$header .= '<div class="window-title-content">'.GetCompanyName($company).'</div>';
				$header .= CreateTabs('game.php?page=company&id='.$comid, 'subid', $subsidiaries, 'ctyid', 'name', 'game.php?page=company&action=managesubsidiaries','+ Neue Niederlassung');	//Target, linkid, data, data-id, data-title

				$content .= '<div class="block">';
				$content .= '<div class="col-xs-12 col-sm-8 col-md-8 nopadding">';

				if($subpage != null) {
					include_once('pageCompanyBusinesses.php');
					$content .= $this -> ShowWidgetDetails($subpage, $subtype, $subid, $action, $lang, $company, $subsidiary, $businesses);
					if($subid != null)
					$content .= $this -> ShowWidgetSubDetails($subpage, $subtype, $subid, $lang, $company, $subsidiary);
				}

				$content .= $this -> ShowWidgetMessages($lang, $company, $subsidiary);
				$content .= $this -> ShowWidgetStatistics($lang, $company, $subsidiary);
				$content .= $this -> ShowAdminCommands($lang, $company, $subsidiary);
				$content .= '</div>';
				$content .= '<div class="col-xs-12 col-sm-4 col-md-4 nopadding">';

				$content .= $this -> ShowWidgetBusinesses($lang, $company, $subsidiary, $businesses);
				$content .= $this -> ShowWidgetFleet($lang, $company, $subsidiary);
				$content .= $this -> ShowWidgetProjects($lang, $company, $subsidiary);

				$content .= '</div>';
				$content .= '</div>';

				$out = showWindowRaw($content, $header);

				return $out;
	}

		function GetPageLink($comid, $subpage='', $subtype='', $subid=''){
			$link = 'game.php?';
			if($comid!=null)
			$link .= '&comid='.$comid;
			if($subpage!=null)
			$link .= '&subpage='.$subpage;
			if($subtype!=null)
			$link .= '&subtype='.$subtype;
			if($subid!=null)
			$link .= '&subid='.$subid;
			return $link;
		}

		function GetPageLinkCommand($action, $comid, $subpage='', $subtype='', $subid=''){
			$link = $this->GetPageLink($comid, $subpage, $subtype, $subid);
			if($action!=null)
			$link .= '&action='.$action;
			return $link;
		}

	function GetSubsidiaries($comid){
		global $MyDB;
		$sql = "SELECT `ctyid` FROM `game_subsidiary` WHERE `comid` = '".$comid."'";
		$subsidiaries = $MyDB-> QuerySQL($sql);
		if($subsidiaries==null)
			return 'No $subsidiaries: '.$sql;

		$cities = array();
		for ($i=0;$i<count($subsidiaries);$i++){
			$ctyid = $subsidiaries[$i]['ctyid'];
			$city = $MyDB-> QuerySQLSingle("SELECT `name` FROM `world_city` WHERE `ctyid` = '".$ctyid."'");
			$cities[$i] = [
					'ctyid' => $ctyid,
					'name' => $city['name']
			];
		}

		return $cities;
	}

	function GetCompany($comid){
		global $MyDB;
		$sql = "SELECT * FROM `game_company` WHERE `comid` = '".$comid."'";
		$company = $MyDB-> QuerySQLSingle($sql);
		if($company==null)
			echo 'No $company: '.$sql;

		return $company;
	}

	function ShowWidgetSubsidiary($lang){
		$content = '';
		return showWidget(12, 12, 12, $content, $lang['subsidiary'], 'style="min-height: 100px; max-height: 240px;"');
	}

	function ShowWidgetDetails($subpage, $subtype, $subid, $action, $lang, $company, $subsidiary, $businesses){
		global $lang, $MyDB;

		$content = ShowBusinessSubpage($company, $subpage, $subtype, $subid, $action, $lang, $MyDB, $businesses);

		return showWidget(12, 12, 12, $content, $lang['businesses'], 'style="min-height: 100px;"');
	}

	function ShowWidgetSubDetails($subpage, $subid, $lang, $company, $subsidiary){
		global $lang, $MyDB;
		$content = '';

		return showWidget(12, 12, 12, $content, $lang['businesses'], 'style="min-height: 100px;"');
	}

	function ShowWidgetBusinesses($lang, $company, $subsidiary, $businesses){
	global $lang, $MyDB, $StaticData;
		$content = '';

		$content .= '<div><a href="#" style="font-size:12px;">'.$lang['add_business'].'</a></div>';

		//GetData
		$businessList = array();
		$cur = array();	$bustype;

		if($businesses!=null){
			for ($i=0;$i<count($businesses);$i++){
				$bustype = $businesses[$i]['bustype'];

				if(isset($businessList[$bustype])){
					$cur = $businessList[$bustype];
				}
				else{
					$cur = array();
					$cur['name'] = $lang['bus_'.$bustype];
					$cur['cost'] = 0;
					$cur['area'] = 0;
					$cur['type'] = $bustype;
				}

				$cur['cost'] += $businesses[$i]['size'] * rand(10,120);
				$cur['area'] += $businesses[$i]['size'];

				$businessList[$businesses[$i]['bustype']] = $cur;
			}
		}

		if(count($businessList)<=0){
		$content .= '<p>'.$lang['no_businesses'].'</p>';
		}
		else{
		$content .= '<div class="list-vertical-container" style="max-height: 240px;">';
		$content .= '<table>';

		foreach ($businessList as $item){
			$link = $this->GetPageLink($company['comid'], 'businesses', $item['type']);
			$content .= '<tr class="list-vertical-entry">';
			$content .= '<td class="list-content"><a href="'.$link.'" class="inherit">'.$item['name'].'</a></td>';
			$content .= '<td width=80px class="list-content-right">'.GetPriceNumber($item['cost'],0).'</td>';
			$content .= '<td width=60px class="list-content-right">'.GetArea($item['area']).'</td>';
			$content .= '</tr>';
		}

		$content .= '</table>';
		$content .= '</div>';
		}
		return showWidget(12, 12, 12, $content, $lang['businesses'], 'style="min-height: 100px;"');
	}

	function ShowWidgetFleet($lang, $company, $subsidiary){
		global $lang;
		$content = '';
		$content .= '<div><a href="#" style="font-size:12px;">'.$lang['to_vehicle_market'].'</a></div>';

		//GetData
		$fleet = array('Truck','Truck','Ship','Plane','Truck');
		$cargo = array('18P','20P','40C','8C','18P');
		$cost = array(42,44,10568,6425,38);

		$content .= '<div class="list-vertical-container" style="max-height: 240px;">';
		$content .= '<table>';

		for ($i=0;$i<count($fleet);$i++){
			$link = $this->GetPageLink($company['comid'], 'fleet', $fleet[$i]);	//['type']
			$content .= '<tr class="list-vertical-entry">';
			$content .= '<td class="list-content"><a href="'.$link.'" class="inherit">'.$fleet[$i].'</a></td>';
			$content .= '<td width=40px class="list-content">'.$cargo[$i].'</td>';
			$content .= '<td width=50px class="list-content-right">'.GetPriceNumber($cost[$i],0).'</td>';
			$content .= '</tr>';
		}

		$content .= '</table>';
		$content .= '</div>';
		return showWidget(12, 12, 12, $content, $lang['fleet'], 'style="min-height: 100px;"');
	}

	function ShowWidgetMessages($lang, $company, $subsidiary){
		$content = '';
		return showWidget(12, 6, 6, $content, $lang['messages'], 'style="min-height: 100px; max-height: 240px;"');
	}

	function ShowWidgetProjects($lang, $company, $subsidiary){
		$content = '';
		return showWidget(12, 12, 12, $content, $lang['projects'], 'style="min-height: 100px; max-height: 240px;"');
	}

		function ShowWidgetStatistics($lang, $company, $subsidiary){
			$content = '';
			return showWidget(12, 6, 6, $content, $lang['statistics'], 'style="min-height: 100px; max-height: 240px;"');
		}

		function ShowAdminCommands($lang, $company, $subsidiary){
			$content = '';
			$link = $this->GetPageLinkCommand("delete", $company['comid']);
			$content .=  '<a href="'.$link.'">'.$lang['delete'].'</a>';
			return showWidget(12, 12, 12, $content, $lang['admincommands'], 'style="min-height: 100px; max-height: 240px;"');
		}
}
