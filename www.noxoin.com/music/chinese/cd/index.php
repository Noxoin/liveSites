<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf8"/>
        <title>Chinese Music List CDs</title>
        <meta name="robots" content="noindex,nofollow">
        <link rel="icon" type="image/ico" href="http://api.noxoin.com/favicon.ico">
        <link rel="stylesheet" type"text/css" href="../../css/table.css">
        <style>body{max-width:1440px;margin-left:auto;margin-right:auto;padding-left:20px;padding-right:20px}table{width:100%}a{color:black}</style>
    </head>
    <body>
<?php

        include "../../../config/constants.php";

        // Set Up DataBase Connections
        include "../../../config/config.php";
        $dbh = mysql_connect($dbHOSTNAME, $dbUSER, $dbPASSWORD, $dbDATABASE) OR DIE ("Unable to connect to database! Please try again later.");
        mysql_select_db($dbDATABASE);
        mysql_set_charset('utf8',$dbh);

        // Get Parameters
        $page = isset($_GET['page'])?$_GET['page']:1;
        $startPlay = isset($_GET['startPlay'])?$_GET['startPlay']:0;
        $loop = isset($_GET['loop'])?$_GET['loop']:0;
        $user = isset($_GET['me'])?$_GET['me']:"";
        $q = isset($_GET['q'])?$_GET['q']:"";
        $star = isset($_GET['star']) && $_GET['star']=="on"?1:0;

        if (!isset($_GET['cd']) || intval($_GET['cd']) < 1) {
            header( 'Location: http://noxoin.com/music/chinese/'.($authority == $user?"?me=$user":"") );
            return;
        }


        $cd = intval($_GET['cd']);

        if($user == $authority) {
            echo '<button type="button" style="float:right" onclick="toggleInsertion();">+</button>';
        }


?>
        <img src="/images/logo_small.jpg" style="height:65px;float:left;"/>
        <h1 style="padding-top:20px;padding-left:75px;font-size:32pt">Noxoin Chinese Music Record</h1>
        <div style="clear:both"></div>

        <form method="get" action="">

<?php
        $sql = "SELECT MAX(cd) AS max FROM $dbTable";
        $results = mysql_fetch_array(mysql_query($sql));
        $maxCD = $results["max"];
        if ($maxCD == "" ) {
            $maxCD == 0;
        }

        $sql = "SELECT song,artist,youtubeURL,timestamp,downloaded,bad,star,cd FROM $dbTable WHERE cd='$cd' ".($q != "" || $star==1 ? "AND ".($q != "" ? "song like '%".mysql_real_escape_string($q)."%' OR artist like '%".mysql_real_escape_string($q)."%' ".($star==1?"AND ":""): "").($star==1 ? "star='1'":"") : "")." ORDER BY ".($user==$authority?"downloaded ASC, star DESC,":"")."timestamp DESC";


        $result = mysql_query($sql);
        if (!$result) {
            http_response_code(500);
            die("Error: ".mysql_error());
        }
        $entriesCount = mysql_num_rows($result);

        if($user == $authority) {
            echo '<input type="hidden" name="me" value="'.$authority.'">';
        }

?>
        <div style="float:right">Page: <select name="page" onchange="this.form.submit()">
<?php
        for($i = 0; $i < ($entriesCount - 1 )/20; $i++ ) {
            echo '<option value="'.($i+1).'" '.(($i+1==$page)?'selected':'').'>'.($i+1).'</option>';
        }
        echo '</select> of '.$i;
?>
        </div>
<?php
        echo '<div><p>Search: <input type="text" name="q" onkeyup="if(event.keyCode == 13) { this.form.submit();}" onmouseup="this.select();" value="'.$q.'"> Star Only: <input type="checkbox" name="star" onchange="this.form.submit()" '.($star==1?'checked':'').'></div>';
?>
        </form>

<?php include "../../lib/construct_table.php"; ?>
<?php include "../../lib/scripts.php"; ?>

    </body>
</html>
