function firstLoadCreators(id) {
    offset = 0;
    loadCreators();
    checkForMore(5);
}

function loadMoreCreators(id) {
    offset = offset + limit;
    loadCreators();
    checkForMore(5);
}

function loadCreators() {
    var name = document.getElementById('filter-search-input').value;
    name = name.trim();
    if(name.length == 0) {
        name = "";
    }
    fetch('search.php?loadCreators=true&name=' + name + '&limit=' + limit + '&offset=' + offset)
    .then(res => res.json())
    .then(res => convertToHtmlCreators(res))
    .then(res => res.forEach(item => document.getElementById("images-container").appendChild(item)))
}

async function convertToHtmlCreators(objects){
    var arr = [];
    for(item of objects) {
        
        var imgUrl = await getPictureCreators(item.id);
        if(imgUrl.length < 10) {
            imgUrl = "../icons/profile.png?ver=1.1";
        } else {
            imgUrl = 'data:image/jpeg;base64,' + imgUrl;
        }

        var div = document.createElement('DIV');
        var template = '<div class="image-container" onclick="openCreator(' + item.id + ')">';
        template += '<img src="' + imgUrl + '">';
        template += '<div class="title">' + item.username + '</div>';
        template += '<div class="like">' + item.likes + '</div>';
        template += '<div class="pics">' + item.pics + '</div>';
        template += '<div id="creator' + item.id + '" hidden></div></div>';

        div.innerHTML = template;
        arr.push(div.firstChild);
    }
    return arr;
}

async function getPictureCreators(id) {
    return fetch('search.php?profilePic=' + id)
    .then(response => response.text())
    .then(img => {
        return img;
    })
}

function openCreator(id) {
    document.getElementById('creatorToOpen').value = id;
    document.getElementById('creatorForm').submit();
}

function searchByTitleCreators() {
    var parent = document.getElementById('images-container');
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }
    offset = 0;
    loadCreators();
}

async function favCreator(id) {
    var userid = await getUserId();
    var isfav = document.getElementById('isFav').innerText;
    if(userid > 0) {        
        fetch('search.php?favCreator=true&id=' + id + '&userid=' + userid + '&isfav=' + isfav);
        if(isfav == 1) {
            document.getElementById('isFav').innerText = 0;
            document.getElementById('profile-fav-img').src = '../icons/star.png';
        } else {
            document.getElementById('isFav').innerText = 1;
            document.getElementById('profile-fav-img').src = '../icons/starFull.png';
        }
    }
}

function cover(x,y) {
    if(y == 0) {
        if(x > 0) {
            document.getElementById('profileCover').style = "opacity : 0";
            document.getElementById('profileLeft').style.cursor = "default";
            var z = document.getElementById('profile-image-input');
            z.disabled = true;
        } else {
            document.getElementById('profileCover').style = "opacity : 1";
            document.getElementById('profileLeft').style.cursor = "pointer";
        }
    } else {
        if(x > 0) {
            document.getElementById('profileCover').style = "opacity : 0";
        } else {
            document.getElementById('profileCover').style = "opacity : 0";
        }
    }
}