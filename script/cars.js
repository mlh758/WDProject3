$(document).ready(init);

function init() {
	$("#find-car").click(findCars);
        $("#rented_cars_tab").click(getActiveRentals);
        $("#returned_cars_tab").click(getReturnedCars);
        
        $(".car_rent").click(rentCar(this));
        $(".return_car").click(returnCar(this));
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
               //print dialog (modify CSS) exclaiming success.
           }
           else
           {
               //print dialog (modify CSS) exclaiming failure. :^(
           }
       }, 
       dataType: "json"
    });
};

function returnCar(element)
{
    var autoID = $(element).attr("data-rental-id");
    $.ajax({
       type: "POST",
       url: "php/rentals.php",
       data: {action: "return", CustomerID: "j.smith", carID: autoID},
       success: function(data)
       {
           if (data.Status == "Success")
           {
               //print dialog exclaiming success.
           }
           else
           {
               //print dialog exclaiming failure. :^(
           }
       }, 
       dataType: "json"
    });
};
    
