$(document).ready(function(){
	$("#a1_projekt").change(function(){
		var projekt = $(this).val();
		$.ajax({
			url: 'ajax_a1.php',
			type: 'post',
			data: {a1Projekt:projekt},
			dataType: 'json',
			success:function(response){
				if(response.aktywny == 1) {
					$("#projekt_info_aktywny").prop("checked", true);
					$("#tr_info_aktywny").css("background-color", "#47c92060");
				} else {
					$("#projekt_info_aktywny").prop("checked", false);
					$("#tr_info_aktywny").css("background-color", "#ff7d7d75");
				}
				//$("#projekt_info_klient").empty();
				$("#projekt_info_klient").val(response.klient);
			}
		});
	});
	$("#projekt_info_aktywny").change(function(){
		var aktywny = $("#projekt_info_aktywny").prop("checked");
		if(aktywny){
			$("#tr_info_aktywny").css("background-color", "#47c92060");
		} else {
			$("#tr_info_aktywny").css("background-color", "#ff7d7d75");
		}
	});
	$("#nowy_projekt_zapisz").bind('keyup mouseup', function(){
		var string = $("#nowy_projekt").val();
		if(!string) {
			backBlink("nowy", "NG");
		} else {
			$.ajax({
				url: 'ajax_a1.php',
				type: 'post',
				data: {nowyProjekt:string},
				dataType: 'json',
				success:function(response){
					if(!response.err) {
						backBlink("nowy", "OK");
						$("#nowy_projekt").empty();
						setTimeout(function(){
							location.reload();
						}, 2000);
					} else {
						backBlink("nowy", "NG");
					}
				},
				error:function(){
					backBlink("nowy", "NG");
				}
			});
		}
	});
	$("#projekt_info_zapisz").bind('keyup mouseup', function(){
		var projekt = $("#a1_projekt").val();
		var aktywny = $("#projekt_info_aktywny").prop("checked");
		//$("#projekt_info_klient").val(aktywny); // wpisanie wartoœci zmiennej do pola
		var klient = $("#projekt_info_klient").val();
		$.ajax({
			url: 'ajax_a1.php',
			type: 'post',
			data: {infoProjekt:aktywny, pNazwa:projekt, pKlient:klient},
			dataType: 'json',
			success:function(response){
				if(!response.err) {
					backBlink("tr_info_zapisz", "OK");
				} else {
					backBlink("tr_info_zapisz", "NG");
				}
			},
			error:function(){
				backBlink("tr_info_zapisz", "NG");
			}
		});
	});
});