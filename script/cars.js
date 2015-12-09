$(document).ready(init);

function init() {
	$("#find-car").click(findCars);
        $("#rented_cars_tab").click(getActiveRentals);
        $("#returned_cars_tab").click(getReturnedCars);
        
        $("#logout-link").click(logout);
        window.onload = addUsername;
}

function findCars(){
	var text = $("#find-car-input").val();
	$.ajax({
	  type: "POST",
	  url: "php/search.php",
	  data: {search: text},
	  success: function(data){
		  renderCars(data.cars, "#search_results", "#find-car-template");
                  $(".car_rent").on("click", function() {rentCar(this);});
	  },
	  dataType: "json"
	});
}

function getActiveRentals(){
    $.ajax({
        type: "POST",
        url: "php/rentals.php",
        data: {action: "activeRentals", token: Date.now().toString() },
        success: function(data){
                  if(data.carCount === 0){
                      $("#rented_cars").html("No cars currently rented");
                  }
                  else{
                      renderCars(data.cars, "#rented_cars", "#rented-car-template");
                      $(".return_car").on("click", function() {returnCar(this);});
                  }
		  
	  },
        dataType: "json"
    });
}

function getReturnedCars(){
     $.ajax({
        type: "POST",
        url: "php/rentals.php",
        data: {action: "history"},
        success: function(data){
                  if(data.carCount === 0){
                      $("#rented_cars").html("No vehicle history found");
                  }
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
       data: {action: "rent", carID: autoID},
       success: function(data)
       {
           if (data.Status === "Success")
           {
               //Display success
               alert("Car was rented successfully.");
               findCars();
           }
           else
           {
               //Display failure. :^(
               alert("Car was not rented successfully.");
               findCars();
           }
       }, 
       dataType: "json"
    });
}

function returnCar(element)
{
    var ID = $(element).attr("data-rental-id");
    $.ajax({
       type: "POST",
       url: "php/rentals.php",
       data: {action: "return", rentalID: ID},
       success: function(data)
       {
           if (data.Status === "Success")
           {
               //Display success
               alert("Car was returned successfully.");
               getActiveRentals();
           }
           if (data.Status === "Failed"){
               alert("Car return failed.");
           }
       }, 
       dataType: "json"
    });
}

function logout() {
    $.ajax({
        type: "POST",
        url: "php/rentals.php",
        data: {action: "logout"},
        success: function (data) {
            if (data.Status === "Success") {
                window.location.assign("index.html");
            }
        }, 
       dataType: "json"
    });
}

function addUsername(){
    $.ajax({
        type:"POST",
        url:"php/rentals.php",
        data:{action: "getName"},
        success:function(data){
            if(data.Status === "Success"){
                $("#username").html(data.Name);
            }
        },
        dataType:"json"
    });
}