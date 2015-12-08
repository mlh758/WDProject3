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
                  //Attach event handler to rent button.
                  $(".car_rent").on("click", function() {rentCar(this);});
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
                  //Attach event handler to return button.
                  $(".return_car").on("click", function() {returnCar(this);});
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

function rentCar(element)
{
    var autoID = $(element).attr("id");
    $.ajax({
       type: "POST",
       url: "php/rentals.php",
       data: {action: "rent", CustomerID: "j.smith", carID: autoID},
       success: function(data)
       {
           if (data.Status == "Success")
           {
               //Display success
               alert("Car was rented successfully.");
               findCars();
           }
           else
           {
               //Display failure. :^(
               alert("Car was not rented successfully.")
               findCars();
           }
       }, 
       dataType: "json"
    });
};

function returnCar(element)
{
    var ID = $(element).attr("data-rental-id");
    $.ajax({
       type: "POST",
       url: "php/rentals.php",
       data: {action: "return", rentalID: ID},
       success: function(data)
       {
           if (data.Status == "Success")
           {
               //Display success
               alert("Car was returned successfully.");
               getActiveRentals();
           }
           else
           {
               //Dialog exclaiming failure. :^(
               alert("Car return failed.");
               getActiveRentals();
           }
       }, 
       dataType: "json"
    });
};
    
