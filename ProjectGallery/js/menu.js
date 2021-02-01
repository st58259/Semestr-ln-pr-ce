var rightNav = document.getElementById("rightNav");
var menuItem = document.getElementById('menuItem');
var rightCont = document.getElementById('rightCont');
var nav1 = document.getElementById('nav1');
var nav2 = document.getElementById('nav2');
var nav3 = document.getElementById('nav3');
var nav4 = document.getElementById('nav4');
var nav5 = document.getElementById('nav5');
var nav6 = document.getElementById('nav6');


window.onclick = function(event) {
    if (event.target != rightNav && event.target != menuItem && event.target != rightCont
        && event.target != nav1 && event.target != nav2 && event.target != nav3 && event.target != nav4
        && event.target != nav5 && event.target != nav6
        ) {
        rightNav.style.right = "-250px";
    }
    if(popup != undefined) {
        if (event.target == popup || event.target == popupm || event.target == popups || event.target == popupc || event.target == popupb) {
            popup.style.display = "none";
            popup.style.opacity = 0;
        }
    }   
}

function showMenu() {
    rightNav.style.right = "0px";
}


window.onerror = suppressError; 
function suppressError() {
    return true;
}