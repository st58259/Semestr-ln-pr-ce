

<?php
echo "<link rel='stylesheet' href='../css/nav.css'>";
echo '<nav>
        <div class="nav-logo-container">
            <img alt="logo" src="../images/logo.png">
        </div>
        <div class="menu-item" id="menuItem" onclick="showMenu()"></div>
        <div class="nav-menu-container">
            <div class="nav-item"><a href="index.php">Gallery</a></div>
            <div class="nav-item"><a href="favourites.php">Favourites</a></div>
            <div class="nav-item"><a href="creators.php">Creators</a></div>';

if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] == 2) {
        echo '<div class="nav-item"><a href="users.php">Users</a></div>';
    }
}

if (isset($_SESSION["username"])) {
    echo '<div class="nav-item"><a href="profile.php">Profile</a></div>';
} else {
    echo ' <div class="nav-item"><a href="signin.php">Sign in</a></div>';
}

echo ' </div>
    </nav>';

echo '<div class="right-nav" id="rightNav">
<div class="right-nav-logo-container">
    <img alt="logo" src="../images/logo.png">
</div>
<div class="right-nav-menu-container" id="rightCont">   
            <div class="nav-item" id="nav1"><a href="index.php">Gallery</a></div>
            <div class="nav-item" id="nav2"><a href="favourites.php">Favourites</a></div>
            <div class="nav-item" id="nav3"><a href="creators.php">Creators</a></div>';

if (isset($_SESSION["username"])) {
    echo '<div class="nav-item" id="nav4"><a href="profile.php">Profile</a></div>';
} else {
    echo ' <div class="nav-item" id="nav5"><a href="signin.php">Sign in</a></div>';
}
if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] == 2) {
        echo '<div class="nav-item" id="nav6"><a href="users.php">Users</a></div>';
    }
}
echo '
</div>
    </div>
';
?>