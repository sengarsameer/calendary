<?php
    session_start();

    if (isset($_SESSION['id'])) { 
        include('../config.php');
        require_once '../src/Google_Client.php';
        require_once '../src/contrib/Google_CalendarService.php';
        include ('../fun.php'); 
        //do_async();
        $id=$_SESSION['id'];
        $sql= "SELECT id,u_id,summary,u_name FROM social_users WHERE u_id='$id'";
        $conn = new mysqli($hostname, $db_username, $db_password, $db_name);
        // Check connection

        if ($conn->connect_error) {
            $myfile = fopen("test.txt", "w");
            fwrite($myfile,$conn->connect_error );
            fclose($myfile);
        }

        $myfile = fopen("../events.txt", "r");
        $updated_event=fgets($myfile);
		fclose($myfile);
        $result = $conn->query($sql);
        $temp=array();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $u_id=$row['u_id'];
                $summary=$row['summary'];
                $u_name=$row['u_name'];

            }
        }

        if($updated_event!=$summary){
            // echo "Hi_Check";
            $summary=$updated_event;
            $sql = "DELETE FROM social_users WHERE u_id='$u_id'";

            if ($conn->query($sql) === TRUE) {
                //echo "Record deleted successfully";
            }
            else {
                $myfile = fopen("test.txt", "w");
                fwrite($myfile,$conn->error );
                fclose($myfile);
            }
            $sql = "INSERT INTO social_users (u_id, summary, u_name) VALUES ('$id', '$summary', '$u_name')";

            if ($conn->query($sql) === TRUE) {
                //echo "New record created successfully";
            }
            else {
                $myfile = fopen("test.txt", "w");
                fwrite($myfile,$conn->error );
                fclose($myfile);
            }
        }

        $conn->close();
    }
    else{
        //echo $_SESSION['id'] ;
        $url="http://localhost/calendary/";
        header('Location: ' . $url, true, 301);
    }

?>

<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendary</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

    <!-- Navigation -->
    <nav id="siteNav" class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Logo and responsive toggle -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">
                	<span class="glyphicon glyphicon-fire"></span> 
                	CALENDARY
                </a>
            </div>
            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active">
                        <a href="#">Home</a>
                    </li>
                    <li>
                        <a href="#">Events</a>
                    </li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Services <span class="caret"></span></a>
						<ul class="dropdown-menu" aria-labelledby="about-us">
							<li><a href="#">Engage</a></li>
							<li><a href="#">Pontificate</a></li>
							<li><a href="#">Synergize</a></li>
						</ul>
					</li>
                    <li>
                        <a href="#">Contact</a>
                    </li>
                </ul>
                
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container -->
    </nav>

	<!-- Header -->
    <header>
        <div class="header-content">
        
            <?php
                echo "<h1> Welcome $u_name your events are here:</h1>";
            ?>
<ul style="list-style-type:circle">
<?php
      $arr=explode(";#;", $summary);
      foreach($arr as $event){
          echo "<li>$event</li>"; // Feteching events for displaying
      }
      ?>
</ul>  
        </div>
    </header>
   
	<!-- Footer -->
    <footer class="page-footer">
    
    	<!-- Contact Us -->
        <div class="contact">
        	<div class="container">
				<h2 class="section-heading">Contact Us</h2>
				<p><span class="glyphicon glyphicon-earphone"></span><br> +91-9797668669</p>
				<p><span class="glyphicon glyphicon-envelope"></span><br> sengar.sameer@gmail.com</p>
        	</div>
        </div>
        	
        <!-- Copyright etc -->
        <div class="small-print">
        	<div class="container">
        		<p>Copyright &copy; sengar.sameer@gmail.com 2018</p>
        	</div>
        </div>
        
    </footer>

    <!-- jQuery -->
    <script src="../js/jquery-1.11.3.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="../js/jquery.easing.min.js"></script>
    
    <!-- Custom Javascript -->
    <script src="../js/custom.js"></script>

</body>

</html>
