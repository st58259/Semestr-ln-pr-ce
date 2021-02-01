<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gallery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/index.css">
    <script src="../js/menu.js" defer></script>
    <script src="../js/gal.js" defer></script>
    <script src="../js/creators.js" defer></script>
    <script src="../js/favourites.js" defer></script>
</head>

<body onload="firstLoadFav(-1)">

    <?php include 'connection.php'; ?>
    <?php include 'nav.php'; ?>

    <div id="filter-container">
        <h1 class="fav-title" id="favTitle">Favourite images</h1>
        <div class="switch-button" id="switchButton" onclick="switchFavourites()">Favourite creators</div>
    </div>

    <div id="images-container">
    </div>

    <div class="load-more">
        <div class="load-more-button" id="loadMoreButton" onclick="loadMoreFav(-1)">Load more</div>
    </div>

    <?php include 'popup.php'; ?>

    <div id="formHelper" hidden>
        <form action="profile.php" method="get" id="creatorForm">
            <input type="text" name="id" id="creatorToOpen" value="">
        </form>
    </div>
    <div id="favPageHelper" hidden>1</div>
    <div id="favModeHelper" hidden>0</div>
    <div id="imgUrlHelper" hidden></div>
    <div id="userIdHelper" hidden>
        <?php if (isset($_SESSION['id'])) {
            echo $_SESSION['id'];
        } else {
            echo -1;
        } ?>
    </div>

</body>

</html>