<!DOCTYPE html>
<html>
<head>
	<title>Import file in csv</title>
	<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
	<div class="cols-md-6">
		<form action="index.php" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="file">Select CSV File</label>
				<input type="file" name="file" class="form-control"  accept=".xls,.xlsx">
			</div>
			<div>
				<button type="submit" name="import" class="btn btn-primary">Import</button>
			</div>
		</form>
	</div>
	
</div>
</body>
</html>

<?php 
error_reporting(0);
$conn=mysqli_connect("localhost","root","","csv");

require_once('plugin/php-excel-reader/excel_reader2.php');
require_once('plugin/SpreadsheetReader.php');

if (isset($_POST["import"]))
{
$allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
if(in_array($_FILES["file"]["type"],$allowedFileType)){

	// is uploaded file
	 $targetPath = 'uploads/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

        // end is uploaded file

        $Reader = new SpreadsheetReader($targetPath);
        
        $sheetCount = count($Reader->sheets());
        for($i=0;$i<$sheetCount;$i++)
        {
           $Reader->ChangeSheet($i);

           foreach ($Reader as $Row)
            {
                $name = "";
                if(isset($Row[0])) {
                    $name = mysqli_real_escape_string($conn,$Row[0]);
                }


                     $description = "";
                if(isset($Row[1])) {
                    $description = mysqli_real_escape_string($conn,$Row[1]);
                }
                if (!empty($name) || !empty($description)) {
                    $query = "insert into file_data(name,description) values('".$name."','".$description."')";
                    $result = mysqli_query($conn, $query);
                
                    if ($result) {
                        $type = "success";
                        $message = "Excel Data Imported into the Database";
                    } else {
                        $type = "error";
                        $message = "Problem in Importing Excel Data";
                    }
                }


            }

        }
        echo "<script>alert('done')</script>";
        


}
else
  { 
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
  }

}

 ?>