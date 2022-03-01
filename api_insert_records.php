<?php

$response = array();
$response['last_insert_record'] = -1;

//get data from curl request
$data = json_decode(file_get_contents('php://input'), true);
//print_r($data);

$records = json_decode($data['records'], true);
$table = $data['table'];

$response['num_records'] = count($records);

//opnen dB connection
$dbconn = pg_connect("host=pg_container port=5432 dbname=postgres user=root password=root");
if(!$dbconn){$response['responce_type'] = 'fail';}

foreach ($records as $key => $record) {
  //Create insert query
  $insert_sql = "INSERT INTO " . $table . " ( ";
  foreach ($record as $col_name => $value) {
    $insert_sql.= " " . strtolower($col_name) . ",";
  }
  $insert_sql[strlen($insert_sql)-1] = ')';
  $insert_sql.= " VALUES (";
  foreach ($record as $col_name => $value) {
    $insert_sql.= " '" . $value . "',";
  }
  $insert_sql[strlen($insert_sql)-1] = ")";

  //Create insert one record
  $result = pg_query($dbconn, $insert_sql);
  if (!$result) {
    $response['responce_type'] = 'fail';
    break;
  }
  else{
    $response['responce_type'] = 'success';
    $response['last_insert_record'] = $record['ID'];
  }
}

//close dB connection
pg_close($dbconn);

//echo json output
echo json_encode($response);
?>
