<?php

    require_once 'src/Google_Client.php';
    require_once 'src/contrib/Google_CalendarService.php';
    include ('config.php'); 
	include ('fun.php'); 
    $count=0;
    while(TRUE){
        sleep(15);
        $email=do_async();
        $myfile = fopen("count.txt", "w");
		fwrite($myfile,$count );
		fclose($myfile);
        $count+=1;
        if($count==120){
            break;
        }
    }

?>