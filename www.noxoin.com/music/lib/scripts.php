        <script src="/music/js/cookies.js"></script>
<?php
        if($user == $authority) {
?>
    <script src="/music/js/admin_functions.js"></script>
<?php
        }
?>
        <script>
            function enqueueAll() {
                SCM.loadPlaylist(<?php echo "[$playlist]";?>);
                SCM.play();
                play = true;
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
