<?php
define("MYSQL_SERVER", "localhost");
define("MYSQL_USER", "id517966_ichimoku");
define("MYSQL_PASSWORD", "");
define("MYSQL_DB", "id517966_ichimoku");
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
    echo "<html><head><style> table { width:100%; } table, th, td { border: 1px solid black; border-collapse: collapse; } th, td { padding: 5px; text-align: left; } table#t01 tr:nth-child(even) { background-color: #eee; } table#t01 tr:nth-child(odd) { background-color:#fff; } table#t01 th { background-color: black; color: white; } </style><title>Ichimoku Scanner</title></head><body  style='font-family:arial; color: #ffffff; background-color: #000000'>";
    echo "<img src='ichimokuscannerlogo.PNG' alt='Ichimoku Scanner Logo'>";
    echo "<h3>Logs</h3><a href='http://traderetgagner.blogspot.com'>traderetgagner.blogspot.com</a><br/>";
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
    $r = mysqli_query($db, "delete from ssb_alert");
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
echo "<html><head><style> table, th, td { border: 1px solid gray; border-collapse: collapse; } th, td { padding: 5px; text-align: left; } table#t01 tr:nth-child(even) { background-color: #eee; } table#t01 tr:nth-child(odd) { background-color:#fff; } table#t01 th { background-color: black; color: white; } </style><title>Ichimoku Scanner</title></head><body  style='font-family:arial; color: #ffffff; background-color: #000000'>";
echo "<img src='ichimokuscannerlogo.PNG' alt='Ichimoku Scanner Logo'>";
echo "<h3>Volatility & Trend Scanner</h3><a href='http://traderetgagner.blogspot.com'>traderetgagner.blogspot.com</a><br/>";
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

//Supprimer les "doublons" inutiles
$r = mysqli_query($db, "DELETE t1 FROM ssb_alert AS t1, ssb_alert AS t2 WHERE t1.timestamp < t2.timestamp AND t1.name = t2.name AND t1.period = t2.period AND t1.type = t2.type and t1.price = t2.price and t1.ssb = t2.ssb");

$today = ((new DateTime)->format("Y-m-d"));
$showOnlyToday = false;
if (isset($_GET['today'])) {
    $showOnlyToday = true;
}
$showOnlyResults = false;
if (isset($_GET['show_only_results'])) {
    $showOnlyResults = true;
}

if (isset($_GET['filter'])) {
    $filter = trim($_GET['filter']);
}

echo "<br/>";
echo "<h3>Summary of results :</32>";
echo '<h4>Name/Delta from first detection/Last detection timestamp<h4>';
foreach($arrayname as $name){
    $r = mysqli_query($db, "select * from ssb_alert where name='" . $name . "' and name like '%" . $filter . "%'    order by timestamp desc");
    $index = 0;
    $lastprice = 0;
    $firstprice = 0;
    $lastdetection = "";
    while($row = $r->fetch_assoc()) {
        if ($index == 0){
            $lastprice = $row["price"];
            $lastdetection = explode(".", $row["timestamp"])[0];
        } else if ($index == ($r->num_rows)-1){
            $firstprice = $row["price"];
        }
        $index++;    
    }
    echo "<table width='50%'>";
    if ($lastprice != 0 && $firstprice != 0){
        $delta = $lastprice - $firstprice;
        if ($delta < 0) $color = 'RED';
        else if ($delta == 0) $color = 'GRAY';
        else if ($delta > 0) $color = 'GREEN';
            $delta = number_format($delta, 6);
        echo "<tr>";
        echo "<td width='20%'>" . $name . "</td><td width='30%'><font color='" . $color . "'>" . $delta . "</font></td><td width='40%'>" . $lastdetection . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo '<br/>';
if ($showOnlyToday){
    echo '<h3>List of today SSB alerts (' . $today . ')</h3>';
} else {
    echo '<h3>List of all SSB alerts</h3>';
}

echo '<h4>Timestamp/Period/Name/Type of price detected/Price/SSB</h4>';
foreach($arrayname as $name){
    //echo '<br/>';
    //echo $name . "<br/>";
    if ($showOnlyToday){
        $r = mysqli_query($db, "select * from ssb_alert where name='" . $name . "' and timestamp like '" . $today . "%' order by timestamp desc");
    } else {
        if ($filter != ""){
            $r = mysqli_query($db, "select * from ssb_alert where name='" . $name . "' and name like '%" . $filter . "%'    order by timestamp desc");
        } else {
            $r = mysqli_query($db, "select * from ssb_alert where name='" . $name . "' order by timestamp desc");        }
    }
    if ($r->num_rows == 0){
        //echo 'No SSB alert.<br/>';
    } else {
        echo $name . "<br/>";
        $lastprice = 0;
        $firstprice = 0;
        if (!$showOnlyResults) echo "<table>";
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
                if (!$showOnlyResults) echo '<tr>';
                if (!$showOnlyResults) echo '<td>' . explode(".", $row["timestamp"])[0] . "</td><td>" . explode("_", $row["period"])[1] . "</td><td>" . $row["name"] . "</td><td>" . $row["type"] . "</td><td>" . $row["price"] . "</td><td>" . $row["ssb"] . "</td>";
                if (!$showOnlyResults) echo '</tr>';
            }
        } else {
            //echo "0 results";
        }
        if (!$showOnlyResults) echo '</table>';
        if ($lastprice>0 && $firstprice>0){
            $delta = ($lastprice-$firstprice);
            $delta = number_format($delta, 6);
            if ($delta > 0){
                echo "<font color='GREEN'>delta = " . $delta . '</font><br/>';
            }
            if ($delta < 0){
                echo "<font color='RED'>delta = " . $delta . '</font><br/>';
            }
            if ($delta == 0){
                echo "<font color='BLUE'>delta = " . $delta . '</font><br/>';
            }
        }
        echo "<br/>";
    }
}
$db->close();
echo "</body></html>";
?>
