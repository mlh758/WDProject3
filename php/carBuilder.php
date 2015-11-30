<?php
//make JSON for cars
function build_car_array($results) {
	$final_result = array();
	$row_count = mysqli_num_rows($results);
	for($i=0;$i<$row_count;++$i){
		$row = mysqli_fetch_array($results);
		$image = 'data:' . $row["Picture_Type"] . ';base64,' . base64_encode($row["Picture"]);
		$item = array("make"=>$row["Make"],"model"=>$row["Model"], "year"=>$row["YearMade"], "color"=>$row["Color"], "ID"=>$row["ID"],
		"picture"=>$image, "size"=>$row["Size"]);
		$final_result["cars"][]=$item;
	}
	return $final_result;
}

function build_rented_car_array($results) {
	$final_result = array();
	$row_count = mysqli_num_rows($results);
	for($i=0;$i<$row_count;++$i){
		$row = mysqli_fetch_array($results);
		$image = 'data:' . $row["Picture_Type"] . ';base64,' . base64_encode($row["Picture"]);
		$item = array("make"=>$row["Make"],"model"=>$row["Model"], "year"=>$row["YearMade"], "color"=>$row["Color"], "ID"=>$row["ID"],
		"picture"=>$image, "size"=>$row["Size"], "rental_ID"=>$row["rentalID"], "rent_date"=>$row["rentDate"], "return_date"=>$row["returnDate"]);
		$final_result["cars"][]=$item;
	}
	return $final_result;
}

?>