function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}


$(document).ready(function(){
	// click edycja
	$(".user").click(function() {
		//$(this).prop('disabled', true);
		
		var id = $(this).attr('name').replace('id_', '');
		var name = "N/A";
		$(this).html("<img style='width:16px' src='load2.gif' />");
		//$("#" + id).slideUp("slow");
		//$(".s_" + id).slideUp("slow");
		sleep(500).then(() => {
			if ($(this).hasClass('edit')) {
				$(".ic_" + id).slideUp("fast");
				name = $("#name_" + id).val();
				$(".name_" + id).text(name);
				console.log("up");
				$(this).html("<img src='/phpmyadmin/themes/original/img/b_edit.png' />").removeClass("edit");
			} else {
				$(".ic_" + id).slideDown("fast");
				name = $(".name_" + id).text();
				$(".name_" + id).html("<input id='name_" + id + "' class='input' type='text' value='" + name + "' />");
				console.log("down");
				$(this).html("▲").addClass("edit");
			}
		});
	});
});

/*
	$("#translate").delegate("th", "click", function() {	// zapisanie zmienionego stringa	[a8] - translate
		var id = $(this).attr('id');
		var textId	 = "#T_" + id;
		var lang = $(textId).attr("name");
		if (!$(textId).val()) {
			//$(textId).val(lang);
			backBlink("TR_"+id, "NG");
		} else {
			$.ajax({
				url: 'ajax_a8.php',
				type: 'post',
				data: {translate:lang,string:id,text:$(textId).val()},
				dataType: 'json',
				success:function(response){
					backBlink("TR_"+id, "OK");
					$.ajax({
						url: 'lang.php',
						type: 'post'
					});
				},
				error:function(){
					backBlink("TR_"+id, "NG");
				}
			});
		}
	});
	$("#nowy_zapisz").bind('keyup mouseup', function(){		// dodanie nowego stringa			[a8] - translate
		var string = $("#nowy_str").val();
		var text = $("#nowy_tr").val();
		if(!string || !text) {
			backBlink("nowy", "NG");
		} else {
			$.ajax({
				url: 'ajax_a8.php',
				type: 'post',
				data: {sString:string,sText:text},
				dataType: 'json',
				success:function(response){
					if(!response.err) {
						backBlink("nowy", "OK");
						$("#nowy_str").empty();
						$("#nowy_tr").empty();
						$.ajax({
							url: 'lang.php',
							type: 'post'
						});
						setTimeout(function(){
							location.reload();
						}, 2000);
					} else {
						
					}
				},
				error:function(){
					backBlink("nowy", "NG");
				}
			});
		}
	});
*/