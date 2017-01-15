<?php
define("MYSQL_SERVER", "localhost");
define("MYSQL_USER", "id517966_ichimoku");
define("MYSQL_PASSWORD", "");
define("MYSQL_DB", "id517966_ichimoku");
define("MAIN_TABLE_NAME","notification");
define("CREATE_DB_IF_NOT_EXISTS", true);
define("CREATE_TABLES_IF_NOT_EXIST", true);
define("LOG_IP", true);
define("LOG_IP_IGNORE", "78.201.68.");

// si le paramètre CREATE_DB_IF_NOT_EXISTS est défini à true alors tenter de créer la base de données dans paramètre MYSQL_DB
if (CREATE_DB_IF_NOT_EXISTS == true){
	// CREATE DB IF NOT EXISTS
	$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD);
	if ($db->connect_errno) {
	    exit;
	}
	$r = mysqli_query($db, "create database if not exists " . MYSQL_DB);
	$db->close();
}

// si le paramètre existe alors tenter de créer les tables annuaire et ip_address_log
if (CREATE_TABLES_IF_NOT_EXIST == true){
	// CREATE TABLE IF NOT EXISTS
	$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
	if ($db->connect_errno) {
	    exit;
	}
	$sql = "CREATE TABLE `notification` (`timestamp` text COLLATE utf8_unicode_ci NOT NULL, `message` text COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	$r = mysqli_query($db, $sql);
	
	$sql = "CREATE TABLE `ssb_alert` (`timestamp` text COLLATE utf8_unicode_ci NOT NULL, `period` text COLLATE utf8_unicode_ci NOT NULL, `name` text COLLATE utf8_unicode_ci NOT NULL, `type` text COLLATE utf8_unicode_ci NOT NULL, `price` double NOT NULL, `ssb` double NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	$r = mysqli_query($db, $sql);

		$sql = "CREATE TABLE `ip_address_log` (`id` bigint(20) NOT NULL AUTO_INCREMENT, `access_date_time` datetime NOT NULL, `ip_address` varchar(32) COLLATE latin1_general_ci NOT NULL, `nslookup` text, `url` varchar(255) COLLATE latin1_general_ci DEFAULT NULL, `count` bigint(20), PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
	$r = mysqli_query($db, $sql);
	
	$db->close();
}


// LOG IP si paramètre LOG_IP = true
if (LOG_IP==true){
	$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
	if ($db->connect_errno) {
	    exit;
	}
	$client_ip = $_SERVER['REMOTE_ADDR'];
	$nslookup = gethostbyaddr($client_ip);
	$url = $_SERVER['PHP_SELF'];
	
	$r = mysqli_query($db, "SELECT * FROM `ip_address_log` where ip_address = '" . $client_ip . "'");
	if ($r->num_rows > 0) {
		if($row = $r->fetch_assoc()) {
			$count = $row["count"];
			$r = mysqli_query($db, "update `ip_address_log` set access_date_time = NOW(), count = " . ($count+1) . ", nslookup='" . $nslookup . "', url='" . $url . "' where ip_address = '" . $client_ip . "'");
		}
	} else {
		$r = mysqli_query($db, "insert into ip_address_log(ip_address, access_date_time, nslookup, url, count) values ('" . $client_ip . "',NOW(),'" . $nslookup . "','" . $url . "',1)");
	}
	
	$r = mysqli_query($db, "DELETE FROM `ip_address_log` where ip_address like '%" . LOG_IP_IGNORE . "%'");
			
	$db->close();
}

if (isset($_GET['view_logs'])) {		
	echo "<html><head><style>table, th, td { border: 1px solid black; border-collapse: collapse; } ";
	echo "th, td { padding: 5px; text-align: left; } </style><title></title></head><body>";
	echo "<h2>Ichimoku Scanner</h2>";
	echo "<h3>Experimental version</h3><a href='http://traderetgagner.blogspot.com'>traderetgagner.blogspot.com</a><br/>";
	echo "<br/>";
	
	$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
	if ($db->connect_errno) {
	    exit;
	}
		
	$r = mysqli_query($db, "SELECT * FROM `ip_address_log` order by access_date_time desc");
	if ($r->num_rows > 0) {
		echo "<table>";
		while($row = $r->fetch_assoc()) {
			echo "<tr>";
			$access_date_time = $row["access_date_time"];
			$ip_address = $row["ip_address"];
			$nslookup = $row["nslookup"];
			$url = $row["url"];
			$count = $row["count"];
			echo "<td>" . $access_date_time . "</td><td>" . $ip_address . "</td><td>" . $nslookup . "</td><td>"  . $url . "</td><td>" . $count . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
		
	$db->close();
	
	echo "</body></html>";

	exit;
}

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
echo "<h3>Experimental version</h3><a href='http://traderetgagner.blogspot.com'>traderetgagner.blogspot.com</a><br/>";
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
