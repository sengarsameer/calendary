<?php
include ('config.php'); 
include ('fun.php'); 
require_once 'src/Google_Client.php';
//require_once 'src/contrib/Google_Oauth2Service.php';
require_once 'src/contrib/Google_CalendarService.php';
session_start();
$gClient = new Google_Client();
$gClient->setApplicationName('calendary');
$gClient->setClientId($client_id);
$gClient->setClientSecret($client_secret);
$gClient->setRedirectUri($redirect_url);
$gClient->setDeveloperKey($dev_key);
$gClient->setScopes(array('https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.readonly'));

//$google_oauthV2 = new Google_Oauth2Service($gClient);
//$acc_token=$gClient->getAccessToken();
if (isset($_GET['code'])) 
{ 
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($redirect_url, FILTER_SANITIZE_URL));
	return;
}

if (isset($_SESSION['token'])) 
{ 
		$gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) 
{	
	//$gClient->setAccessToken($acc_token);
	$cal = new Google_CalendarService($gClient);
	$calendarList  = $cal->calendarList->listCalendarList();
	//echo '<pre>'; print_r($calendarList); echo '</pre>';
		get_events($calendarList,$cal);
		$workers=new My($gClient); 
		$workers->start(); 
		
	  //Get user details if user is logged in
	  $_SESSION['token'] 	= $gClient->getAccessToken();
}
else 
{
    $url="http://localhost/calendary/";
	header('Location: ' . $url, true, 301);
}

?>