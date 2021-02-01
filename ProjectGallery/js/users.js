function delUser(id) {
    var r = confirm("Are you sure you want to delete this user ?");
    if (r == true) {
        location.replace("users.php?del=true&id=" + id);
    }
}


function exportXml() {
    location.replace("users.php?exp=true");
}

function editUser(id) {
    document.getElementById('editPopup').style.display = "block";
    editInfo(id);
}

async function editInfo(id) {
    fetch('search.php?selectUserInfo=true&id=' + id)
        .then(res => res.json())
        .then(res => fillInputs(res));
    return;
}

async function fillInputs(objects) {
    var arr = [];
    for (item of objects) {
        document.getElementById('usernameInput').value = item.username;
        document.getElementById('emailInput').value = item.email;
        document.getElementById('idInput').value = item.id;
        document.getElementById('roleInput').value = item.role;
    }
}

function closeEdit() {
    document.getElementById('editPopup').style.display = "none";
}