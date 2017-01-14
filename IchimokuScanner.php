<?php
/* This file must be renamed to index.php absolutely for it to receive data from remote Metatrader 5 Platform with IchimokuExperimental001.mq5 */
/* Developed on 000webhost */
define("MYSQL_SERVER", "localhost");
define("MYSQL_USER", "id517966_ichimoku");
define("MYSQL_PASSWORD", "thepassword");
define("MYSQL_DB", "id517966_ichimoku");
define("MAIN_TABLE_NAME","notification");
//supprimer tous les messages dans la table des notifications
if (isset($_GET['reset_all'])) {
	$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
	if ($db->connect_errno) {
		exit;
	}
	$r = mysqli_query($db, "delete from notification");
	$r = mysqli_query($db, "delete from ssb_alert");
	$db->close();
	exit;
}
if (isset($_GET['reset_notifications'])) {
	$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
	if ($db->connect_errno) {
		exit;
	}
	$r = mysqli_query($db, "delete from notification");
	$db->close();
	exit;
}
if (isset($_GET['reset_ssb_alerts'])) {
	$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
	if ($db->connect_errno) {
		exit;
	}
	$r = mysqli_query($db, "delete from ssb_alert");
	$db->close();
	exit;
}
echo "<html><head><style>table, th, td { border: 1px solid black; border-collapse: collapse; } ";
echo "th, td { padding: 5px; text-align: left; } </style><title></title></head><body>";
echo "<h2>Ichimoku Scanner</h2>";
echo "<h3>Experimental version</h3><a href='http://traderetgagner.blogspot.spot'>traderetgagner.blogspot.spot</a><br/>";
if (isset($_GET['notification'])) {
	$notification = $_GET['notification'];
	echo "received=  [[$notification]]<br/>";
	$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
	if ($db->connect_errno) {
		echo "Error : " . $db->connect_errno . " <br/>";
		exit;
	}
	$r = mysqli_query($db, "select * from notification");
	if ($r->num_rows == 0){
		echo 'No notifications.<br/>';
	} else {
		echo $r->num_rows . " notifications in table<br/>";
	}
	$timestamp = (new DateTime())->format('Y-m-d H:i:s');
	$r = mysqli_query($db, "insert into " . MAIN_TABLE_NAME . " (timestamp, message) values ('" . $timestamp . "', '" . $notification . "')");
	if (DEBUG) echo 'Notification recorded OK.<br/>';
	$db->close();
	exit;
}
if (isset($_GET['upload_ssb_alert'])) {
	$ssbalert = $_GET['upload_ssb_alert'];
	echo "received=  [[$ssbalert]]<br/>";
	$array = explode(";", $ssbalert);
	if (count($array)==6){
		$timestamp = $array[0];
		$period = $array[1];
		$name = $array[2];
		$type = $array[3];
		$price = $array[4];
		$ssb = $array[5];
		$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
		if ($db->connect_errno) {
			echo "Error : " . $db->connect_errno . " <br/>";
			exit;
		}
		$r = mysqli_query($db, "select * from notification");
		if ($r->num_rows == 0){
			echo 'No notifications.<br/>';
		} else {
			echo $r->num_rows . " notifications in table<br/>";
		}
		//$timestamp = (new DateTime())->format('Y-m-d H:i:s');
		$r = mysqli_query($db, "insert into ssb_alert (timestamp, period, name, type, price, ssb) values ('" . $timestamp . "', '" . $period . "', '" . $name . "', '" . $type . "', " . $price . ", " . $ssb . ")");
		if (DEBUG) echo 'SSB alert recorded OK.<br/>';
		$db->close();
		exit;
	}
}
$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
if ($db->connect_errno) {
	echo "Error : " . $db->connect_errno . " <br/>";
	exit;
}
$r = mysqli_query($db, "select distinct(name) from ssb_alert");
$arrayname = array();
if ($r->num_rows == 0){
	echo 'No SSB alert.<br/>';
} else {
	if ($r->num_rows > 0) {
		while($row = $r->fetch_assoc()) {
			//echo '<br/>';
			//echo $row["name"] . '<br/>';
			array_push($arrayname, $row["name"]);
		}
	} else {
		//echo "0 results";
	}
}
sort($arrayname);
$today = ((new DateTime)->format("Y-m-d"));
$showOnlyToday = false;

if (isset($_GET['today'])) {
	$showOnlyToday = true;
}

echo '<br/>';
if ($showOnlyToday){
	echo 'List of today SSB alerts (' . $today . ')<br/>';
} else {
	echo 'List of all SSB alerts<br/>';
}
echo 'Timestamp/Period/Name/Type of price detected/Price/SSB<br/>';
foreach($arrayname as $name){
	echo '<br/>';
	echo $name . "<br/>";
	if ($showOnlyToday){
		$r = mysqli_query($db, "select * from ssb_alert where name='" . $name . "' and timestamp like '" . $today . "%' order by timestamp desc");
	} else {
		$r = mysqli_query($db, "select * from ssb_alert where name='" . $name . "' order by timestamp desc");
	}
	if ($r->num_rows == 0){
		echo 'No SSB alert.<br/>';
	} else {
		$lastprice = 0;
		$firstprice = 0;
		echo '<table>';
		$index = 0;
		if ($r->num_rows > 0) {
			while($row = $r->fetch_assoc()) {
				if ($index == 0){
					$lastprice = $row["price"];
				} else if ($index == ($r->num_rows)-1){
					$firstprice = $row["price"];
				}
				$index++;
				//echo '<br/>';
				echo '<tr>';
				echo '<td>' . $row["timestamp"] . "</td><td>" . $row["period"] . "</td><td>" . $row["name"] . "</td><td>" . $row["type"] . "</td><td>" . $row["price"] . "</td><td>" . $row["ssb"] . "</td>";
				echo '</tr>';
			}
		} else {
			//echo "0 results";
		}
		echo '</table>';
		if ($lastprice>0 && $firstprice>0){
			$delta = ($lastprice-$firstprice);
			if ($delta > 0){
				echo "<font color='GREEN'>delta = " . $delta . '</font><br/>';
			}
			if ($delta < 0){
				echo "<font color='RED'>delta = " . $delta . '</font><br/>';
			}
			if ($delta == 0){
				echo "<font color='BLACK'>delta = " . $delta . '</font><br/>';
			}
		}
	}
}
/*
echo '<br/>';
echo '<br/>';
$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
if ($db->connect_errno) {
echo "Error : " . $db->connect_errno . " <br/>";
exit;
}
$r = mysqli_query($db, "select * from notification");
if ($r->num_rows == 0){
echo 'No notifications.<br/>';
} else {
echo $r->num_rows . " notifications in table<br/>";
$filter = "";
if (isset($_GET['filter'])) {
$filter = $_GET['filter'];
}
if (trim($filter) == ""){
$r = mysqli_query($db, "select * from " . MAIN_TABLE_NAME . " order by timestamp desc");
} else {
$r = mysqli_query($db, "select * from " . MAIN_TABLE_NAME . " where message like '%" . $filter . "%' order by timestamp desc");
}
echo '<br/>';
echo 'List of notifications<br/>';
if ($r->num_rows > 0) {
while($row = $r->fetch_assoc()) {
echo '<br/>';
//echo $row["timestamp"] . ' : ' .  $row["message"] . '<br/>';
echo $row["message"] . '<br/>';
}
} else {
//echo "0 results";
}
$db->close();
}
*/
$db->close();
echo "</body></html>";
?>
