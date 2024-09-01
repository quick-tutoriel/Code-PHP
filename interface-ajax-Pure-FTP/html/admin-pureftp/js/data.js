$(document).ready(function(){	
	var employeeData = $('#employeeList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"paging": true,
		"order":[],
		"ajax":{
			url:"action.php",
			type:"POST",
			data:{action:'listEmployee'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0,1,2,3,4,5,6,7,8,9,10,11,12,13],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	$('#addEmployee').click(function(){
		$('#employeeModal').modal('show');
		$('#employeeForm')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Employee");
		$('#action').val('addEmployee');
		$('#save').val('Add');
	});		
	$("#employeeList").on('click', '.update', function(){
		var empUser = $(this).attr("User");
		var action = 'getEmployee';
		$.ajax({
			url:'action.php',
			method:"POST",
			data:{empUser:empUser, action:action},
			dataType:"json",
			success:function(data){
				$('#employeeModal').modal('show');
				$('#empUser').val(data.User);
                                $('#empStatus').val(data.status);
                                $('#empPassword').val(data.Password);
                                $('#empUid').val(data.Uid);
                                $('#empGid').val(data.Gid);
                                $('#empDir').val(data.Dir);
                                $('#empULBandwidth').val(data.ULBandwidth);
                                $('#empDLBandwidth').val(data.DLBandwidth);
                                $('#empComment').val(data.comment);
                                $('#empIpaccess').val(data.ipaccess);
                                $('#empQuotasize').val(data.QuotaSize);
                                $('#empQuotafile').val(data.QuotaFiles);
				$('.modal-title').html("<i class='fa fa-plus'></i> Modification Utilisateur");
				$('#action').val('updateEmployee');
				$('#save').val('Save');
			}
		})
	});
	$("#employeeModal").on('submit','#employeeForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#employeeForm')[0].reset();
				$('#employeeModal').modal('hide');				
				$('#save').attr('disabled', false);
				employeeData.ajax.reload();
			}
		})
	});		
	$("#employeeList").on('click', '.delete', function(){
		var empUser = $(this).attr("User");		
		var action = "empDelete";
		if(confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
			$.ajax({
				url:"action.php",
				method:"POST",
				data:{empUser:empUser, action:action},
				success:function(data) {					
					employeeData.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
});
