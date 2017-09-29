<?php
  //ini_set('display_errors', 'On');
  include 'storedInfo.php';
  $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ( !$mysqli || $mysqli->connect_errno ) {
    echo 'Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
  }
?>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Video Store... What's That?</title>
  </head>
  <body>
  <h3>Video Store Inventory Form</h3>
  <br>
    <form action="videostoreinterface.php" method="POST">
      <table>
	    <caption>Add Video:</caption>
		<tr>
		  <td><span>NAME:</span>
		    <input type="text" name="name">
	    <tr>
		  <td><span>CATEGORY:</span>
		    <input type="text" name="category">
		<tr>
		  <td><span>LENGTH:</span>
		    <input type="text" name="length">
		<tr>
		  <td>
		    <input type="submit" value="Add Video">
	  </table>
    </form>
  <?php 
//check for empty form fields
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
	  if ( empty($_POST["name"]) ) {
	    echo "**Name is required.**";
	  }
	  else {
	    $name = $_POST["name"];
	  }
	  if ( !empty($_POST["category"]) ) {
	    $category = $_POST["category"];
      }		
	  if ( !empty($_POST["length"]) ) {
	    if ( !is_numeric($_POST["length"]) ) {
		  echo "Length must be numeric.";
		}
		else {
	      $length = $_POST["length"];
		}
	  }
	}
/* Prepared statement, stage 1: prepare */
    if (!($stmt = $mysqli->prepare("INSERT INTO video_rental_store(name, category, length, rented) VALUES (?, ?, ?, ?)"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
	$rented = 0;
/* Prepared statement, stage 2: bind and execute */
    if (!$stmt->bind_param("ssii", $name, $category, $length, $rented)) {
      echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    if (!$stmt->execute()) {
      echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }
/* explicit close recommended */
    $stmt->close();
//prepare results
    if ( !($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM video_rental_store")) ) {
	  echo 'Prepare failed: (' . $mysqli->errno . ') '. $mysqli->error;
	}
//bind and execute results
   	if (!$stmt->execute()) {
	  echo 'Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error;
	}
	if ( !$stmt->bind_result($outName, $outCategory, $outLength, $outRented) ) {
	  echo 'Binding output parameters failed: (' .$stmt->errno . ') ' . $stmt->error;
	}
    echo '<table border="1">';
    echo '<caption>Videos in Stock:</caption>';
    echo '<thead><th>NAME</th><th>CATEGORY</th><th>LENGTH</th><th>STATUS</th><th></th><th></th></thead>';
    echo '<tbody>';
    $i = 0;
    while ( $stmt->fetch() ) {
	$i++;
      if ( $outRented == 0 ) {
      $rentalStatus = 'Available';
	  $checkInOut = 'Check Out';
      }
      else {
        $rentalStatus = 'Checked Out';
		$checkInOut = 'Check In';
      }
      $videoName = $outName;
      echo '<tr>
              <td>' . $outName . '</td>
    		  <td>' . $outCategory . '</td>
			  <td>' . $outLength . '</td>
		      <td>' . $rentalStatus . '</td>
		      <td><form action="delete.php" method="POST"><input type="hidden" name="checkName" value ="' . $outName . '"><input type="submit" value="' . $checkInOut . '"></form></td>
		      <td><form action="delete.php" method="POST"><input type="hidden" name="deleteName" value="' . $outName . '"><input type="submit" value="Delete Movie"></form></td>';
	}
    echo '</table>';
    echo '<form action="delete.php" method="POST"><input type="submit" name="deleteAll" value="Remove All"></form>';
/* explicit close recommended */
    $stmt->close();
		
//prepare results for drop down menu
    if ( !($stmt = $mysqli->prepare("SELECT DISTINCT category FROM video_rental_store")) ) {
	  echo 'Prepare failed: (' . $mysqli->errno . ') '. $mysqli->error;
	}
//bind and execute results
	if (!$stmt->execute()) {
	  echo 'Execute failed: (' . $mysqli->errno . ') ' . $mysqli->error;
	}
	if ( !$stmt->bind_result($categories) ) {
	  echo 'Binding output parameters failed: (' .$stmt->errno . ') ' . $stmt->error;
	}
	echo "Filter Results:";
	echo '<select name="chooseCategory">';
    while ( $row = $stmt->fetch() ) {
      echo '<option value="' . $categories . '">' . $categories . '</option>';
	}
    echo '</select><input type="hidden" name="chooseCategory" value="' . $categories . '"><input type="submit" value="Select"></select></form>';
	if ( isset($_GET['chooseCategory']) ) {
	$catWanted = $_GET['chooseCategory'];
	$mysqli->query("SELECT name, category, length, rented FROM video_rental_store WHERE name='$catWanted'");
	}
	echo '</form>';
/* explicit close recommended */
    $stmt->close();
  ?>
  </body>
</html>