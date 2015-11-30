<?php

require_once 'connection.php';
require_once 'carBuilder.php';
    $data = $_POST['search'];
	$terms = explode(" ", $data);
	
    $query = "SELECT c.ID, c.`Picture`, c.`Picture_type`, c.`Color`, cs.`Make`, cs.`Model`, cs.`YearMade`, cs.`Size` ";
    $query .= "FROM Car c, CarSpecs cs WHERE c.`CarSpecsID` = cs.`ID` AND c.`Status` = 1";
	if(count($terms) > 0){
		$clause = " AND (";
		$count = 0;
		foreach($terms as $item){
			if($count > 0){
				$clause .= " OR ";
			}
			$value = (int) $item;
			if($value > 0){
				$clause .= "cs.`YearMade` = $item";
			}
			else{
				$clause .= "c.`Color` LIKE '%$item%' OR ";
				$clause .= "cs.`Make` LIKE '%$item%' OR ";
				$clause .= "cs.`Model` LIKE '%$item%' OR ";
				$clause .= "cs.`Size` LIKE '%$item%'";
			}
			$count += 1;
		}
		$clause .= ");";
                $query .= $clause;
	}
	
    $result = mysqli_query($connection, $query);

    if (!$result)
        die("Database access failed: " . mysqli_error($connection));


    $cars = build_car_array($result);
    mysqli_close($connection);
    echo json_encode($cars);
?>