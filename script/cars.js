$(document).ready(init);

function init() {
	$("#find-car").click()
}

function findCars(){
	var text = $("#find-car-input").val();
	$.ajax({
	  type: "POST",
	  url: "server/search.php",
	  data: {search: text},
	  success: function(data){
		  renderCars(data.cars);
	  },
	  dataType: "json"
	});
}

function renderCars(cars){
	var searchTemplate = $("#find-car-template").html();
	var searchMaker = new htmlMaker(searchTemplate);
	$("#search_results").html(searchMaker.getHTML(cars));
}
