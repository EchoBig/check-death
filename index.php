<!DOCTYPE html>
<html>
<head>
	<title>ตรวจสอบคนตาย สปสช.</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css?family=Niramit|Taviraj&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<style>
		body{
			font-family: 'Taviraj', serif;
			font-size: 18px;
		}

		h2{
			font-family: 'Niramit', sans-serif;
		}
	</style>
</head>
<body  class="bg-light">
	<div class="container">

		<!-- <div class="row text-center"> -->
			<div class="py-5">
				<h2 class="text-center">ตรวจสอบคนตาย สปสช.</h2>
			</div>
		<!-- </div> -->
		<div class="row">
			<div class="col-md-12">
				<!-- <form id="frm" method="post" action="sql_check.php"> -->
				<form id="frm">
					<div class="row">
			          <div class="col-md-6 mb-3">
			            <label>CID เจ้าหน้าที่ตรวจสอบ</label>
			            <input type="text" class="form-control" name="cid_staft"  required>
			          </div>
			          <div class="col-md-6 mb-3">
			            <label>รหัส NHSO Token <small class="text-danger"> ** ได้จากโปรแกรม nhsoauthen4.x</small></label>
			            <input type="text" class="form-control" name="token" required>
			          </div>
			          <div class="col-md-12 mb-3">
			            <label>CID ประชากรที่รับผิดชอบ <small class="text-danger"> ** ระบุเลขประจำตัวประชาชนบรรทัดละคน</small></label>
			            <textarea rows="15" class="form-control" name="cid_person" id="cid_person"></textarea>
			          </div>
			        </div>
			        <button class="btn btn-block btn-primary btn-lg" type="submit" id="btnsmt">ตรวจสอบ <i class="fa fa-check-square-o" aria-hidden="true"></i></button>
				</form>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-12">
				<div id="before" style="display:none;">
					<div class="d-flex justify-content-center">
					  <div class="spinner-border text-warning" role="status">
					    <span class="sr-only">Loading...</span>
					  </div>
					</div>
				</div>
				<div id="result"></div>
			</div>
		</div>
	</div>

</body>
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
	$(document).ready(function(){
		$('#frm').on('submit', function(event){
          event.preventDefault();
			$.ajax({
				url:'sql_check.php',
				method:'POST',
				data:$(this).serialize(),
				dataType:'json',
				beforeSend: function() {
	              $("#before").show();
	              $('#btnsmt').prop('disabled', true);
	           	},
				success:function(data){
					$("#before").hide();
					$('#btnsmt').prop('disabled', false);
					if (data.status == false) {
						$('#result').html(data.st_desc);
					}else{
						$('#cid_person').val('');
						if (data.count > 0) {
							$('#result').html(data.load_file);
						}
						else{
							$('#result').html('<div class="card"><div class="card-body"><a class="text-danger">ไม่มีคนตาย</a></div></div>');
						}
					}
				}
			});
		});
	});
</script>
</html>