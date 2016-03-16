(function(module){
    module.init = function(){
		var initData = JSON.parse($('#init-data').val());
		if(initData.fertilizing_machine_type == 1){
			$('#optionsRadios1').attr('checked',true);
			$("#machine_type_1").css("display", "block");
			$("#machine_type_2").css("display", "none");
            initData.sub_fertilizer_usual_amount=0;
            $("#new2").attr("disabled","disabled");
		}else{
			$('#optionsRadios2').attr('checked',true);
			$("#machine_type_2").css("display", "block");
			$("#machine_type_1").css("display", "none");
		}
		$('input[name="control_methodology"][value="'+initData.control_methodology+'"]').prop('checked', true);
		$('#mesh_size').val(initData.mesh_size);
		$('input[name="main_fertilizer_usual_amount"]').val(initData.main_fertilizer_usual_amount);
		$('input[name="sub_fertilizer_usual_amount"]').val(initData.sub_fertilizer_usual_amount);
		$('#fertilizer_standard_definition_id').val(initData.fertilizer_standard_definition_id);
        $('#fertilizer_notes').text(initData.fertilizerStandard.notes);
        $('#fertilizer_range').text(initData.fertilizerStandard.range_of_application);
		$('input[name="soil_analysis_type"][value="'+initData.soil_analysis_type+'"]').prop('checked', true);
		if(initData.soil_analysis_type === "1"){
			$("#analysis_type_2").css("display", "none");
		}else if(initData.soil_analysis_type === "2"){
			$("#analysis_type_2").css("display", "block");
			$("#analysis_type_2_cover").css("display", "none");
		}
		creatingmap.changeOptions(initData.p,initData.k);
		$('#p option[value="'+Number(initData.k).toFixed(2).toString() +'"]').attr('selected', 'selected');
		$('#k option[value="'+Number(initData.k).toFixed(2).toString() +'"]').attr('selected', 'selected');
		
		creatingmap.sumTable3();
		creatingmap.sumTable4();
		creatingmap.changeControlMethodology();
		$('#fixed_fertilizer_amount'+initData.control_methodology).val(initData.fixed_fertilizer_amount);
    };
})(fertilizerMap ={});