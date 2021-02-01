function switchFavourites(x) {
    var parent = document.getElementById('images-container');
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }

    var mode = document.getElementById('favModeHelper').innerText;
    
    if(mode == 0) {
        offset = 0;
        loadFavCreators();
        document.getElementById('favModeHelper').innerText = 1;
        document.getElementById('favTitle').innerText = "Favourite creators";
        document.getElementById('switchButton').innerText = "Favourite images";
        checkForMore(3);
    } else {
        offset = 0;
        loadFavImages();
        document.getElementById('favModeHelper').innerText = 0;
        document.getElementById('favTitle').innerText = "Favourite images";
        document.getElementById('switchButton').innerText = "Favourite creators";
        checkForMore(2);
    }
}

function firstLoadFav(id) {
    offset = 0;
    loadFavImages();
    checkForMore(2);
}

function loadMoreFav(id) {
    var mode = document.getElementById('favModeHelper').innerText;
    offset = offset + limit;
    if(mode == 0) {
        loadFavImages();
        checkForMore(2);
    } else {
        loadFavCreators();
        checkForMore(3);
    }
}

async function loadFavCreators() {
    var userid = await getUserId();
    fetch('search.php?loadFavCreators=true&userid=' + userid + '&limit=' + limit + '&offset=' + offset)
    .then(res => res.json())
    .then(res => convertToHtmlCreators(res))
    .then(res => res.forEach(item => document.getElementById("images-container").appendChild(item)))
}

async function loadFavImages() {
    var userid = await getUserId();
    fetch('search.php?loadFavImages=true&userid=' + userid + '&limit=' + limit + '&offset=' + offset)
    .then(res => res.json())
    .then(res => convertToHtml(res))
    .then(res => res.forEach(item => document.getElementById("images-container").appendChild(item)))
}