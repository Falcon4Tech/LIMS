$(document).ready(function(e){
	$(":input:not(.info)").bind("keyup change", function(e) {
		$(".frame-legend").css("background-image", "background-image: linear-gradient(90deg, red, transparent)");
	});
});

	function zapisano() {
		$(".frame-legend").css("background-image", "");
	}

	function backBlink(id, color, stay = false) {
		if (color == "OK"){color = "#47c920";}
		if (color == "NG"){color = "#FF0000";}
		clearTimeout(timeout);
		$("#"+id).css("transition", "none");
		$("#"+id).css("background-color", color+"60");
		$("#"+id).css("transition", "background 0.95s cubic-bezier(0, 0, 0.5, 3) 0s");
		if (stay === true) {
			var timeout = setTimeout(function(){
				$("#"+id).css("background-color", color+"30");
				$("#"+id).css("transition", "background 3s ease 0s");
			}, 1000);
		} else if (stay === false){
			var timeout = setTimeout(function(){
				$("#"+id).css("background-color", "none");
				$("#"+id).css("transition", "background 3s ease 0s");
			}, 1000);
		} else {
			var timeout = setTimeout(function(){
				$("#"+id).css("background-color", stay);
				$("#"+id).css("transition", "background 3s ease 0s");
			}, 1000);
		}
	}

	function komunikat(id, text) {
		$("#"+id).empty().append("<span class='puff-out-center'>"+ text +"</span>");
	}


/*
	$("#test").change(function(){
		var piana = $("#piana").val();
		if($(this).val() == 1) {
			$.ajax({
				url: 'ajax_referencje.php',
				type: 'post',
				data: {test:piana},
				dataType: 'json',
				success:function(response){
					$('#tolerancje').empty();
					//$('#tolerancje').append("tablica");
				}
			});
		}
	});
	function projChange(deptid){					// funkcja testowa
		//var deptid = $(this).val();

		$.ajax({
			url: 'ajax_referencje.php',
			type: 'post',
			data: {projekt:deptid},
			dataType: 'json',
			success:function(response){

				var len = response.length;

				//$("#ref").empty();
				$('#tolerancje').empty();
				for( var i = 0; i<len; i++){
					var id = response[i]['id'];
					var num = response[i]['num'];
					if (i > 0)
					{
						num = num.replace(/(\w{4})/g, '$1 ');
					}
					
					$("#ref").append("<option value='"+id+"'>"+num+"</option>");

				}
			}
		});
	}
*/