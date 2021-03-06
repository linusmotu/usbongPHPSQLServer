<?php

	// connect to the database
	header('Content-Type: application/json');
	include('usbong-fits-connect.php');
	
	if($_SERVER['SERVER_ADDR'] == "::1") {
		$address = "localhost";
	} else {
		$address = $_SERVER['SERVER_ADDR'];
	}
	
	//$filePath = $mysqli->real_escape_string($_POST['filepath']);
	$json = file_get_contents('php://input');
	if(isset($json)) {
		$data = json_decode($json, TRUE);
		if(is_null($json) == false) {
			$filePath = $data['FILEPATH'];
		}	
	}
	
	//echo $filePath;
	// get the records from the database
	if ($result = $mysqli->query("SELECT `DOWNLOADCOUNT` FROM `fits` WHERE `FILEPATH` = '".$filePath."'"))
	{
		// display records if there are records to display
		if ($result->num_rows > 0)
		{
			$responses = array();
			$test = $result->fetch_object();
			//$newTest = (int)$test + 1;
			$newTest = (Integer)($test->DOWNLOADCOUNT) + 1;
			$mysqli->query("UPDATE `fits` SET `DOWNLOADCOUNT`=".$newTest." where `FILEPATH` = '".$filePath."'");
			$newCount = $mysqli->query("SELECT `DOWNLOADCOUNT` FROM `fits` WHERE `FILEPATH` = '".$filePath."'");
			$temp = $newCount->fetch_object();
			$jsonResponse = array(
				"result" => "Successfully downloaded",
				"new_count" => $temp->DOWNLOADCOUNT,
				"date_created" => date("Y-m-d-h-i-s")
			);
			echo json_encode($jsonResponse);  
		}
		// if there are no records in the database, display an alert message
		else
		{
				// echo "No results to display!";
		}
	}
	// show an error if there is an issue with the database query
	else
	{
			// echo "Error: " . $mysqli->error;
	}
	// close database connection
	$mysqli->close();

?>
