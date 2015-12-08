<?php

require_once 'connection.php';
require_once 'carBuilder.php';
require_once 'utility.php';
session_start(); //start the session

if (isset($_POST["action"]) && is_session_active()) {
    $action = sanitizeMYSQL($connection,$_POST["action"]);
    $results = Array("Status" => "Failed"); //Assume failure by default
    $_SESSION['start'] = time(); //reset the session start time

    switch ($action) {       
        case "rent":
                $customerID = sanitizeMYSQL($connection, $_SESSION["username"]);
                $carID = sanitizeMYSQL($connection, $_SESSION["username"]);
                $results["Status"] = rentCar($connection, $customerID, $carID);
                break;
        case "return":
                $rentalID = sanitizeMYSQL($connection, $_POST["rentalID"]);
                $results["Status"] = returnCar($connection, $rentalID);
                break;
        case "history":
                $customerID = sanitizeMYSQL($connection, $_SESSION["username"]);
                $data = customerHistory($connection, $customerID, 2);
                $results["cars"] = $data;
                break;
        case "activeRentals":
                $customerID = sanitizeMYSQL($connection, $_SESSION["username"]);
                $data = customerHistory($connection, $customerID, 1);
                $results["cars"] = $data;
                break;
        case "logout":
            logout();
            $results["Status"]= "Success";
            break;
        case "addUsername":
            $results["Status"] = "Success";
            $results["Username"] = $_SESSION["username"];
            break;
    }
    echo json_encode($results);
}

function is_session_active() {
    return isset($_SESSION) && count($_SESSION) > 0 && time() < $_SESSION['start'] + 60 * 5; //check if it has been 5 minutes
}

function getUsersName($connection){
    $customerID = sanitizeMYSQL($connection, $_SESSION["username"]);
    $query = "SELECT `Name` FROM `customer` WHERE `ID` = '$customerID'";
    return runQuery($connection, $query);
}

function rentCar($connection, $ID, $customerID, $carID){
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

function logout() {
    // Unset all of the session variables.
    $_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }

// Finally, destroy the session.
    session_destroy();
}
?>