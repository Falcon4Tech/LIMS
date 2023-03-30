$(document).ready(function(){
	$("#piana").bind('keyup mouseup', function(){	// wpisanie numeru piany	[u1] - pomiary
		if($(this).val().length > 6) { 
			var deptid = $(this).val();
			$.ajax({
				//async: false,
				url: 'ajax_u1.php',
				type: 'post',
				data: {piana:deptid},
				dataType: 'json',
				success:function(response){
					//var len = response.length;
					//var proj = response['projekt_id'];
					
					if (response['projekt'] > 0){
						$("#piana").css("background-color", "#47c92060");
						backBlink("p_piana", "#47c92060");
						$("#projekt").val(response['projekt']).change();
						$("#ref").val(response['reff']).change();
						$("#data").val(response['data']).change();
						$("#indeks").val(response['indeks']).change();
						$('#zm' + response['zmiana']).prop("checked", true);
					} else {
						$("#piana").css("background-color", "#ff7d7d75");
						backBlink("p_piana", "#ff7d7d75");
					}
					/*setTimeout(function(){
						$("#ref").val(response['reff']).change();
					}, 750);*/
				}
			});
		}
	});
	$("#projekt").change(function(){				// zmiana projektu			[u1]
		var deptid = $(this).val();
		//projChange(deptid);

		$.ajax({
			async: false,
			url: 'ajax_u1.php',
			type: 'post',
			data: {projekt:deptid},
			dataType: 'json',
			success:function(response){

				var len = response.length;

				$("#ref").empty();
				$('#tolerancje').empty();
				for( var i = 0; i<len; i++){
					var id = response[i]['id'];
					var num = response[i]['num'];
					var opt = response[i]['option'];
					if (i > 0)
					{
						num = num.replace(/(\w{4})/g, '$1 ');
					}
					
					$("#ref").append("<option "+opt+" value='"+id+"'>"+num+"</option>");
				}
			}
		});
	});
	$("#ref").change(function(){					// zmiana numeru ref.		[u1] - pomiary
		var deptid = $(this).val();

		$.ajax({
			//async: false,
			url: 'ajax_u1.php',
			type: 'post',
			data: {refe:deptid},
			dataType: 'json',
			success:function(response){

				var len = response.length;

				$("#linia").empty();
				$("#indeks").val(response[0]['indeks']);
				for( var i = 0; i<len; i++){
					var id = response[i]['id'];
					var num = response[i]['num'];
					var opt = response[i]['option'];
					$("#linia").append("<option "+opt+" value='"+id+"'>"+num+"</option>");
				}
			}
		});
		$.ajax({
			async: false,
			url: 'ajax_u1.php',
			type: 'post',
			data: {tol:deptid},
			dataType: 'json',
			success:function(response){

				var len = response.length;

				$('#tolerancje').empty();
				for(var i = 0; i<len; i++) {
					var id = response[i]['id'];
					var nom = response[i]['nom'];

					$("#tolerancje").append(id + " " + nom + "<br/>");
				}
			}
		});
	});
});