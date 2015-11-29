<?php

require_once 'connection.php';
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
				$clause .= "c.`Color` LIKE '%$item' OR ";
				$clause .= "cs.`Make` LIKE '$item' OR ";
				$clause .= "cs.`Model` LIKE '$item' OR ";
				$clause .= "cs.`Size` LIKE '$item'";
			}
			$count += 1;
		}
		$clause .= ");";
	}
	
    $result = mysqli_query($connection, $query);

    if (!$result)
        die("Database access failed: " . mysqli_error($connection));


    $cars = build_car_array($result);
    mysqli_close($connection);
    echo json_encode($cars);


//make JSON for cars
function build_car_array($results) {
	$final_result = array();
	$row_count = mysqli_num_rows($results);
	for($i=0;$i<$row_count;++$i){
		$row = mysqli_fetch_array($results);
		$image = 'data:' . $row["Picture_Type"] . ';base64,' . base64_encode($row["Picture"]);
		$item = array("make"=>$row["Make"],"model"=>$row["Model"], "year"=>$row["YearMade"], "color"=>$row["Color"], "ID"=>$row["ID"], "picture"=>$image);
		$final_result["items"][]=$item;
	}
	return $final_result;
}

?>