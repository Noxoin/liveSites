<script>
    var play = false;
    window.onkeydown = function(e) { 
        if (e.keyCode == 32) {
            if (play) {
                SCM.pause();
                play = false;
            } else {
                SCM.play();
                play = true;
            }
        }
        return !(e.keyCode == 32);
    };

    function keyPress(keyCode) {
        if (keyCode == 37) { // left arrow Key
            SCM.previous();
        } else if (keyCode == 39) { // right arrow Key
            SCM.next();
        }
    }

</script>
