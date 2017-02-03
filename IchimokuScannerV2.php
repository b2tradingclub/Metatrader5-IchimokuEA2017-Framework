<?php
define("MYSQL_SERVER", "localhost");
define("MYSQL_USER", "id517966_ichimoku");
define("MYSQL_PASSWORD", "");
define("MYSQL_DB", "id517966_ichimoku");
define("TBL_PREFIX", "ichimokueav2");
define("CREATE_DB_IF_NOT_EXISTS", true);
define("CREATE_TABLES_IF_NOT_EXIST", true);
define("LOG_IP", true);
define("LOG_IP_IGNORE", "78.201.68.");
define("DISABLE_DETAILED_LOG_VIEW", true);
define("DEBUG", true);
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
    $sql = "CREATE TABLE " . TBL_PREFIX . "_notification (`timestamp` text COLLATE utf8_unicode_ci NOT NULL, `message` text COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $r = mysqli_query($db, $sql);
    $sql = "CREATE TABLE " . TBL_PREFIX . "_ssb_alert (`timestamp` text COLLATE utf8_unicode_ci NOT NULL, `period` text COLLATE utf8_unicode_ci NOT NULL, `name` text COLLATE utf8_unicode_ci NOT NULL, `type` text COLLATE utf8_unicode_ci NOT NULL, `price` double NOT NULL, `ssb` double NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $r = mysqli_query($db, $sql);
    $sql = "CREATE TABLE " . TBL_PREFIX . "_ip_address_log (`id` bigint(20) NOT NULL AUTO_INCREMENT, `access_date_time` datetime NOT NULL, `ip_address` varchar(32) COLLATE latin1_general_ci NOT NULL, `nslookup` text, `url` varchar(255) COLLATE latin1_general_ci DEFAULT NULL, `count` bigint(20), PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
    $r = mysqli_query($db, $sql);
    $sql = "CREATE TABLE " . TBL_PREFIX . "_2jcs_alert (`id` bigint(20) NOT NULL, `timestamp` text COLLATE utf8_unicode_ci NOT NULL, `period` text COLLATE utf8_unicode_ci NOT NULL, `symbol` text COLLATE utf8_unicode_ci NOT NULL, `buy` double NOT NULL,`sell` double NOT NULL, `h1_ls_validated` text COLLATE utf8_unicode_ci NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $r = mysqli_query($db, $sql);    
    $sql = "ALTER TABLE " . TBL_PREFIX . "_2jcs_alert ADD PRIMARY KEY (`id`);";
    $r = mysqli_query($db, $sql);    
    $sql = "ALTER TABLE " . TBL_PREFIX . "_2jcs_alert MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;";
    $r = mysqli_query($db, $sql);        
    $sql= "ALTER TABLE " . TBL_PREFIX . "_2jcs_alert ADD `m1_ls_validated` TEXT NOT NULL  AFTER `h1_ls_validated`";
    $r = mysqli_query($db, $sql);    
    $sql="CREATE TABLE " . TBL_PREFIX . "_history (`id` bigint(20) NOT NULL,`timestamp` text COLLATE utf8_unicode_ci NOT NULL,`symbol` text COLLATE utf8_unicode_ci NOT NULL,`buy` double NOT NULL,`sell` double NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $r = mysqli_query($db, $sql);    
    $sql="ALTER TABLE " . TBL_PREFIX . "_history ADD PRIMARY KEY (`id`);";
    $r = mysqli_query($db, $sql);    
    $sql="ALTER TABLE " . TBL_PREFIX . "_history MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;";
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
    $r = mysqli_query($db, "SELECT * FROM " . TBL_PREFIX . "_ip_address_log where ip_address = '" . $client_ip . "'");
    if ($r->num_rows > 0) {
        if($row = $r->fetch_assoc()) {
            $count = $row["count"];
            $r = mysqli_query($db, "update " . TBL_PREFIX . "_ip_address_log set access_date_time = NOW(), count = " . ($count+1) . ", nslookup='" . $nslookup . "', url='" . $url . "' where ip_address = '" . $client_ip . "'");
        }
    } else {
        $r = mysqli_query($db, "insert into " . TBL_PREFIX . "_ip_address_log(ip_address, access_date_time, nslookup, url, count) values ('" . $client_ip . "',NOW(),'" . $nslookup . "','" . $url . "',1)");
    }
    $r = mysqli_query($db, "DELETE FROM " . TBL_PREFIX . "_ip_address_log where ip_address like '%" . LOG_IP_IGNORE . "%'");
    $db->close();
}
if (isset($_GET['view_logs'])) {
    echo "<html><head><style> table { width:100%; } table, th, td { border: 1px solid gray; border-collapse: collapse; } th, td { padding: 5px; text-align: left; } table#t01 tr:nth-child(even) { background-color: #eee; } table#t01 tr:nth-child(odd) { background-color:#fff; } table#t01 th { background-color: black; color: white; } </style><title>Ichimoku Scanner</title></head><body  style='font-family:arial; color: #ffffff; background-color: #000000'>";
    echo "<img src='../ichimokuscannerlogo.PNG' alt='Ichimoku Scanner Logo'>";
    echo "<h3>Logs</h3><a href='http://ichimoku-expert.blogspot.com'>ichimoku-expert.blogspot.com</a><br/>";
    echo "<br/>";
    $db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
    if ($db->connect_errno) {
        exit;
    }
    $r = mysqli_query($db, "SELECT * FROM " . TBL_PREFIX . "_ip_address_log order by access_date_time desc");
    if ($r->num_rows > 0) {
        echo "<table>";
        while($row = $r->fetch_assoc()) {
            echo "<tr>";
            $access_date_time = $row["access_date_time"];
            $ip_address = $row["ip_address"];
            $nslookup = $row["nslookup"];
            $url = $row["url"];
            $count = $row["count"];
            if (!DISABLE_DETAILED_LOG_VIEW){
                echo "<td>" . $access_date_time . "</td><td>" . $ip_address . "</td><td>" . $nslookup . "</td><td>"  . $url . "</td><td>" . $count . "</td>";
            } else {
                echo "<td>" . $access_date_time . "</td><td> DISABLED </td><td> DISABLED </td><td>"  . $url . "</td><td>" . $count . "</td>";
            }
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
    $r = mysqli_query($db, "delete from " . TBL_PREFIX . "_ssb_alert");
    $db->close();
    exit;
}
if (isset($_GET['reset_ssb_alerts'])) {
    $db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
    if ($db->connect_errno) {
        exit;
    }
    $r = mysqli_query($db, "delete from " . TBL_PREFIX . "_ssb_alert");
    $db->close();
    exit;
}
echo "<html><head><style> table, th, td { border: 1px solid gray; border-collapse: collapse; } th, td { padding: 5px; text-align: left; } table#t01 tr:nth-child(even) { background-color: #eee; } table#t01 tr:nth-child(odd) { background-color:#fff; } table#t01 th { background-color: black; color: white; } </style><title>Ichimoku Scanner</title></head><body  style='font-family:arial; color: #ffffff; background-color: #000000'>";
echo "<img src='../ichimokuscannerlogo.PNG' alt='Ichimoku Scanner Logo'>";
echo "<h3>Volatility & Trend Scanner</h3><a href='http://ichimoku-expert.blogspot.com'>ichimoku-expert.blogspot.com</a><br/>";
echo "<center>";
echo "<h3>This scanner works with Japanese Candlesticks and Ichimoku : <a href='http://ichimoku-ea.000webhostapp.com/ichimoku-ea-v2/'>Ichimoku EA V2</a></h3>";
echo "<h3><b>First step is to have textual alerts that my EA detects.</b></h3>";
echo "<h3><a href='mailto:trader77330@netcourrier.com'>Click here to send me an e-mail</a></h3>";
echo "</center>";

if (isset($_GET['upload_history'])) {
    $history = $_GET['upload_history'];
    echo "received=  [[$history]]<br/>";
    $array = explode(";", $history);
    if (count($array)==4){
        $timestamp = $array[0];
        $symbol = $array[1];
        $buy = $array[2];
        $sell = $array[3];
        $db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if ($db->connect_errno) {
            echo "Error : " . $db->connect_errno . " <br/>";
            exit;
        }
        //$timestamp = (new DateTime())->format('Y-m-d H:i:s');
        $r = mysqli_query($db, "insert into " . TBL_PREFIX . "_history(timestamp, symbol, buy, sell) values ('" . $timestamp . "', '" . $symbol . "', '" . $buy . "', '" . $sell . "')");
        if (DEBUG) echo 'History recorded OK.<br/>';
        $db->close();
        exit;
    }
}

if (isset($_GET['upload_2jcs_alert'])) {
    $jcsalert = $_GET['upload_2jcs_alert'];
    echo "received=  [[$jcsalert]]<br/>";
    $array = explode(";", $jcsalert);
    if (count($array)==6){
        $timestamp = $array[0];
        $period = $array[1];
        $symbol = $array[2];
        $buy = $array[3];
        $sell = $array[4];
        $h1_ls_validated = $array[5];
        $db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if ($db->connect_errno) {
            echo "Error : " . $db->connect_errno . " <br/>";
            exit;
        }
        $r = mysqli_query($db, "select * from " . TBL_PREFIX . "_2jcs_alert");
        if ($r->num_rows == 0){
            echo 'No 2JCS alert.<br/>';
        } else {
            echo $r->num_rows . " 2JCS alerts in table<br/>";
        }
        //$timestamp = (new DateTime())->format('Y-m-d H:i:s');
        $r = mysqli_query($db, "insert into " . TBL_PREFIX . "_2jcs_alert (timestamp, period, symbol, buy, sell, h1_ls_validated) values ('" . $timestamp . "', '" . $period . "', '" . $symbol . "', '" . $buy . "', '" . $sell . "', '" . $h1_ls_validated . "')");
        if (DEBUG) echo '2JCS alert recorded OK.<br/>';
        $db->close();
        exit;
    }
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
        $r = mysqli_query($db, "select * from " . TBL_PREFIX . "_notification");
        if ($r->num_rows == 0){
            echo 'No notifications.<br/>';
        } else {
            echo $r->num_rows . " notifications in table<br/>";
        }
        //$timestamp = (new DateTime())->format('Y-m-d H:i:s');
        $r = mysqli_query($db, "insert into " . TBL_PREFIX . "_ssb_alert (timestamp, period, name, type, price, ssb) values ('" . $timestamp . "', '" . $period . "', '" . $name . "', '" . $type . "', " . $price . ", " . $ssb . ")");
        if (DEBUG) echo 'SSB alert recorded OK.<br/>';
        $db->close();
        exit;
    }
}
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

if (isset($_GET['view_rates'])){
    //experimental
    echo "<br/>";
    $page = file_get_contents("https://rates.fxcm.com/RatesXML");
    $xml = new SimpleXMLElement($page);
    $result = $xml->xpath('/Rates/Rate');
    //echo 'result count = ' . count($result);
    echo "<table>";
    for($i=0;$i<count($result);$i++){
        $symbol = (string) $result[$i]->xpath('@Symbol')[0];
        $bid = (string) $result[$i]->xpath('Bid')[0]; // sell price
        $ask = (string) $result[$i]->xpath('Ask')[0]; // buy price
        echo "<tr>";
        echo "<td>" . $symbol . '</td><td>' . $bid . '</td><td>' . $ask . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    //end experimental
}

echo "<br/>";
echo "<h3>Summary of alerts in dabatase :</h3>";

$count = 0;

$db = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
if ($db->connect_errno) {
    echo "Error : " . $db->connect_errno . " <br/>";
    exit;
}

$r = mysqli_query($db, "select * from " . TBL_PREFIX . "_2jcs_alert order by timestamp desc");
echo "<table>";
echo "<tr><th>Timestamp</th><th>Period</th><th>Symbol</th><th>Buy</th><th>Sell</th><th>H1 LS Validation</th></tr>";
while($row = $r->fetch_assoc()) {
    $timestamp = $row["timestamp"];
    $period = $row["period"];
    $symbol = $row["symbol"];
    $buy = $row["buy"];
    $sell = $row["sell"];
    $h1_ls_validated = $row["h1_ls_validated"];
    //echo $timestamp . " " . $period . " " . $symbol . " " . $buy . " " . $sell . " " . $h1_ls_validated . "<br/>";
    echo "<tr>";
    echo "<td>" . $timestamp . "</td><td>" . $period . "</td><td>" . $symbol . "</td><td>" . $buy . "</td><td>" . $sell . "</td><td>" . $h1_ls_validated . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br/>";
echo "<h3>Summary of database recording :</h3>";

$count = 0;

$r = mysqli_query($db, "select count(*) from " . TBL_PREFIX . "_history");
while($row = $r->fetch_assoc()) {
    if ($index == 0){
        $count = $row["count(*)"];
    }
}

echo "count = " . $count;
echo "<br/>";



$db->close();
echo "</body></html>";
?>
