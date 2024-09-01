<?php 
include('inc/header.php');
?>
<title>Quick-Tutoriel.com : Ajout, modification et suppression de données avec Ajax, PHP et MySQL</title>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/data.js"></script>	
<?php include('inc/container.php');?>
<div class="container contact">	
	<h2>Gestion des comptes et des accès du serveur FTP.</h2>	
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   		
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-10">
					<h3 class="panel-title"></h3>
				</div>
				<div class="col-md-2" align="right">
					<button type="button" name="add" id="addEmployee" class="btn btn-success btn-xs">Add User</button>
				</div>
			</div>
		</div>
		<table id="employeeList" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>User</th>
					<th>Status</th>
					<th>Password</th>					
					<th>UID</th>
					<th>GID</th>
					<th>DIR</th>
                                        <th>UP Bandwith</th>
                                        <th>DL Bandwith</th>	
                                        <th>Comment</th>	
                                        <th>IP Access</th>	
                                        <th>Quota Size</th>	
                                        <th>Quota File</th>	
					<th></th>
					<th></th>					
				</tr>
			</thead>
		</table>
	</div>
	<div id="employeeModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="employeeForm">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Modification Utilisateur</h4>
    				</div>
    				<div class="modal-body">
				   <div class="form-group"
                                      label for="user" class="control-label">User</label>
                                      <input type="text" class="form-control" id="empUser" name="empUser" placeholder="User" required>
                                   </div>
                                   <div class="form-group">
                                      <label for="status" class="control-label">Status</label>
                                      <input type="number" class="form-control" id="empStatus" name="empStatus" value="1" placeholder="Status" required>
                                   </div>
                                   <div class="form-group">
                                      <label for="password" class="control-label">Password</label>
                                      <input type="text" class="form-control"  id="empPassword" name="empPassword" placeholder="Password">
                                   </div>
                                   <div class="form-group">
                                      <label for="uid" class="control-label">Uid</label>
                                      <input type="text" class="form-control"  id="empUid" name="empUid" value="2001" placeholder="Uid">
                                   </div>
                                   <div class="form-group">
                                      <label for="gid" class="control-label">Gid</label>
                                      <input type="text" class="form-control" id="empGid" name="empGid" value="2001" placeholder="Gid">
                                   </div>
                                   <div class="form-group">
                                      <label for="dir" class="control-label">Dir</label>
                                      <input type="text" class="form-control"  id="empDir" name="empDir" value="/home/" placeholder="Dir">
                                   </div>
                                   <div class="form-group">
                                      <label for="ulbandwidth" class="control-label">ULBandwidth</label>
                                      <input type="number" class="form-control"  id="empULBandwidth" name="empULBandwidth" value="0" placeholder="ULBandwidth">
                                   </div>
                                   <div class="form-group">
                                      <label for="dlbandwidth" class="control-label">DLBandwidth</label>
                                      <input type="number" class="form-control"  id="empDLBandwidth" name="empDLBandwidth" value="0" placeholder="DLBandwidth">
                                   </div>
                                   <div class="form-group">
                                      <label for="comment" class="control-label">Comment</label>
                                      <input type="text" class="form-control"  id="empComment" name="empComment" placeholder="Comment">
                                   </div>
                                   <div class="form-group">
                                      <label for="ipaccess" class="control-label">IPAccess</label>
                                      <input type="text" class="form-control"  id="empIpaccess" name="empIpaccess" value="*" placeholder="IPAccess">
                                   </div>
                                   <div class="form-group">
                                      <label for="quotasize" class="control-label">QuotaSize</label>
                                      <input type="number" class="form-control"  id="empQuotasize" name="empQuotasize" value="1024" placeholder="QuotaSize">
                                   </div>
                                   <div class="form-group">
                                      <label for="quotafile" class="control-label">QuotaFile</label>
                                      <input type="number" class="form-control"  id="empQuotafile" name="empQuotafile" value="0" placeholder="QuotaFile">
                                   </div>

                                </div>
    				<div class="modal-footer">
    					<input type="hidden" name="action" id="action" value="" />
    					<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
</div>	
<?php include('inc/footer.php');?>
