$(document).ready(function(){
	$("#tol_ref").change(function(){				// zmiana numeru ref.		[a3] - tolerancje
		var deptid = $(this).val();

		$.ajax({
			//async: false,
			url: 'ajax_a3.php',
			type: 'post',
			data: {tol_refe:deptid},
			dataType: 'json',
			success:function(response){
				$("#indeks").val(response.num).change();
				for(var i = 1; i < 20; i++) $("#indeks option:eq("+ i +")").removeAttr("disabled");
				for(var i = response.max; i < 20; 1) {
					i++;
					$("#indeks option:eq("+ i +")").prop('disabled', 'disabled');
				}
			}
		});
		$.ajax({
			url: 'ajax_a3.php',
			type: 'post',
			data: {tolerancje:deptid},
			dataType: 'json',
			success:function(response){
				var len = response.length;
				$("#a3Tolerancje tr.a").remove();
				for(var i = 0; i<len; i++){
					var check = " ";
					if(response[i].procent == 1) check = " checked ";
					var qwe = "<tr class='a'>";
					qwe += "<th>" + response[i].nazwa + ":</th>";
					qwe += "<td><input type='number' step='0.1' class='input' value='" + response[i].nom + "'/></td>";
					qwe += "<td><input type='number' step='0.1' class='input' value='" + response[i].dev + "'/><input"+check+"type='checkbox' value='1'/>%</td>";
					qwe += "</tr>";
					$("#a3Tolerancje").append(qwe);
				}
			}
		});
	});
	$("#tol_projekt").change(function(){			// zmiana projektu			[a3] - tolerancje
		var deptid = $(this).val();
		//projChange(deptid);

		$.ajax({
			async: false,
			url: 'ajax_a3.php',
			type: 'post',
			data: {projekt:deptid},
			dataType: 'json',
			success:function(response){

				var len = response.length;

				$("#tol_ref").empty();
				for( var i = 0; i<len; i++){
					var id = response[i]['id'];
					var num = response[i]['num'];
					var opt = response[i]['option'];
					if (i > 0)
					{
						num = num.replace(/(\w{4})/g, '$1 ');
					}
					
					$("#tol_ref").append("<option "+opt+" value='"+id+"'>"+num+"</option>");
				}
			}
		});
	});
});