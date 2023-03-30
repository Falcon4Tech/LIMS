$(document).ready(function(){
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
});