<?php

require_once 'connection.php';
require_once 'carBuilder.php';
require_once 'utility.php';

if (isset($_POST["action"])) {
    $action = sanitizeMYSQL($connection,$_POST["action"]);
	$results = Array("Status" => "Failed"); //Assume failure by default


    switch ($action) {       
        case "rent":
			$customerID = sanitizeMYSQL($connection, $_POST["CustomerID"]);
			$carID = sanitizeMYSQL($connection, $_POST["carID"]);
            $results["Status"] = rentCar($connection, $customerID, $carID);
            break;
		case "return":
			$rentalID = sanitizeMYSQL($connection, $_POST["rentalID"]);
                        $results["Status"] = returnCar($connection, $rentalID);
			break;
		case "history":
			$customerID = sanitizeMYSQL($connection, $_POST["CustomerID"]);
			$data = customerHistory($connection, $customerID, 2);
			$results["cars"] = $data;
			break;
		case "activeRentals":
			$customerID = sanitizeMYSQL($connection, $_POST["CustomerID"]);
			$data = customerHistory($connection, $customerID, 1);
			$results["cars"] = $data;
			break;
    }
    echo json_encode($results);
}
//2nd parameter $ID removed. It wasn't being called by the action switch statement.
function rentCar($connection, $customerID, $carID){
	$query = "INSERT INTO `rental` (`rentDate`, `returnDate`, `status`, `CustomerID`, `carID`) ";
	$query .= "VALUES(CURDATE(), NULL, 1, '$customerID', '$carID')";
	$result1 = runQuery($connection, $query);
	$query = "UPDATE `car` SET `status` = 2 WHERE `id` = $carID";
	$result2 = runQuery($connection, $query);
	if(result1 && result2){
		return "Success";
	}
	else{
		return "Failed";
	}
}
function returnCar($connection, $rentalID){
	$query = "UPDATE `rental` SET `returnDate` = CURDATE(), `status` = 2 WHERE `ID` = $rentalID";
	$result1 = runQuery($connection, $query);
        $query = "SELECT `carID` from `rental` where `ID` = $rentalID";
        $temp = runQuery($connection, $query);
        $row_count = mysqli_num_rows($temp);
        if($row_count != 1){
            return "Failed, no carID found";
        }
        $row = mysqli_fetch_array($temp);
        $carID = $row["carID"];
	$query = "UPDATE `car` SET `status` = 1 WHERE `ID` = $carID";
	$result2 = runQuery($connection, $query);
	if(result1 && result2){
		return "Success";
	}
	else{
		return "Failed";
	}
}
function customerHistory($connection, $customerID, $flag){
	$query = "SELECT c.ID, c.`Picture`, c.`Picture_type`, c.`Color`, cs.`Make`, cs.`Model`, cs.`YearMade`, cs.`Size`, r.`ID` as rentalID, r.`rentDate`, r.`returnDate` ";
        $query .= "FROM Car c, CarSpecs cs, Rental r WHERE r.`CustomerID` = '$customerID' AND r.`status` = $flag AND r.`carID` = c.`ID` AND  c.`CarSpecsID` = cs.`ID`";
	$data = runQuery($connection, $query);
	$cars = build_rented_car_array($data);
	return $cars["cars"];
}
function runQuery($connection, $string){
	$result = mysqli_query($connection, $string);

    if (!$result)
        die("Database access failed: " . mysqli_error($connection) . $string);
	return $result;
}
?>