<?php
include ('config.php'); 
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';
require_once 'src/contrib/Google_CalendarService.php';
session_start();
$gClient = new Google_Client();
$gClient->setApplicationName('calendary');
$gClient->setClientId($client_id);
$gClient->setClientSecret($client_secret);
$gClient->setRedirectUri($redirect_url);
$gClient->setDeveloperKey($dev_key);
$gClient->setScopes(array('https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.readonly'));

$google_oauthV2 = new Google_Oauth2Service($gClient);
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
	$calendarList  = $cal->calendarList->listCalendarList();;
	echo '<pre>'; print_r($calendarList); echo '</pre>';
	while(true) {
		foreach ($calendarList->getItems() as $calendarListEntry) {

			echo $calendarListEntry->getSummary()."\n";


			// get events 
			$events = $service->events->listEvents($calendarListEntry->id);


			foreach ($events->getItems() as $event) {
			    echo "-----".$event->getSummary()." ";
			}
		}
		$pageToken = $calendarList->getNextPageToken();
		if ($pageToken) {
			$optParams = array('pageToken' => $pageToken);
			$calendarList = $service->calendarList->listCalendarList($optParams);
		} else {
			break;
		}
	}
	  //Get user details if user is logged in
      $user 				= $google_oauthV2->userinfo->get();
      echo '<pre>'; print_r($user); echo '</pre>';
	  $user_id 				= $user['id'];
	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	  $_SESSION['token'] 	= $gClient->getAccessToken();
}
else 
{
    $url="http://localhost/calendary/";
	header('Location: ' . $url, true, 301);
}

?>