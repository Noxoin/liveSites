        <div class="CSSTableGenerator" style="margin-bottom:10px;">
            <table>
                <tr>
                    <td>Song</td>
                    <td>Artist</td>
                    <td style="width:400px">youtubeURL</td>
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
                        <td><input type="text" style="width:100%"></td>
                        <td><input type="text" style="width:100%"></td>
                        <td><input type="text" style="width:100%" onkeyup="if(event.keyCode == 13) { insert(); }"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

<?php
                    //Skips the entries
                    for ($i = 0; $i < $page - 1; $i++) {
                        for ( $j = 0; $j < $entryPerPage; $j++) {
                            $entry = mysql_fetch_array($result);
                        }
                    }

                    // Fill Table
                    $playlist = '';
                    $i = 0;
                    while ($entry = mysql_fetch_array($result)) {
                        if($i >= $entryPerPage) { break;}
                        if ($i > 0) { $playlist .= ',';}
                        $songJSON = '{\'title\':\''.mysql_real_escape_string($entry['song']).' - '.mysql_real_escape_string($entry['artist']).'\',\'url\':\''.$entry['youtubeURL'].'\'}';
                        $playlist .= $songJSON;
                        $youtubeID = substr($entry['youtubeURL'], 32);
                        if ($user == $authority) {
                            echo '<tr id="'.$i.'" '.(($entry['bad']=="1")?'style="color:red"':(($entry['downloaded']=="1")?'style="color:blue"':'')).' >';
                        } else {
                            echo '<tr>';
                        }

                            echo '<td '.($user==$authority ? 'onclick="copy(this)"':'').'>'.$entry['song'].'</td>'
                            .'<td '.($user==$authority ? 'onclick="copy(this)"':'').'>'.$entry['artist'].'</td>'
                            .'<td>'
                                .'<img src="/music/images/plus-24.png" style="float:right;height:20px;cursor:pointer" onclick="SCM.nqueue('.$songJSON.')"/>'
                                .'<img src="/music/images/playIcon.png" style="float:right;height:20px;cursor:pointer" onclick="SCM.play('.$songJSON.')"/>'
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
