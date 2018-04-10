<?php
class My extends Thread{
    public $gClient;
    public function __constructor($gClient)
    {
        $this->$gClient=$gClient;
    }
    function run(){
        
        for($i=1;$i<10;$i++){
            do_async($gClient);
            sleep(2);     // <------
        }
    }
}
function get_events($calendarList,$cal){
    $arr=array();
    $email=$calendarList['items'][0]['id'];
    // get events 
    $events = $cal->events->listEvents($email);
    $name=$events['items'][0]['creator']['displayName'];
    //echo '<pre>'; print_r($events); echo '</pre>';
    foreach ($events['items'] as $event) {
        echo "-----".$event['summary']." ";
        array_push($arr,$event['summary']);
    }
    echo '<pre>'; print_r($arr); echo '</pre>'; 
    store_events($arr,$email,$name);       
}
function store_events($arr,$id,$name){
    global $db_username;
    global $db_password;
    global $hostname;
    global $db_name;
    $sql= "SELECT id, u_id, summary, u_name FROM social_users WHERE u_id='$id'";
    $conn = new mysqli($hostname, $db_username, $db_password, $db_name);
// Check connection
if ($conn->connect_error) {
    echo("Connection failed: " . $conn->connect_error);
} 
$result = $conn->query($sql);
$temp=array();
$events = implode(";#;", $arr);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

        echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
        if($events==$row['summary']){
            continue;
        }else{
            $sql = "INSERT INTO social_users (u_id, summary, u_name) VALUES ('$id', '$events', '$name')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
        }
    }
} else {
    $sql = "INSERT INTO social_users (u_id, summary, u_name) VALUES ('$id', '$events', '$name')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
}
function do_async($gClient){
    $cal = new Google_CalendarService($gClient);
    $calendarList  = $cal->calendarList->listCalendarList();
    get_events($calendarList,$cal);
}
?>