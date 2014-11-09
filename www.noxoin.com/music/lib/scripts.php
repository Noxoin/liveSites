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
            function enqueueAll() {
                SCM.loadPlaylist(<?php echo "[$playlist]";?>);
                SCM.play();
            }

            function entitiesPerPage(element) {
                var index = element.selectedIndex;
                var value = element.value;
                console.log(value);
                var ca = document.cookie.split(';');
                console.log(document.cookie);

                var d = new Date();
                d.setTime(d.getTime() + (365*24*60*60*1000));
                var expires = "expires="+d.toUTCString();

                var newCookie = "";
                for(var i=0; i<ca.length; i++) {
                    var c = ca[i];
                    if (c.indexOf("_ga") != -1) {
                        continue;
                    }
                    if (newCookie != "") {
                        newCookie += "; ";
                    }
                    while (c.charAt(0)==' ') c = c.substring(1);
                    if (c.indexOf("entryPerPage") != -1) {
                        c = "entryPerPage="+value;
                    }
                    if (c.indexOf("expires=") != -1) {
                        c = expires;
                    }
                    newCookie += c;
                }

                if(newCookie.indexOf("entryPerPage") == -1) {
                    if(newCookie.length > 1) {
                        newCookie += "; ";
                    }
                    newCookie += "entryPerPage="+value;
                }
                if(newCookie.indexOf("expires") == -1) {
                    if(newCookie.length > 1) {
                        newCookie += "; ";
                    }
                    newCookie += expires;
                }
                console.log(newCookie);
                document.cookie = newCookie;
                window.location.href = window.location.href;
            }
            function toggleRepeat(element) {
                if(element.innerHTML.indexOf("All") == -1) {
                    SCM.repeatMode(1);
                    element.innerHTML = "Repeat All";
                } else {
                    SCM.repeatMode(2);
                    element.innerHTML = "Repeat One";
                }
            }
        </script>
        <script type="text/javascript" src="http://scmplayer.net/script.js" 
        data-config="{'skin':'skins/simpleBlue/skin.css','volume':50,'autoplay':false,'shuffle':false,'repeat':1,'placement':'bottom','showplaylist':false,'playlist':[]}" ></script>
