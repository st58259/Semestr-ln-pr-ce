function signout() {
    window.location.replace("profile.php?signout=true");
}

function toUpload() {
    window.location.replace("upload.php");
}

function confirmChange() {
    if (document.getElementById("profile-image-input").files.length != 0) {
        document.getElementById('profileSave').style.display = "block";
    }
    loadPrev();
}

function saveProfile() {
    window.location.replace("profile.php?save=true");
}

function screenshotProfile() {
    html2canvas(document.getElementById('screenArea')).then(function(canvas) {
        var image = canvas.toDataURL('image/jpeg', 0.8);
        document.getElementById('screenShot').src = image;
        document.getElementById('prev').value = image;
    });
}


function loadPrev() {
    var x = document.getElementById("profile-image-input").files;
    if (x.length > 0) {
        var fileReader = new FileReader();
        fileReader.onload = function(event) {
            document.getElementById("img-area").style.backgroundImage = 'url(' + event.target.result + ')';
        };
        fileReader.readAsDataURL(x[0]);
        setTimeout(screenshotProfile, 100);
    }
}
