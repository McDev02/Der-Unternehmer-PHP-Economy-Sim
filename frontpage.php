<?php
include('./core/start.php');
$starttime = microtime();

if(isset($_SESSION['accid'])){
	redirect('game.php', 0);
}

$MyDB = new DBHelper($dbhost, $dbuser, $dbpass, $dbname);
$MyDB -> OpenDB();

//Start
$action = '';
$page = '';
if(isset($_GET['action']))
	$action = validate_input($_GET['action']);
if(isset($_REQUEST['page']))
	$page = validate_input($_REQUEST['page']);

//Document
echo createlayout_start();
echo CreateMainMenu($MyDB, $page);
echo '<div class="block">';

switch($page)
{
	case '': showLogin($MyDB,$action); break;
	case 'login': showLogin($MyDB,$action); break;
	case 'register': showRegister($MyDB,$action); break;
}

echo '</div>';
echo createlayout_footer();
echo createlayout_end();
//End

//Methods
function showLogin($MyDB, $action){
	global $lang;

	$out = '';
	$info = '';
	$showform = true;
	if($action == 'doLogin'){
		$showform = true;
		if(!isset($_POST['action'])){	//||$_POST['action']!='humaen'
			$info = '<p>'.$lang['login_botwarning'].'</p>';
		}
		else if(isset($_POST['username']) && isset($_POST['password'])){
			$usrname = validate_input($_POST['username']);
			$password = validate_input($_POST['password']);
			$account = $MyDB -> QuerySQLSingle("SELECT * FROM `core_accounts` WHERE `uname` = '".$usrname."'");
			if($account != null)
			{
				//AdminPass: K4m572NDaQBYkZhN
				$hashedPass = hash('sha256', $account['accid']. md5($password));
				if($hashedPass==$account['upass']){
					$showform = false;
					$_SESSION['gamid'] = 1;
					$_SESSION['accid'] = $account['accid'];
					$_SESSION['acc_name'] = $account['uname'];
					$user = $MyDB -> QuerySQLSingle("SELECT * FROM `game_users` WHERE `accid` = '".$account['accid']."'");
					$_SESSION['usrid'] = $user['usrid'];
					$_SESSION['usr_tutstate'] = $user['tutstate'];
					//$out .= '<p>You are logged in (ID: '.$_SESSION['accid'].')</p><p><a href="game.php">Continue</a> Redirect missing.';
					redirect('game.php', 0);
				}
				else
					$info = '<p>'.$lang['login_WrongDataEntered'].'</p>';
			}
			else
				$info = '<p>'.$lang['login_NoUserFound'].'</p>';
		}
		else{
			$info = '<p>'.$lang['login_LoginFailed'].'</p>';
		}
	}
	//Show Form
	if($showform){
		$infocontent='Willkommen zu '.$lang['gametitle'].', das Browserspiel f&uuml;r alle die sich f&uuml;r globale oder lokale Wirtschaft interessieren. Das Spiel ist noch in einem sehr fr&uuml;hen Alphastadium, bitte bedenke das bei der Registrierung. Die Entwicklung wurde erst am 08. September 2016 begonnen.
			';//</br></br>
			//Wenn Du mir Feedback geben m&ouml;chtest dann geht das am einfachsten &uuml;ber diese Mailadresse: <a href="mailto:mcdev02@yahoo.com?subject=Feedback%20-%20Der%20Unternehmer">mcdev02@yahoo.com</a>';
		$out .= showInfoBar($infocontent,'',true);

		//Register
		$out .= '<div class="col-xs-12 col-sm-6 col-md-6">';
		$out .= '<div class="form-window">';
		$out .= '<div class="form-window-title">'.$lang['frontpage_register'].'</div>';
		$out .= '<form action="frontpage.php?page=register&action=doRegister" method="post">';

		$out .= CreateFormRowField($lang['username'], 'reg_username', '', 'text', '24');
		$out .= CreateFormRowField($lang['email_address'], 'reg_email', '', 'text', '32');
		$out .= CreateFormRowField($lang['password'], 'reg_password', '', 'password', '32');

		$out .= '<button class="form-control form-submit" name="action" value="humaen" type="submit">'.$lang['register'].'</button>';

		$out .= '</form>';
		$out .= '</div>';	//form-window
		$out .= '</div>';	//Col md Register

		//Login
		$out .= '<div class="col-xs-12 col-sm-6 col-md-6">';
		$out .= '<div class="form-window">';
		$out .= '<div class="form-window-title">'.$lang['frontpage_login'].'</div>';
		$out .= '<form action="frontpage.php?page=login&action=doLogin" method="post">';

		$out .= CreateFormRowField($lang['username'], 'username', '', 'text', '24');
		$out .= CreateFormRowField($lang['password'], 'password', '', 'password', '32');

		$out .= '<button style="display: none;" type="submit" name="action" type="button">'.$lang['login'].'</button>';

		$out .= '
			<div class="col-xs-12 form-row">';

		$out .= '<div class="col-xs-4 col-sm-7"><p class="form-info">'.$info.'</p></div>';
		$out .= '<div class="form-field col-xs-12 col-sm-4">
					<button class="form-control form-submit" name="action" value="humaen" type="submit">'.$lang['login'].'</button>
				</div>';

		$out .= '
				</div>
				';

		$out .= '</form>';
		$out .= '</div>';	//form-window
		$out .= '</div>';	//Col md Login
	}
	echo $out;
}
function showRegister($MyDB, $action){
		global $lang;

		$out = '';
		$info = '';
		$showform = true;
	if($action == 'doRegister'){
		$showform = true;
		if(!isset($_POST['action'])){	//||$_POST['action']!='humaen'
			$info = '<p>'.$lang['login_botwarning'].'</p>';
		}
		else if(isset($_POST['reg_username']) && isset($_POST['reg_email']) && isset($_POST['reg_password'])){
			$usrname = validate_input($_POST['reg_username']);
			$email = validate_input($_POST['reg_email']);
			$password = validate_input($_POST['reg_password']);

			$account = $MyDB -> QuerySQLSingle("SELECT * FROM `core_accounts` WHERE `uname` = '".$usrname."' OR `email` = '".$email."' ");
			if($account == null)
			{
				//Create Account
				include_once('./core/CoreFactory.php');
				$accID = CreateCoreAccount($MyDB, $usrname, $hashedPass, $email);
				$account = $MyDB -> QuerySQLSingle("SELECT * FROM `core_accounts` WHERE `uname` = '".$usrname."'");
				if(isset($account)){
					CreateGameUser($MyDB, $account['accid'], 1);
					$showform = false;
					$_SESSION['gamid'] = 1;
					$_SESSION['accid'] = $account['accid'];
					$_SESSION['acc_name'] = $account['uname'];
					$user = $MyDB -> QuerySQLSingle("SELECT * FROM `game_users` WHERE `accid` = '".$account['accid']."'");
					$_SESSION['usrid'] = $user['usrid'];
					$_SESSION['usr_tutstate'] = $user['tutstate'];
					//$out .= '<p>You are logged in (ID: '.$_SESSION['accid'].')</p><p><a href="game.php">Continue</a> Redirect missing.';
					redirect('game.php', 0);
				}

			}
			else
				$info = '<p>'.$lang['login_UserAlreadyRegistered'].'</p>';
		}
		else{
			$info = '<p>'.$lang['login_LoginFailed'].'</p>';
			$out.=$info;
		}
	}

	echo $out;
}

?>
