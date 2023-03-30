function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}

$(document).ready(function (e) {
	$(".m").on('change',delay(function() {
		var pole = $(this).val();
		var nazwa = $(this).attr("name");
		var id = $("#idd").attr("name");
		//alert("zmiana " + pole + " (" + nazwa + ")");
		console.log(nazwa + " / " + pole);

		$.ajax({
			url: "ajax_u3.php",
			type: "POST",
			dataType: "json",
			data: {input:true, name:nazwa, value:pole, idd:id},
			success: function(resp) {
				backBlink("tr_"+nazwa, "OK", true);
				//komunikat("a2_kom_projekt", "zapisane");
				zapisano();
			},
			error:function(){
				backBlink("tr_"+nazwa, "NG", true);
				//komunikat("a2_kom_projekt", "błąd");
			}
		});
	}, 500));
	/*$(".mc").click(function() {
		if ($(this).is(':checked')) {
			var pole = $(this).val();
			var nazwa = $(this).attr("name");
			alert("zmiana " + pole + " (" + nazwa + ")");
		}
	});*/

	$("#u3_projekt").change(function(){				// zmiana projektu			[u3]
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
				//$('#tolerancje').empty();
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
});

/*
	upload plików
*/
$(document).ready(function (e) {
	/*$('.up').on('change', function() {
		var na = $(this).attr("id");
		var file_data = $(na).prop('files')[0];   
		var form_data = new FormData();                  
		form_data.append('file', file_data);
		alert(form_data);                             
		$.ajax({
			url: 'ajax_u3.php', // point to server-side PHP script 
			dataType: 'text',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(php_script_response){
				alert(php_script_response); // display response from the PHP script, if any
			}
		 });
	});*/
	$(".up").on('change',(function(e) {
		// append file input to form data
		
		var id = $(this).attr("id");
		var idd = $("#idd").attr("name");
		var nazwa = $(this).attr("name");
			$("#png_"+nazwa).empty();
			$("#png_"+nazwa).append("<img class='photo' src='load2.gif' />");
		var fileInput = document.getElementById(id);
		var file = fileInput.files[0];
		var formData = new FormData();
		formData.append('file', file);

		$.ajax({
			url: "ajax_u3.php",
			type: "POST",
			data: formData,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data) {
				//alert(data);
				if(data[0] > 100000) {
					$.ajax({
						url: "ajax_u3.php",
						type: "POST",
						data: {section:id, report:idd, id:data[0], ext:data[1], img:data[2]},
						dataType: 'json',
						success:function(resp){
							backBlink("tr_"+nazwa, "OK", true);
							$("#png_"+nazwa).empty();
							$("#png_"+nazwa).append("<a href='section/" + data[2] + "." + data[1] + "' target='_blank'><img class='photo' src='photo.png' /></a>");
						}
					});
				}
			},
			error: function(e) {
				backBlink("tr_"+nazwa, "NG", true);
			}
		});
	}));
});

/*
	lista raportów
*/
$(document).ready(function (e) {
	$("#filtr_proj,#filtr_ref,#filtr_date,#filtr_order").keyup(delay(function(e){
		$(this).val($(this).val().trim().toUpperCase());
		var p = $("#filtr_proj").val().replace(/[^a-z0-9]/gi,'');
		var r = $("#filtr_ref").val().replace(/[^x0-9]/gi,'');
		var d = $("#filtr_date").val().replace(/[^\-0-9]/gi,'');
		var o = $("#filtr_order").val().replace(/[^0-9]/gi,'');
		var lan = $("#pageLang").val();

		var regProj= new RegExp(p, "i");
		var regRef = new RegExp(r, "i");
		var regDate= new RegExp("^"+d, "i");
		var regOrd = new RegExp("^"+o, "i");

		var pad = "F00";

		if ($(this).val().length < 2 && $(this).val().length != 0) return;
		
		$.ajax({
			url: 'ajax_u3.php',
			type: 'post',
			data: {fProjekt:p,fRef:r,fDate:d,fOrder:o},
			dataType: 'json',
			success: function(response) {
				$("#lista").empty();
				$.each(response, function(i, el) {
					$("#lista").append(
						"<tr>" + 
							"<td>" + (i+1) + "</td>" + 
							"<td>" + el.nazwa.replace(regProj, '<f>'+p+'</f>') + "</td>" +
							"<td>" + el.ref.replace(regRef, '<f>'+r+'</f>') + "</td>" +
							"<td>" + pad.slice(0,-el.tool.length) + el.tool + "</td>" +
							"<td>" + el.date.replace(regDate, '<f>'+d+'</f>') + "</td>" +
							"<td>" + el.order.replace(regOrd, '<f>'+o+'</f>') + "</td>" +
							"<td><a href='" + lan + "-u3-" + el.idd + ".lims'><img src='/phpmyadmin/themes/original/img/b_edit.png' /></a></td>" + 
							"<td><img src='/phpmyadmin/themes/original/img/b_print.png' /></td>" +
						"</tr>"
					);
				});
				backBlink("lista", "OK", false);
			}
		});
	}, 500));
	/*$("#filtr_proj").keyup(function(){
		$(this).val($(this).val().trim().toUpperCase());
		var input = (this).val();
	});*/
});