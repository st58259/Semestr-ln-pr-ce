var popup = document.getElementById("image-popup-container");
var popupm = document.getElementById("popup-middle");
var popups = document.getElementById("popup-spacer");
var popupc = document.getElementById("popup-cont");
var popupb = document.getElementById("popup-body");
var btn = document.getElementById("myBtn");
var span = document.getElementsByClassName("close")[0];

function openImage(id) {
    popup.style.display = "block";
    popup.style.opacity = 1;
    loadPopup(id);
    document.getElementById("popup-cont").scrollTo(0,0);
}

var limit = 20;
var offset = 0;

var loadMoreTitle = false;

var visitProfileId = -1;

/* Load */

async function firstLoad(id) {
    visitProfileId = -1;
    if(id == -1) {  // index
        offset = 0;
        load();
        checkForMore(1);
    } else if(id > 0) {  // profile
        offset = 0;
        searchByCreator(id);    

        var userid = await getUserId();
        if (parseInt(userid) != parseInt(id)) {
            visitProfileId = id;
        }
        checkForMore(4);
    }
}

async function loadMore(id) {
    if(id == -1) {  // index
        offset = offset + limit;
        if(loadMoreTitle) {
            loadByTitle();
            checkForMore(11);
        } else {
            load();
            checkForMore(1);
        }
        
    } else if(id > 0) {  // profile
        offset = offset + limit;
        searchByCreator(id);

        var userid = await getUserId();
        if (parseInt(userid) != parseInt(id)) {
            visitProfileId = id;
        }
        checkForMore(4);
    } 
}

async function checkForMore(mode) {
    var userid = await getUserId();
    var userid = await getUserId();

    if(visitProfileId  > 0) {
        var count = await getCount(mode, visitProfileId);
    } else {
        var count = await getCount(mode, userid);
    }

    if(offset >= count - limit) {
        document.getElementById('loadMoreButton').style.display = "none";
    } else {
        document.getElementById('loadMoreButton').style.display = "block";
    }
}

async function getCount(mode, userid) {
    if(mode == 11 || mode == 5) {
        var x = document.getElementById('filter-search-input').value;
        x = x.trim();
        return fetch('search.php?checkForMore=true&st=' + x + '&mode=' + mode + '&userId=' + userid)
        .then(res => res.text())
        .then(res => {return res;});
    } else {
            return fetch('search.php?checkForMore=true&mode=' + mode + '&userId=' + userid)
            .then(res => res.text())
            .then(res => {return res;});
    }
}


function load() {
    fetch('search.php?load=true&limit=' + limit + '&offset=' + offset)
    .then(res => res.json())
    .then(res => convertToHtml(res))
    .then(res => res.forEach(item => document.getElementById("images-container").appendChild(item)))
}

function loadByTitle() {
    var x = document.getElementById('filter-search-input').value;
    x = x.trim();
    fetch('search.php?searchTitle=' + x + '&limit=' + limit + '&offset=' + offset)
    .then(res => res.json())
    .then(res => convertToHtml(res))
    .then(res => res.forEach(item => document.getElementById("images-container").appendChild(item)))
}


/************/

/* search */

function searchByTitle() {
    var parent = document.getElementById('images-container');
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }
    offset = 0;
    loadMoreTitle = true;
    loadByTitle();
    checkForMore(11);
}

function searchByCreator(id) {
    fetch('search.php?searchCreator=' + id + '&limit=' + limit + '&offset=' + offset)
    .then(res => res.json())
    .then(res => convertToHtml(res))
    .then(res => res.forEach(item => document.getElementById("images-container").appendChild(item)))
}

async function convertToHtml(objects){
    var arr = [];
    for(item of objects) {
        var imgUrl = await getPicture(item.id);
        var div = document.createElement('DIV');
        var template = '<div class="image-container" onclick="openImage(' + item.id + ')">';
        template += '<img src="data:image/jpeg;base64,' + imgUrl + '">';
        template += '<div class="title">' + item.title + '</div>';
        template += '<div class="like" id="imglike' + item.id + '">' + item.likes + '</div>';
        template += '<div class="comment">' + item.comments + '</div>';
        template += '<div id="img' + item.id + '" hidden></div></div>';

        div.innerHTML = template;
        arr.push(div.firstChild);
    }
    return arr;
}

async function getPicture(id) {
    return fetch('search.php?preview=' + id)
    .then(response => response.text())
    .then(img => {
        return img;
    })
}

async function getPictureFull(id) {
    return fetch('search.php?img=' + id)
    .then(response => response.text())
    .then(img => {
        return img;
    })
}

var windowWidth = window.innerWidth;
var multiplier = 2;
var offsetAmount = multiplier * (windowWidth/5);

var maxOffset = 0;
var amountTimes = 0;


async function loadPopup(id) {
    document.getElementById('popupImg').src = '../icons/load.gif?ver=1.2';
    var userid = await getUserId();
    if(userid > 0) {
        res = await fetch('search.php?searchid=' + id + '&userid=' + userid);
        document.getElementById('newComm').style.display = "block";
        document.getElementById('popup-like-img').style.cursor = "pointer";
        document.getElementById('popup-fav-img').style.cursor = "pointer";
    } else {
        res = await fetch('search.php?searchid=' + id);
        document.getElementById('newComm').style.display = "none";
        document.getElementById('popup-like-img').style.cursor = "default";
        document.getElementById('popup-fav-img').style.cursor = "default";
    }
    
    var data = await res.json();
    var admin = await isAdmin();
    var owner = 0;
    
    
    for(item of data) {
        document.getElementById('popupId').textContent = item.id;
        document.getElementById('popupTitle').textContent = item.title;
        document.getElementById('popupDesc').innerText = item.description;
        document.getElementById('popupLikes').innerText = item.likes;
        document.getElementById('popupIsLiked').innerText = item.isliked;
        document.getElementById('popupIsFav').innerText = item.isfav;
        owner = await isOwner(item.creator_id);

        if(userid > 0 && item.isliked == 1) {
            document.getElementById('popup-like-img').src = '../icons/likeredfull.png';
        } else {
            document.getElementById('popup-like-img').src = '../icons/likered.png';
        }
        if(userid > 0 && item.isfav == 1) {
            document.getElementById('popup-fav-img').src = '../icons/starFull.png';
        } else {
            document.getElementById('popup-fav-img').src = '../icons/star.png';
        }
    }
    var imgUrl = await getPictureFull(id);
    document.getElementById('popupImg').src = 'data:image/jpeg;base64,' + imgUrl;
    document.getElementById('popupImg').style.opacity = 1;
    document.getElementById('popupShowCommImg').style.display = "block";
    document.getElementById('popupCommIput').style.display = "none";
    document.getElementById('popupCommIput').value = "";
    document.getElementById('popupAddCommButton').style.display = "none";

    if(admin == 1 || owner == 1) {
        document.getElementById('popupDelImg').style.display = "block";
    } else {
        document.getElementById('popupDelImg').style.display = "none";
    }

    loadComments(id);
}

/************/

/* comments */

async function loadComments(id) {
    var elements = document.getElementsByClassName('popup-comment');
    while(elements.length > 0){
        elements[0].parentNode.removeChild(elements[0]);
    }

    fetch('search.php?comm=true&getComms=true&imgId=' + id)
    .then(res => res.json())
    .then(res => convertToHtmlComms(res))
    .then(res => res.forEach(item => document.getElementById("popupComms").appendChild(item)));
    return;
}

async function convertToHtmlComms(objects){
    var arr = [];
    var admin = await isAdmin();

    for(item of objects) {
        var div = document.createElement('DIV');
        
        var arrx = item.created.split('-');
        var year = arrx[0];
        var month = arrx[1];
        var day = arrx[2].split(' ')[0]

        var owner = await isOwner(item.user_id);

        var template = '<div class="popup-comment" id="comment' + item.id + '">';
        template += '<div class="com-name">'+ item.username +'</div>';
        template += '<div class="com-date">'+ day + '. ' + month + '. ' + year + '</div>';
        if(admin == 1 || owner == 1) {
        template += '<div class="com-delete-com" onclick="delComm(' + item.id + ')"></div>';
        }
        template += '<div class="com-body">'+ item.comment +'</div>';
        template +=  '</div>';

        div.innerHTML = template;
        arr.push(div.firstChild);
    }
    return arr;
}

function showComm() {
    document.getElementById('popupShowCommImg').style.display = "none";
    document.getElementById('popupCommIput').style.display = "block";
    document.getElementById('popupAddCommButton').style.display = "block";
}

async function AddComm() {
    var imgId = document.getElementById('popupId').textContent;
    var userid = await getUserId();
    var comment = document.getElementById('popupCommIput').value;
    if(userid > 0) {     
        await fetch('search.php?comm=true&imgId=' + imgId + '&userId=' + userid + '&comment=' + comment);
        
        var tempHeight = document.getElementById('popupComms').offsetHeight;      // get height
        document.getElementById('popupComms').style.height = tempHeight + "px";     // set fixed height -> fix for flickering

        //await loadComments(imgId);

        var elements = document.getElementsByClassName('popup-comment');
    while(elements.length > 0){
        elements[0].parentNode.removeChild(elements[0]);
    }

    fetch('search.php?comm=true&getComms=true&imgId=' + imgId)
    .then(res => res.json())
    .then(res => convertToHtmlComms(res))
    .then(res => res.forEach(item => document.getElementById("popupComms").appendChild(item)));

        setTimeout(h, 50);
    }

    function h() {
        document.getElementById('popupComms').style.height = "auto";
        document.getElementById('popupShowCommImg').style.display = "block";
        document.getElementById('popupCommIput').style.display = "none";
        document.getElementById('popupCommIput').value = "";
        document.getElementById('popupAddCommButton').style.display = "none";
    }
}


/* like, fav */

async function like() {
    var imgId = document.getElementById('popupId').textContent;
    var userid = await getUserId();
    var isliked = document.getElementById('popupIsLiked').innerText;
    if(userid > 0) {        
        fetch('search.php?like=true&id=' + imgId + '&userid=' + userid + '&isliked=' + isliked);
        if(isliked == 1) {
            document.getElementById('popupIsLiked').innerText = 0;
            document.getElementById('popup-like-img').src = '../icons/likered.png';
            var l = parseInt(document.getElementById('popupLikes').innerText);
            document.getElementById('popupLikes').innerText = --l;
            document.getElementById('imglike' + imgId).innerText = l;
        } else {
            document.getElementById('popupIsLiked').innerText = 1;
            document.getElementById('popup-like-img').src = '../icons/likeredfull.png';
            var l = parseInt(document.getElementById('popupLikes').innerText);
            document.getElementById('popupLikes').innerText = ++l;
            document.getElementById('imglike' + imgId).innerText = l;
        }
    }
}

async function fav() {
    var imgId = document.getElementById('popupId').textContent;
    var userid = await getUserId();
    var isfav = document.getElementById('popupIsFav').innerText;
    if(userid > 0) {        
        fetch('search.php?fav=true&id=' + imgId + '&userid=' + userid + '&isfav=' + isfav);
        if(isfav == 1) {
            document.getElementById('popupIsFav').innerText = 0;
            document.getElementById('popup-fav-img').src = '../icons/star.png';
        } else {
            document.getElementById('popupIsFav').innerText = 1;
            document.getElementById('popup-fav-img').src = '../icons/starFull.png';
        }
    }

    var isfavPage = document.getElementById('favPageHelper').innerText;
    if(isfavPage != 0) {
        var ele = document.getElementById('img' + imgId);
        ele = ele.parentElement;
        var parent = document.getElementById('images-container');
        parent.removeChild(ele);
    }
}

/* Ckecks */

async function isAdmin() {
    var userid = await getUserId();
    var x = await getAdmin(userid);
    if(parseInt(x) == 2) {
        return 1;
    } else {
        return 0;
    }
}

async function getAdmin(userid) {
    return fetch('search.php?isAdmin=true&id=' + userid)
    .then(res => res.text())
    .then(res => {return res});
}

async function isOwner(id) {
    var userid = await getUserId();
    if(parseInt(userid) == parseInt(id)) {
        return 1;
    } else {
        return 0;
    }
}

async function getUserId() {
    return fetch('search.php?getUserId=true')
    .then(res => res.text())
    .then(res => {return res;});
}

/* Deletes */

function delComm(id) {
    var r = confirm("Are you sure you want to delete this comment ?");
    if (r == true) {
        fetch('search.php?delComm=true&id=' + id);
        var el = document.getElementById('comment' + id);
        el.remove();
    }
}

function del() {
    var r = confirm("Are you sure you want to delete this picture ?");
    if (r == true) {
        var id = document.getElementById('popupId').textContent;
        var ele = document.getElementById('img' + id);
        ele = ele.parentElement;
        var parent = document.getElementById('images-container');
        parent.removeChild(ele);
        popup.style.display = "none";
        popup.style.opacity = 0;

        fetch('search.php?delImg=true&id=' + id);
    }
}





