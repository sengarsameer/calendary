<?php
	include ('config.php'); 
	include ('fun.php'); 
	require_once 'src/Google_Client.php';
	require_once 'src/contrib/Google_CalendarService.php';
	session_start();
	$gClient = new Google_Client();
	$gClient->setApplicationName('calendary');
	$gClient->setClientId($client_id);
	$gClient->setClientSecret($client_secret);
	$gClient->setRedirectUri($redirect_url);
	$gClient->setDeveloperKey($dev_key);
	$gClient->setScopes(array('https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.readonly'));

	if (isset($_GET['code'])) { 
		$gClient->authenticate($_GET['code']);
		$_SESSION['token'] = $gClient->getAccessToken();
		header('Location: ' . filter_var($redirect_url, FILTER_SANITIZE_URL));
		return;
	}

	if (isset($_SESSION['token'])) { 
		$gClient->setAccessToken($_SESSION['token']);
	}

	if ($gClient->getAccessToken()) {	
		$myfile = fopen("token.txt", "w");
		fwrite($myfile,$gClient->getAccessToken() );
		fclose($myfile);
		$email=do_async();
		ignore_user_abort(true);
		set_time_limit(0);
		exec('nohup setsid php async.php > /dev/null 2>&1 &');
		$_SESSION['id'] 	= $email;
		$url="http://localhost/calendary/success/";
		header('Location: ' . $url, true, 301);
	  	
	}

	else {
    	$url="http://localhost/calendary/";
		header('Location: ' . $url, true, 301);
	}

?>