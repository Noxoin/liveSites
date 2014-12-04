var play = false;
window.onkeydown = function(e) { 
    if (document.activeElement.tagName === "INPUT") {
        return true;
    }
    if (e.keyCode == 32 && document.activeElement.tagName != "INPUT") { // enter key and prevents scrolling
        if (play) {
            SCM.pause();
            play = false;
        } else {
            SCM.play();
            play = true;
        }
        return false;
    }
    return true;
};

function keyPress(keyCode) {
    if (keyCode == 37) { // left arrow Key
        SCM.previous();
    } else if (keyCode == 39) { // right arrow Key
        SCM.next();
    }
}
