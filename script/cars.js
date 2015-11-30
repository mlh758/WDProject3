$(document).ready(init);

function init() {
	$("#find-car").click(findCars);
        $("#rented_cars_tab").click(getActiveRentals);
        $("#returned_cars_tab").click(getReturnedCars);
}

function findCars(){
	var text = $("#find-car-input").val();
	$.ajax({
	  type: "POST",
	  url: "php/search.php",
	  data: {search: text},
	  success: function(data){
		  renderCars(data.cars, "#search_results", "#find-car-template");
	  },
	  dataType: "json"
	});
}

function getActiveRentals(){
    $.ajax({
        type: "POST",
        url: "php/rentals.php",
        data: {action: "activeRentals", CustomerID: "j.smith"},
        success: function(data){
		  renderCars(data.cars, "#rented_cars", "#rented-car-template");
	  },
        dataType: "json"
    });
}

function getReturnedCars(){
     $.ajax({
        type: "POST",
        url: "php/rentals.php",
        data: {action: "history", CustomerID: "j.smith"},
        success: function(data){
		  renderCars(data.cars, "#returned_cars", "#returned-car-template");
	  },
        dataType: "json"
    });
}

function renderCars(cars, location, template){
    if(!cars){
        return;
    }
	var searchTemplate = $(template).html();
	var searchMaker = new htmlMaker(searchTemplate);
	$(location).html(searchMaker.getHTML(cars));
}
