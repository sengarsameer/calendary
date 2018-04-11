<?php

    function get_events($calendarList,$cal){
        $arr=array();
        $email=$calendarList['items'][0]['id'];
        // get events 
        $events = $cal->events->listEvents($email);
        $name=$events['items'][0]['creator']['displayName'];
        //echo '<pre>'; print_r($events); echo '</pre>';

        foreach ($events['items'] as $event) {
            //echo "-----".$event['summary']." ";
            array_push($arr,$event['summary']);
        }
        
        //echo '<pre>'; print_r($arr); echo '</pre>'; 
        store_events($arr,$email,$name);
        return $email;       
    }

    // save to database
    function store_events($arr,$id,$name){
        global $db_username;
        global $db_password;
        global $hostname;
        global $db_name;
        $sql= "SELECT summary FROM social_users WHERE u_id='$id'";
        $conn = new mysqli($hostname, $db_username, $db_password, $db_name);
        // Check connection

        if ($conn->connect_error) {
            $myfile = fopen("test.txt", "w");
		    fwrite($myfile,$conn->error );
		    fclose($myfile);
        }

        $result = $conn->query($sql);
        $temp=array();
        $events = implode(";#;", $arr);
        $myfile = fopen("events.txt", "w");
		fwrite($myfile,$events);
		fclose($myfile);
        
        if ($result->num_rows > 0) {
            // output data of each row

            while($row = $result->fetch_assoc()) {

                if($events==$row['summary']){
                    continue;
                }
                else{
                    $sql = "DELETE FROM social_users WHERE u_id='$id'";

                    if ($conn->query($sql) === TRUE) {
                        echo "Record deleted successfully";
                    }
                    else {
                        $myfile = fopen("test.txt", "w");
		                fwrite($myfile,$conn->error );
		                fclose($myfile);
                    }
                    $sql = "INSERT INTO social_users (u_id, summary, u_name) VALUES ('$id', '$events', '$name')";

                    if ($conn->query($sql) === TRUE) {
                        echo "New record created successfully";
                    }
                    else {
                        $myfile = fopen("test.txt", "w");
		                fwrite($myfile,$conn->error );
		                fclose($myfile);
                    }
                }
            }
        }
        else {
            
            $sql = "INSERT INTO social_users (u_id, summary, u_name) VALUES ('$id', '$events', '$name')";

            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            }
            else {
                //echo "Error: " . $sql . "<br>" . $conn->error;
                $myfile = fopen("test.txt", "w");
		        fwrite($myfile,$conn->error );
		        fclose($myfile);
            }
        }

        $conn->close();
    }

    // driving function for handling events
    function do_async(){
        $myfile = fopen("token.txt", "r");
        $token=fgets($myfile);
        fclose($myfile);
        $gClient = new Google_Client();
	    $gClient->setAccessToken($token);
        $cal = new Google_CalendarService($gClient);
        $calendarList  = $cal->calendarList->listCalendarList();
        $email=get_events($calendarList,$cal,$count);
        return $email;
    }
?>