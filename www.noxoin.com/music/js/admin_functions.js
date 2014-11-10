function updateSong(element){
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
}

function toggleInsertion() {
    var insertElem = document.getElementById("insertion");
    if (insertElem.hidden == true) {
        document.getElementById("insertion").hidden = false;
    } else { 
        document.getElementById("insertion").hidden = true;
    }
}
