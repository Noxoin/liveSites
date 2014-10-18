<?php
    $requestType = $_SERVER['REQUEST_METHOD'];

    include "../../config/config.php";

    if (isset($_SERVER['HTTP_ORIGIN'])) {
        if ( strpos($_SERVER['HTTP_ORIGIN'], $allowedDomains[0]) > -1 ) {
            header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
            header("Access-Control-Allow-Methods: POST");
            header("Access-Control-Allow-Headers: charset, content-type");
        } elseif ( strpos($_SERVER['HTTP_ORIGIN'], $allowedDomains[1]) > -1 ) {
            header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
            header("Access-Control-Allow-Methods: GET, POST, PUT");
            header("Access-Control-Allow-Headers: charset, content-type");
        }
    }

    if ($requestType == "POST") {

        $song = $_POST['song'];
        $artist = $_POST["artist"];
        $youtubeURL = $_POST["youtubeURL"];
        $user = $_POST["user"];

        $dbh = mysql_connect($dbHOSTNAME, $dbUSER, $dbPASSWORD, $dbDATABASE) OR DIE ("Unable to connect to database! Please try again later.");
        mysql_select_db($dbDATABASE);
        mysql_set_charset('utf8',$dbh);

        $sql = "SELECT song,artist,youtubeURL,timestamp FROM $dbTable WHERE song='".mysql_real_escape_string($song)."' AND artist='".mysql_real_escape_string($artist)."'";
        echo $sql."\n";
        
        $result = mysql_query($sql);

        $rows = mysql_num_rows($result);

        if (!$rows){

            $sql = "INSERT INTO $dbTable (song, artist, youtubeURL, timestamp, user)
                    VALUES('".mysql_real_escape_string($song)."', '".mysql_real_escape_string($artist)."', '$youtubeURL', NOW(), '$user')";

            echo $sql;

            if (!mysql_query($sql)) {
                http_response_code(500);
                die ('Error: '.mysql_error());
            } else {
                http_response_code(201);
            }

        }
        mysql_close($dbh);


    } else if ($requestType == "GET") {
        
        header("Content-Type: application/json;charset=utf-8");
        if ($migrate == 1) {
            header( 'Location: http://noxoin.com/music/chinese/');
            return;
        }

        $page = isset($_GET['page'])?$_GET['page']:1;
        $startPlay = isset($_GET['startPlay'])?$_GET['startPlay']:0;
        $loop = isset($_GET['loop'])?$_GET['loop']:0;
        $user = isset($_GET['me'])?$_GET['me']:"";
        $q = isset($_GET['q'])?$_GET['q']:"";
        $star = isset($_GET['star']) && $_GET['star']=="on"?1:0;

        $dbh = mysql_connect($dbHOSTNAME, $dbUSER, $dbPASSWORD, $dbDATABASE) OR DIE ("Unable to connect to database! Please try again later.");
        mysql_select_db($dbDATABASE);
        mysql_set_charset('utf8',$dbh);

        $sql = "SELECT song,artist,youtubeURL,timestamp,downloaded,bad,star,cd FROM $dbTable ".($q != "" || $star==1 ? "WHERE ".($q != "" ? "song like '%".mysql_real_escape_string($q)."%' OR artist like '%".mysql_real_escape_string($q)."%' ".($star==1?"AND ":""): "").($star==1 ? "star='1'":"") : "")." ORDER BY ".($user==$authority?"downloaded ASC, star DESC,":"")."timestamp DESC";
        
        $result = mysql_query($sql);
        if (!$result) {
            http_response_code(500);
            echo "Error: ".mysql_error();
        } else {

            //Skips the entries
            for ($i = 0; $i < $page - 1; $i++) {
                for ( $j = 0; $j < 20; $j++) {
                    $entry = mysql_fetch_array($result);
                }
            }
            $i = 0;
            $dictionary = array();
            while ($entry = mysql_fetch_array($result)) {
                if($i >= 20) { break;}
                $youtubeID = substr($entry['youtubeURL'], 32);
                $songInfo = array(
                                    "song" => $entry['song'],
                                    "artist" => $entry['artist'],
                                    "youtubeURL" => $entry['youtubeURL'],
                                    "timeInserted" => $entry['timestamp'],
                                    "star" => $entry['star']
                                );
                if ($user == $authority) {
                    $songInfo["status"] = $entry['bad'] == '1' ? "Bad": ($entry['downloaded'] == '1' ? "Downloaded" : "");
                    $songInfo["cd"] = $entry['cd'];
                }
                $dictionary[$i] = $songInfo;
                $i++;
            }

            echo json_encode($dictionary);
        }
    } else if ($requestType == "PUT") {

        parse_str(file_get_contents("php://input"),$_PUT);
    
        $song = $_PUT['song'];
        $artist = $_PUT["artist"];
        $user = $_PUT["user"];
        $downloaded = $_PUT["downloaded"];
        $bad = $_PUT["bad"];
        $star = $_PUT["star"];
        $cd = $_PUT["cd"];

        $dbh = mysql_connect($dbHOSTNAME, $dbUSER, $dbPASSWORD, $dbDATABASE) OR DIE ("Unable to connect to database! Please try again later.");
        mysql_select_db($dbDATABASE);
        mysql_set_charset('utf8',$dbh);

        $sql = "UPDATE $dbTable SET downloaded=$downloaded, bad=$bad, star=$star, cd=".($cd=="0"?"NULL":$cd)." WHERE song='".mysql_real_escape_string($song)."' AND artist='".mysql_real_escape_string($artist)."'";

        if (!mysql_query($sql)) {
            http_response_code(500);
            die ('Error: '.mysql_error());
        }
        
    }
?>
