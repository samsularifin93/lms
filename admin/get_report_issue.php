<?php

//issue_book.php

include '../database_connection.php';

include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

function convert_date($date_sample){
	$explode = explode("/", $date_sample);
	$date_export = $explode[2]."-".$explode[1]."-".$explode[0];
	return $date_export;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Report Data</title>
</head>
<body>
	<style type="text/css">
	body{
		font-family: sans-serif;
	}
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
 
	}
	a{
		background: blue;
		color: #fff;
		padding: 8px 10px;
		text-decoration: none;
		border-radius: 2px;
	}
	</style>
 
	<?php
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=Data Report.xls");
	?>
 
	<center>
		<h1>Report Data</h1>
	</center>
 
	<table border="1">
		<tr>
			<th>Tape Number</th>
			<th>Tape Label</th>
			<th>User Unique ID</th>
			<th>Issue Date</th>
			<th>Return Date</th>
			<th>Status</th>
		</tr>
<?php
$error = '';

if(isset($_POST["issue_book_button"]))
{
    $formdata = array();
	
	$query = "
             SELECT a.book_id AS tape_number, a.user_id AS user_nik, a.issue_date_time AS tanggal_pinjam, a.return_date_time AS tanggal_kembali, a.book_issue_status AS tape_status, b.book_name AS tape_label FROM lms_issue_book AS a LEFT JOIN lms_book AS b ON a.book_id = b.book_isbn_number ";
	
	if(empty($_POST["start"]) || empty($_POST["end"]))
    {
        $formdata['start'] = '';
		$formdata['end'] = '';
    }
    else
    {
		if(!(strpos($query, "WHERE") > 0)){
			$query .= " WHERE ";
		}
		
        $formdata['start'] = trim($_POST['start']);
		$formdata['end'] = trim($_POST['end']);
		
		$query .= "
            a.issue_date_time >= '".$formdata['start']."' AND a.issue_date_time <= '".$formdata['end']."' ";
		
		$query .= "
            AND a.return_date_time >= '".$formdata['start']."' AND a.return_date_time <= '".$formdata['end']."' ";
    }
	
    if(empty($_POST["book_id"]))
    {
        $formdata['book_id'] = '';
    }
    else
    {
        $formdata['book_id'] = trim($_POST['book_id']);
		
		if(!(strpos($query, "WHERE") > 0)){
			$query .= " WHERE ";
		}
		
		$query .= "
            a.book_id='".$formdata['book_id']."' ";
    }

    if(empty($_POST["user_id"]))
    {
        $formdata['user_id'] = '';
    }
    else
    {
        $formdata['user_id'] = trim($_POST['user_id']);
		
		if(!(strpos($query, "WHERE") > 0)){
			$query .= " WHERE ";
		}
		
		if(strpos($query, "a.book_id") > 0){
			$query .= " AND ";
		}
		
		$query .= "
            a.user_id='".$formdata['user_id']."' ";
    }
	
	if(empty($_POST["status"]))
    {
        $formdata['status'] = '';
    }
    else
    {
        $formdata['status'] = trim($_POST['status']);
		
		if(!(strpos($query, "WHERE") > 0)){
			$query .= " WHERE ";
		}
		
		if(strpos($query, "a.user_id") > 0){
			$query .= " AND ";
		}
		
		$query .= "
            a.book_issue_status='".$formdata['status']."' ";
    }
	

    if($error == '')
    {
        $queryx = "
            SELECT a.book_id AS tape_number, a.user_id AS user_nik, a.issue_date_time AS tanggal_pinjam, a.return_date_time AS tanggal_kembali, a.book_issue_status AS tape_status, b.book_name AS tape_label FROM lms_issue_book AS a LEFT JOIN lms_book AS b ON a.book_id = b.book_isbn_number WHERE
            a.book_id='".$formdata['book_id']."' AND a.user_id='".$formdata['user_id']."' AND a.book_issue_status='".$formdata['status']."'
            ";

			$statement = $connect->prepare($query);

            $statement->execute();
									
			if($statement->rowCount() > 0){
					foreach($statement->fetchAll() as $row_data)
					{
							echo "<tr>";
							echo "<td>".$row_data['tape_number']."</td>";
							echo "<td>".$row_data['tape_label']."</td>";
							echo "<td>".$row_data['user_nik']."</td>";
							echo "<td>".$row_data['tanggal_pinjam']."</td>";
							echo "<td>".$row_data['tanggal_kembali']."</td>";
							echo "<td>".$row_data['tape_status']."</td>";
							echo "</tr>";
					}	
			}
    }
    else{
        $error .= '<li>No Result</li>';
    }
	
	echo "</table></body></html>";
}

$queryx = "
	SELECT * FROM lms_issue_book 
    ORDER BY issue_book_id DESC
";

$statement = $connect->prepare($query);

$statement->execute();