var cookie = new Cookie(document.cookie);

function entitiesPerPage(element) {
    var index = element.selectedIndex;
    var value = element.value;
    cookie.entryPerPage = value;

    cookie.save(365);
    window.location.href = window.location.href;
}

function starSong(element) {
    var id = element.parentNode.parentNode.id;
    var starList = cookie.starList;
    
    if(element.checked) {
        cookie.starList += id + "-";
    } else {
        cookie.starList = starList.replace("-"+id+"-","-");
    }
    cookie.save(365);
}

function Cookie(currCookie) {
    this.entryPerPage;
    this.starList;

    var startIndex = currCookie.indexOf("entryPerPage=");
    if (startIndex != -1) {
        var endIndex = currCookie.indexOf(";", startIndex);
        if (endIndex == -1) {
            endIndex = currCookie.length;
        }
        var valueStartIndex = startIndex + "entryPerPage=".length;
        this.entryPerPage = Number(currCookie.substr(valueStartIndex, endIndex - valueStartIndex));
    } else {
        this.entryPerPage = 20;
    }

    var startIndex = currCookie.indexOf("starList=");
    if (startIndex != -1) {
        var endIndex = currCookie.indexOf(";", startIndex);
        if (endIndex == -1) {
            endIndex = currCookie.length;
        }
        var valueStartIndex = startIndex + "starList=".length;
        this.starList = currCookie.substr(valueStartIndex, endIndex - valueStartIndex);
    } else {
        this.starList = "-";
    }

    this.save = function(days) {
        var d = new Date();
        d.setTime(d.getTime() + (days*24*60*60*1000));
        var expires = "expires="+d.toUTCString();

        document.cookie = "entryPerPage="+this.entryPerPage+"; "+expires;
        document.cookie = "starList="+this.starList+"; "+expires;
        console.log(document.cookie);
    }
}

