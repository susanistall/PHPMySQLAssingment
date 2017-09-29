<?php
  include 'storedInfo.php';
  $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ( !$mysqli || $mysqli->connect_errno ) {
    echo 'Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
  }
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ( isset($_POST['deleteAll']) ) {
        $mysqli->query("TRUNCATE TABLE video_rental_store");
      }
      if ( isset($_POST['deleteName']) ) {
	    $videoDelete = $_POST['deleteName'];
	    $mysqli->query("DELETE FROM video_rental_store WHERE name='$videoDelete'");
	  }
	  if ( isset($_POST['checkName']) ) {
	    $videoName = $_POST['checkName'];
		$mysqli->query("UPDATE video_rental_store SET rented= NOT rented WHERE name='$videoName'");
	  }  
	}
    header('Location: videostoreinterface.php', true);
?> 