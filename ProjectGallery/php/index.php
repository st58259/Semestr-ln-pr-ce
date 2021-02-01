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

</head>

<body onload="firstLoad(-1)">

    <?php include 'connection.php'; ?>
    <?php include 'nav.php'; ?>

    <div id="filter-container">
        <input type="text" id="filter-search-input" placeholder="Search..." name="fname" autocomplete="off">
        <div class="search" id="search-gallery" onclick="searchByTitle()"></div>
    </div>

    <div id="images-container">
    </div>

    <div class="load-more">
        <div class="load-more-button" id="loadMoreButton" onclick="loadMore(-1)">Load more</div>
    </div>

    <?php include 'popup.php'; ?>

    <div id="favPageHelper" hidden>0</div>
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