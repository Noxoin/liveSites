<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf8"/>
        <title>Chinese Music List</title>
        <meta name="robots" content="noindex,nofollow">
        <link rel="icon" type="image/ico" href="http://api.noxoin.com/favicon.ico">
        <link rel="stylesheet" type"text/css" href="../css/table.css">
        <style>body{max-width:1440px;margin-left:auto;margin-right:auto;padding-left:20px;padding-right:20px}table{width:100%}a{color:black}</style>
    </head>
    <body>
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

        $sql = "SELECT song,artist,youtubeURL,timestamp,downloaded,bad,star,cd FROM $dbTable ".($q != "" || $star==1 ? "WHERE ".($q != "" ? "song like '%".mysql_real_escape_string($q)."%' OR artist like '%".mysql_real_escape_string($q)."%' ".($star==1?"AND ":""): "").($star==1 ? "star='1'":"") : "")." ORDER BY ".($user==$authority?"downloaded ASC, star DESC,":"")."timestamp DESC";

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

        <div class="CSSTableGenerator" style="margin-bottom:30px;">
            <table>
                <tr>
                    <td style="width:15%">Song</td>
                    <td style="width:15%">Artist</td>
                    <td>youtubeURL</td>
                    <td width="150px;">Time Inserted</td>
<?php
                    if ($user == $authority) {
                        echo '<td width="100px">Status</td>';
                        echo '<td width="50px">CD</td>';
                    }
?>
                        <td widtd="25px">Star</td>
                    </tr>
                    <tr id="insertion" hidden>
                        <td width="15%"><input type="text" style="width:100%"></td>
                        <td width="15%"><input type="text" style="width:100%"></td>
                        <td><input type="text" style="width:100%" onkeyup="if(event.keyCode == 13) { insert(); }"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

<?php
                    //Skips the entries
                    for ($i = 0; $i < $page - 1; $i++) {
                        for ( $j = 0; $j < 20; $j++) {
                            $entry = mysql_fetch_array($result);
                        }
                    }

                    // Fill Table
                    $i = 0;
                    while ($entry = mysql_fetch_array($result)) {
                        if($i >= 20) { break;}
                        $youtubeID = substr($entry['youtubeURL'], 32);
                        if ($user == $authority) {
                            echo '<tr id="'.$i.'" '.(($entry['bad']=="1")?'style="color:red"':(($entry['downloaded']=="1")?'style="color:blue"':'')).' >';
                        } else {
                            echo '<tr>';
                        }

                            echo '<td '.($user==$authority ? 'onclick="copy(this)"':'').'>'.$entry['song'].'</td>'
                            .'<td '.($user==$authority ? 'onclick="copy(this)"':'').'>'.$entry['artist'].'</td>'
                            .'<td>'
                                .'<img src="../images/playIcon.png" style="float:right;height:20px;cursor:pointer" onclick="SCM.play({title:\''.$entry['song'].' - '.$entry['artist'].'\',url:\''.$entry['youtubeURL'].'\'})"/>'
                                .($user==$authority ?
                                    '<a href="http://www.video2mp3.net/loading.php?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D'.$youtubeID.'" style="text-decoration:none;color:inherit">'.$entry['youtubeURL'].'</a>' :
                                    '<a href="'.$entry['youtubeURL'].'" style="text-decoration:none;color:inherit">'.$entry['youtubeURL'].'</a>')
                            .'</td>'
                            .'<td>'.$entry['timestamp'].'</td>';
                            if($user == $authority) {
                                echo '<td><select onchange="updateSong(this)"><option value=""></option><option value="downloaded" '.(($entry['bad']=="0" && $entry['downloaded']=="1")?'selected':'').'>Downloaded</option><option value="bad" '.(($entry['bad']=="1")?'selected':'').'>Bad</option></select></td>';
                                echo '<td><select onchange="updateSong(this)"><option value="0"></option>';
                                for ( $j = 1; $j <= $maxCD+1; $j++) {
                                    echo "<option value=\"$j\" ".($entry['cd']==$j? "selected":"").">$j</option>";
                                }
                                echo '</select></td>';
                            }
                            echo '<td><input type="checkbox" onchange="updateSong(this)" '.($user == $authority ? '' : 'disabled').' '.($entry['star']=="1"?'checked':'').'></td>'
                            .'</tr>';
                            $i++;
                    }
?>
                </table>
            </div>

            <script>
<?php
        if($user == $authority) {
            echo 'function updateSong(element){
                        var url = document.URL;
                        url = url.replace("noxoin","api.noxoin");
                        url = url.substr(0,url.indexOf("?"));
                        var request = new XMLHttpRequest();
                        request.open("PUT", url, true);
                        request.setRequestHeader("content-type", "application/x-www-form-urlencoded;charset=utf8");
                        var tr = element.parentNode.parentNode;
                        var cells = tr.getElementsByTagName("td");

                        tr.style.color = (cells[4].firstElementChild.value=="bad"? "red" : (cells[4].firstElementChild.value == "downloaded" ? "blue" : "black"));
                        var cdIndex = cells[5].firstElementChild.selectedIndex;
                        var cdValue = cells[5].firstElementChild.value;
                        
                        var data = "song="+cells[0].innerHTML
                                +  "&artist="+cells[1].innerHTML
                                +  "&downloaded="+(cells[4].firstElementChild.value=="downloaded"?1:0)
                                +  "&bad="+(cells[4].firstElementChild.value=="bad"?1:0)
                                +  "&cd="+(cells[5].firstElementChild.value)
                                +  "&star="+(cells[6].firstElementChild.checked?1:0)
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
                        url = url.replace("noxoin","api.noxoin");
                        url = url.substr(0,url.indexOf("?"));
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
        echo    'function copy(element) {
                    window.prompt("", element.innerHTML);
                }';
        }
?>
        </script>
        <script type="text/javascript" src="http://scmplayer.net/script.js" 
        data-config="{'skin':'skins/simpleBlue/skin.css','volume':50,'autoplay':false,'shuffle':false,'repeat':1,'placement':'bottom','showplaylist':false,'playlist':[]}" ></script>
    </body>
</html>
