$(document).ready(function(){
	$("#a2_projekt").change(function(){				// zmiana projektu			[a2]
		var deptid = $(this).val();
		//projChange(deptid);

		$.ajax({
			async: false,
			url: 'ajax_a2.php',
			type: 'post',
			data: {projekt:deptid},
			dataType: 'json',
			success:function(response){

				var len = response.length;

				$("#a2Referencje").empty();
				$("#a2_ref").empty();
				for( var i = 0; i<len; i++){
					var id = response[i]['id'];
					var num = response[i]['num'];
					var opt = response[i]['option'];
					if (i > 0)
					{
						num = num.replace(/(\w{4})/g, '$1 ');
					}
					
					//$("#a2_ref").append("<option "+opt+" value='"+id+"'>"+num+"</option>");
					$("#a2Referencje").append("<option "+opt+" id='"+id+"' value='"+num.trim()+"'>");
				}
			}
		});
	});

	$("#a2_ref").keyup(function(e){
		if(e.keyCode == 120 || e.keyCode == 88) $("#a2_ref").change();
	});

	$("#a2_ref").change(function(){				// wpisanie referencji				[a2]
		$("#a2_ref").val($("#a2_ref").val().trim().toUpperCase());
		var projekt = $("#a2_projekt").val();
		var ref = $("#a2_ref").val().replace(' ','');

		//var langAddNew = <?php echo $lang->S_ADD_NEW; ?>;

		if(ref.length != 8) {
			backBlink("a2_kom_projekt", "NG");
			komunikat("a2_kom_projekt", "błędny numer");
			$("#a2_ref").css("background-color", "#ff7d7d75");
		} else {
			$.ajax({
				url: 'ajax_a2.php',
				type: 'post',
				data: {a2RefChange:projekt, a2projekt:projekt, a2ref:ref},
				dataType: 'json',
				success:function(response){
					if(response[0] > 0) {
						backBlink("a2_kom_projekt", "OK");
						komunikat("a2_kom_projekt", "możesz modyfikować");
						$("#a2_ref").css("background-color", "#47c92060");
						$("#a2_projekt_zapisz").val("zapisz");
						for(var i = 1; i < 5; i++)
							response[1].linia.includes(i) ? $("#line_"+i).prop("checked", true) : $("#line_"+i).prop("checked", false);
						$("#a2_indeks").val(response[1].indeks).change();
						if(response[1].aktywny == 1) {
							$("#ref_info_aktywny").prop("checked", true);
							$("#tr_info_aktywny").css("background-color", "#47c92060");
						} else {
							$("#ref_info_aktywny").prop("checked", false);
							$("#tr_info_aktywny").css("background-color", "#ff7d7d75");
						}
					} else {
						backBlink("a2_kom_projekt", "NG");
						komunikat("a2_kom_projekt", "dodajesz nowy?");
						$("#a2_ref").css("background-color", "#ff7d7d75");
						$("#a2_projekt_zapisz").val("dodaj");
					}
				},
				error:function(){
					backBlink("a2_kom_projekt", "NG");
					komunikat("a2_kom_projekt", "błąd odczytu");
				}
			});
		}
	});

	$("#ref_info_aktywny").change(function(){
		var aktywny = $("#ref_info_aktywny").prop("checked");
		if(aktywny){
			$("#tr_info_aktywny").css("background-color", "#47c92060");
		} else {
			$("#tr_info_aktywny").css("background-color", "#ff7d7d75");
		}
	});

	$("#a2_projekt_zapisz").bind('keyup mouseup', function(){	// zapis projektu	[a2]
		var projekt = $("#a2_projekt").val();
		var ref = $("#a2_ref").val().replace(' ','');
		var indeks = $("#a2_indeks").val();
		var aktywny = $("#ref_info_aktywny").prop("checked");

		var linia1 = $("#line_1").prop('checked') ? 1 : 0;
		var linia2 = $("#line_2").prop('checked') ? 2 : 0;
		var linia3 = $("#line_3").prop('checked') ? 3 : 0;
		var linia4 = $("#line_4").prop('checked') ? 4 : 0;

		var linia = linia1 + "" + linia2 + "" + linia3 + "" + linia4;
		
		//if(projekt || ref || indeks || (linia1 || linia2 || linia3 || linia4) ) {
		if(!projekt || !ref) {
			//$("#tes").addClass("puff-out-center");
			backBlink("a2_kom_projekt", "NG");
			komunikat("a2_kom_projekt", "wszystkie pola wymagane");
		} else {
			$.ajax({
				url: 'ajax_a2.php',
				type: 'post',
				data: {test:projekt, a2Projekt:projekt, a2Ref:ref, a2indeks:indeks, a2linia:linia, infoProjekt:aktywny},
				dataType: 'json',
				success:function(response){
					if(!response.err) {
						backBlink("a2_kom_projekt", "OK");
						komunikat("a2_kom_projekt", "zapisane");
						zapisano();
					} else {
						backBlink("a2_kom_projekt", "NG");
						komunikat("a2_kom_projekt", "błąd");
					}
				},
				error:function(){
					backBlink("a2_kom_projekt", "NG");
					komunikat("a2_kom_projekt", "błąd");
				}
			});
		}
	});
});