<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf8"/>
        <title>Chinese Music List</title>
        <meta name="robots" content="noindex,nofollow">
        <link rel="icon" type="image/ico" href="http://api.noxoin.com/favicon.ico">
        <link rel="stylesheet" type"text/css" href="../css/table.css">
        <style>
            body{
                max-width:1000px;
                background-image:url('/music/images/ticks.png');
                margin-left:auto;
                margin-right:auto;
                padding-bottom:30px;
                padding-left:20px;
                padding-right:20px;
            }
            table{
                width:100%;
            }
            a{
                color:black;
            }
        </style>
        <?php include "../lib/script_head.php"?>
    </head>
    <body onkeyup="keyPress(event.keyCode);">
<?php

        include "../../config/constants.php";

        // Set Up DataBase Connections
        include "../../config/config.php";
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

        // Saved in Cookie
        $entryPerPage = isset($_COOKIE['entryPerPage'])?$_COOKIE['entryPerPage']:20;

        if($user == $authority) {
            echo '<button type="button" style="float:right" onclick="toggleInsertion();">+</button>';
        }
?>

        <button type="button" style="float:right" onclick="toggleRepeat(this)" style="padding-top:10px">Repeat All</button>

        <img src="/images/logo_small2.png" style="height:65px;float:left;padding-top:10px;"/>
        <h1 style="font-family:Arial;padding-top:20px;padding-left:75px;font-size:32pt;margin-bottom:5px">Noxoin Chinese Music Record</h1>
        <div style="clear:both"></div>

        <form method="get" action="">

<?php
            $sql = "SELECT MAX(cd) AS max FROM $dbTable";
            $results = mysql_fetch_array(mysql_query($sql));
            $maxCD = $results["max"];
            if ($maxCD == "" ) {
                $maxCD == 0;
            }

            $sql = "SELECT id,song,artist,youtubeURL,timestamp,downloaded,bad,star,cd FROM $dbTable "
                    ."WHERE song like '%".mysql_real_escape_string($q)."%'"
                        ." OR artist like '%".mysql_real_escape_string($q)."%'";
            if($star == 1 && $user == $authority) {
                $sql = " AND star='1'";
            }
            $sql .= " ORDER BY ".($user==$authority?"downloaded ASC, star DESC,":"")."timestamp DESC";

/*
            $sql = "SELECT id,song,artist,youtubeURL,timestamp,downloaded,bad,star,cd FROM $dbTable "
                    .($q != "" || $star==1 ? 
                        "WHERE ".($q != "" ? 
                            "song like '%".mysql_real_escape_string($q)."%' OR artist like '%".mysql_real_escape_string($q)."%' "
                            .($star==1?"AND ":"")
                            : ""
                        )
                        .($star==1 ? "star='1'":"") 
                        : ""
                    )." ORDER BY ".($user==$authority?"downloaded ASC, star DESC,":"")."timestamp DESC";
*/

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
            <div style="float:right">
                <button type="button" onclick="enqueueAll()" style="margin-right:20px">Play All +</button>
                Page: <select name="page" onchange="this.form.submit()">
<?php
            for($i = 0; $i < ($entriesCount - 1 )/$entryPerPage; $i++ ) {
                echo '<option value="'.($i+1).'" '.(($i+1==$page)?'selected':'').'>'.($i+1).'</option>';
            }
            $maxPage = $i;
            echo '</select> of '.$i;
?>
            </div>
            <div>
                <p>Search: <input type="text" name="q" onkeyup="if(event.keyCode == 13) { this.form.submit();}" onmouseup="this.select();" value="<?php echo $q;?>"> 
                Star Only: <input type="checkbox" name="star" onchange="this.form.submit()" <?php echo ($star==1?'checked':'');?>>
                <select style="margin-left: 30px" onchange="entitiesPerPage(this)">
                    <option value="20" <?php if($entryPerPage==20){echo 'selected';}?>>20</option>
                    <option value="30" <?php if($entryPerPage==30){echo 'selected';}?>>30</option>
                    <option value="40" <?php if($entryPerPage==40){echo 'selected';}?>>40</option>
                    <option value="50" <?php if($entryPerPage==50){echo 'selected';}?>>50</option>
                </select>
                entries per page.
                </p>
            </div>
        </form>

<?php include "../lib/construct_table.php"; ?>
        <div style="padding-top:10px;padding-bottom:20px">
            <div style="float:right;">
<?php
        $url = "/music/chinese/?page=";
        if ($page != $maxPage) {
            echo '<a href="'.$url.($page+1).'">Next Page &gt;&gt;</a> ';
        }
?>
            </div>
            <div>
<?php
        if ($page != 1) {
            echo '<a href="'.$url.($page-1).'">&lt;&lt; Previous Page</a> ';
        }
?>
            </div>
        </div>
            
<?php include "../lib/scripts.php"; ?>

    </body>
</html>
