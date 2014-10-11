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
    header("Content-Type: text/html;charset=utf-8");

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

        echo "<html><head><meta http-equiv=\"content-type\" content=\"text/html;charset=utf8\"/><title>Chinese Music List</title><meta name=\"robots\" content=\"noindex,nofollow\">
                <link rel=\"icon\" type=\"image/ico\" href=\"http://api.noxoin.com/favicon.ico\">
                <style>table{width:100%}a{color:black}</style></head><body>";

        $dbh = mysql_connect($dbHOSTNAME, $dbUSER, $dbPASSWORD, $dbDATABASE) OR DIE ("Unable to connect to database! Please try again later.");
        mysql_select_db($dbDATABASE);
        mysql_set_charset('utf8',$dbh);


        $sql = "SELECT song,artist,youtubeURL,timestamp,downloaded,bad,star FROM $dbTable ".($q != "" || $star==1 ? "WHERE ".($q != "" ? "song like '%".mysql_real_escape_string($q)."%' OR artist like '%".mysql_real_escape_string($q)."%' ".($star==1?"AND ":""): "").($star==1 ? "star='1'":"") : "")." ORDER BY ".($user==$authority?"downloaded ASC, star DESC,":"")."timestamp DESC";
        
        $result = mysql_query($sql);
        if (!$result) {
            http_response_code(500);
            echo "Error: ".mysql_error();
        } else {
            $entriesCount = mysql_num_rows($result);
            if($user == $authority) {
                echo '<button type="button" style="float:right" onclick="toggleInsertion();">+</button>';
            }
            echo '<h1>Noxoin Chinese Music Record</h1>';
            echo '<form method="get" action="">';
            if($user == $authority) {
                echo '<input type="hidden" name="me" value="'.$authority.'">';
            }
            echo '<div style="float:right">Page: <select name="page" onchange="this.form.submit()">';
            for($i = 0; $i < ($entriesCount - 1 )/20; $i++ ) {
                echo '<option value="'.($i+1).'" '.(($i+1==$page)?'selected':'').'>'.($i+1).'</option>';
            }
            echo '</select> of '.$i."</div>";
            echo '<div><p>Search: <input type="text" name="q" onkeyup="if(event.keyCode == 13) { this.form.submit();}" onmouseup="this.select();" value="'.$q.'"> Star Only: <input type="checkbox" name="star" onchange="this.form.submit()" '.($star==1?'checked':'').'></div>';
            echo '</form>';
            echo '<table border="1"><tr><th width="15%">Song</th><th width="15%">Artist</th><th>youtubeURL</th><th width="150px;">Time Inserted</th><th width="100px">Status</th><th width="25px">Star</th></tr>';
            if ($user==$authority ) {
                echo '<tr id="insertion" hidden><td width="15%"><input type="text" width="100%"></td><td width="15%"><input type="text" width="100%"></td><td><input type="text" width="100%" onkeyup="if(event.keyCode == 13) { insert(); }"></td><td></td><td></td><td></td></tr>';
            }

            //Skips the entries
            for ($i = 0; $i < $page - 1; $i++) {
                for ( $j = 0; $j < 20; $j++) {
                    $entry = mysql_fetch_array($result);
                }
            }
            $i = 0;
            while ($entry = mysql_fetch_array($result)) {
                if($i >= 20) { break;}
                $youtubeID = substr($entry['youtubeURL'], 32);
                echo '<tr id="'.$i.'" '.(($entry['bad']=="1")?'style="color:red"':(($entry['downloaded']=="1")?'style="color:blue"':'')).' >'
                    .'<td '.($user==$authority ? 'onclick="copy(this)"':'').'>'.$entry['song'].'</td>'
                    .'<td '.($user==$authority ? 'onclick="copy(this)"':'').'>'.$entry['artist'].'</td>'
                    //.'<td '.($user==$authority ? 'onclick="copy(this.children[1])"':'').'>'
                    .'<td>'
                        .'<embed id="audio_'.$i.'" style="float:right" src="http'.(stripos($_SERVER['SERVER_PROTOCOL'],'https')===true?'s':'').'://www.youtube.com/v/'.$youtubeID.'?hd=1&amp;version=2&amp;theme=dark&amp;border=0&amp;autoplay='.(($i==0&&$startPlay=="1")?'1':'0').'&amp;loop='.($loop==1?'1':'0').'&amp;enablejsapi=1" type="application/x-shockwave-flash" allowscriptaccess="always" width="30" height="25" >'
                        .'</embed>'
                        .($user==$authority ? 
                            '<a href="http://www.video2mp3.net/loading.php?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D'.$youtubeID.'" style="text-decoration:none;color:inherit">'.$entry['youtubeURL'].'</a>' :
                            '<a href="'.$entry['youtubeURL'].'" style="text-decoration:none;color:inherit">'.$entry['youtubeURL'].'</a>')
                    .'</td>'
                    .'<td>'.$entry['timestamp'].'</td>'
                    .'<td><select onchange="updateSong(this)" '.($user == $authority ? '' : 'disabled').'><option value=""></option><option value="downloaded" '.(($entry['bad']=="0" && $entry['downloaded']=="1")?'selected':'').'>Downloaded</option><option value="bad" '.(($entry['bad']=="1")?'selected':'').'>Bad</option></select></td>'
                    .'<td><input type="checkbox" onchange="updateSong(this)" '.($user == $authority ? '' : 'disabled').' '.($entry['star']=="1"?'checked':'').'></td>'
                    .'</tr>';
                    $i++;
            }
            echo '</table>';
        }
            echo '<script>';
        if($user == $authority) {
            echo 'function updateSong(element){
                        var url = document.URL;
                        var request = new XMLHttpRequest();
                        request.open("PUT", url, true);
                        request.setRequestHeader("content-type", "application/x-www-form-urlencoded;charset=utf8");
                        var tr = element.parentNode.parentNode;
                        var cells = tr.getElementsByTagName("td");

                        tr.style.color = (cells[4].firstElementChild.value=="bad"? "red" : (cells[4].firstElementChild.value == "downloaded" ? "blue" : "black"));
                        
                        var data = "song="+cells[0].innerHTML
                                +  "&artist="+cells[1].innerHTML
                                +  "&downloaded="+(cells[4].firstElementChild.value=="downloaded"?1:0)
                                +  "&bad="+(cells[4].firstElementChild.value=="bad"?1:0)
                                +  "&star="+(cells[5].firstElementChild.checked?1:0)
                                +  "&user=noxoin";

                        console.log(data);
                        request.send(data);
                    }
                    function toggleInsertion() {
                        var insertElem = document.getElementById("insertion");
                        if (insertElem.hidden == true) {
                            document.getElementById("insertion").hidden = false;
                        } else { 
                            document.getElementById("insertion").hidden = true;
                        }
                    }
                    function insert() {
                        var url = document.URL;
                        values = document.getElementById("insertion").getElementsByTagName("input");

                        var request = new XMLHttpRequest();
                        request.open("POST", url, true);
                        request.setRequestHeader("content-type", "application/x-www-form-urlencoded;charset=utf8");
                        
                        var data = "song="+values[0].value
                                +  "&artist="+values[1].value
                                +  "&youtubeURL="+values[2].value
                                +  "&user=noxoin";

                        console.log(data);
                        request.send(data);
                        setTimeout( function() {
                            location.reload();
                        }, 1000);
                    }';
        }
        echo    'function copy(element) {
                    window.prompt("", element.innerHTML);
                }
              </script>';
        echo '</body></html>';

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

        $sql = "UPDATE $dbTable SET downloaded=$downloaded, bad=$bad, star=$star,".($cd==""?"":" cd=$cd")." WHERE song='".mysql_real_escape_string($song)."' AND artist='".mysql_real_escape_string($artist)."'";

        if (!mysql_query($sql)) {
            http_response_code(500);
            die ('Error: '.mysql_error());
        } else {
            http_response_code(201);
        }
        
    }
?>
