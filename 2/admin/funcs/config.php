<?PHP
//-----------------------------------------------------------------------------------------------
//My Program-O Version 1.0.1
//Program-O  chatbot admin area
//Written by Elizabeth Perreau
//Feb 2010
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
//db config file
//you might want to make thise different to the program-o chatbot user as you need privs to insert, delete, create tables.

//$dbh = "localhost"; //server location (localhost should be ok for this)
//$dbn = ""; //database name/prefix
//$dbu = ""; //database username
//$dbp = ""; //database password

$dbh = SAE_MYSQL_HOST_M; //server location (localhost should be ok for this)
$dbn = SAE_MYSQL_DB; //database name/prefix
$dbu = SAE_MYSQL_USER; //database username
$dbp = SAE_MYSQL_PASS; //database password


function openDB()
{
	global $dbh,$dbp,$dbu,$dbn;
	//$conn = mysql_connect($dbh,$dbu,$dbp,$dbn)or die(mysql_error());

	$conn = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
	if (!$conn)
		die ("<b>Cannot connect to database, check if username, password and host are correct.</b>");
	$success = mysql_select_db(SAE_MYSQL_DB,$conn);

	return $conn;
}

?>