<?php
require('config.php');
class Employee extends Dbconfig {	
    protected $hostName;
    protected $userName;
    protected $password;
	protected $dbName;
	private $empTable = 'users';
	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 		
			$database = new dbConfig();            
            $this -> hostName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
			$this -> dbName = $database -> dbName;			
            $conn = new mysqli($this->hostName, $this->userName, $this->password, $this->dbName);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}   	
	public function employeeList(){		
		$sqlQuery = "SELECT * FROM ".$this->empTable." ";
	        if(!empty($_POST["search"]["value"])){
                    $sqlQuery .= ' where (User LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR Password LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR Uid LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR Gid LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR Dir LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR ULBandwidth LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR DLBandwidth LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR comment LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR ipaccess LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR QuotaSize LIKE "%'.$_POST["search"]["value"].'%" ';
                    $sqlQuery .= ' OR QuotaFiles LIKE "%'.$_POST["search"]["value"].'%") ';
                }
		if(!empty($_POST["order"])){
			$sqlQuery .= ' ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= ' ORDER BY User ASC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		
		$sqlQuery1 = "SELECT * FROM ".$this->empTable." ";
		$result1 = mysqli_query($this->dbConnect, $sqlQuery1);
		$numRows = mysqli_num_rows($result1);
		
		$employeeData = array();	
		while( $employee = mysqli_fetch_assoc($result) ) {		
			$empRows = array();			
			$empRows[] = $employee['User'];
			$empRows[] = $employee['status'];
			$empRows[] = $employee['Password'];		
			$empRows[] = $employee['Uid'];	
			$empRows[] = $employee['Gid'];
			$empRows[] = $employee['Dir'];
                        $empRows[] = $employee['ULBandwidth'];
			$empRows[] = $employee['DLBandwidth'];
                        $empRows[] = $employee['comment'];
                        $empRows[] = $employee['ipaccess'];
                        $empRows[] = $employee['QuotaSize'];
                        $empRows[] = $employee['QuotaFiles'];			
			$empRows[] = '<button type="button" name="update" User="'.$employee["User"].'" class="btn btn-warning btn-xs update">Update</button>';
			$empRows[] = '<button type="button" name="delete" User="'.$employee["User"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$employeeData[] = $empRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$employeeData
		);
		echo json_encode($output);
	}
	public function getEmployee(){
		if($_POST["empUser"]) {
			$sqlQuery = "
				SELECT * FROM ".$this->empTable." 
				WHERE User  = '".$_POST["empUser"]."'";
			$result = mysqli_query($this->dbConnect, $sqlQuery);	
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			echo json_encode($row);
		}
	}
	public function updateEmployee(){
		if($_POST['empUser']) {	
			$updateQuery = "UPDATE ".$this->empTable." 
			SET User = '".$_POST["empUser"]."', status = '".$_POST["empStatus"]."', Password = MD5('".$_POST["empPassword"]."'), Uid = '".$_POST["empUid"]."', Gid = '".$_POST["empGid"]."', Dir = '".$_POST["empDir"]."', ULBandwidth = '".$_POST["empULBandwidth"]."', DLBandwidth = '".$_POST["empDLBandwidth"]."', comment = '".$_POST["empComment"]."', ipaccess = '".$_POST["empIpaccess"]."', QuotaSize = '".$_POST["empQuotasize"]."', QuotaFiles = '".$_POST["empQuotafile"]."'
			WHERE User ='".$_POST["empUser"]."'";
			$isUpdated = mysqli_query($this->dbConnect, $updateQuery);		
		}	
	}
	public function addEmployee(){
		$_POST['empQuotafile'] ==  1;
		$insertQuery = "INSERT INTO ".$this->empTable." (User, status, Password, Uid, Gid, Dir, ULBandwidth, DLBandwidth, comment, ipaccess, QuotaSize, QuotaFiles) 
			VALUES ('".$_POST["empUser"]."', '".$_POST["empStatus"]."', MD5('".$_POST["empPassword"]."'), '".$_POST["empUid"]."', '".$_POST["empGid"]."', '".$_POST["empDir"]."', '".$_POST["empULBandwidth"]."', '".$_POST["empDLBandwidth"]."', '".$_POST["empComment"]."', '".$_POST["empIpaccess"]."', '".$_POST["empQuotasize"]."', '".$_POST["empQuotafile"]."')";
		$isUpdated = mysqli_query($this->dbConnect, $insertQuery);		
	}
	public function deleteEmployee(){
		if($_POST["empUser"]) {
			$sqlDelete = "
				DELETE FROM ".$this->empTable."
				WHERE User = '".$_POST["empUser"]."'";		
			mysqli_query($this->dbConnect, $sqlDelete);		
		}
	}
}
?>
