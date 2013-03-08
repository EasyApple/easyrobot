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

$dbh = "localhost"; //server location (localhost should be ok for this)
$dbn = ""; //database name/prefix
$dbu = ""; //database username
$dbp = ""; //database password


function openDB()
{
	global $dbh,$dbp,$dbu,$dbn;
	$conn = mysql_connect($dbh,$dbu,$dbp,$dbn)or die(mysql_error());
	return $conn;
}

?>