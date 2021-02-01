<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gallery</title>
    <link rel="stylesheet" type="text/css" href="../css/index.css">
    <link rel="stylesheet" type="text/css" href="../css/profile.css">
    <script src="../js/menu.js" defer></script>
    <script src="../js/gal.js" defer></script>
    <script src="../js/creators.js" defer></script>
    <script src="../js/profile.js" defer></script>
    <script src="../js/html2canvas.js"></script>
</head>

<?php
if (isset($_GET['id'])) {
    if (isset($_SESSION['id'])) {
        if ($_GET['id'] == $_SESSION['id']) {
            $otherProfile = false;
            $otherProfileId = -1;
        } else {
            $otherProfile = true;
            $otherProfileId = $_GET['id'];
        }
    } else {
        $otherProfile = true;
        $otherProfileId = $_GET['id'];
    }
} else {
    $otherProfile = false;
    $otherProfileId = -1;
}
?>

<body onload="firstLoad(<?php if (!$otherProfile) {
                            echo $_SESSION['id'];
                        } else {
                            echo $otherProfileId;
                        } ?>)">

    <?php include 'connection.php'; ?>
    <?php include 'nav.php'; ?>

    <?php
    if (isset($_GET["signout"])) {
        session_destroy();
        header("location: signin.php");
    }

    if (isset($_POST['submit'])) {
        $prevFileName = uniqid('', true) . '.txt';
        $prevFile = $prevFileName;

        $prevData = $_POST['prev'];
        $prevDataArr = explode(',', $prevData);
        $current = $prevDataArr[1];

        file_put_contents($prevFile, $current);
        $prevFile = file_get_contents(addslashes($prevFile));

        $stnt = $conn->prepare("UPDATE users SET picture = :picture WHERE id = :id");
        $stnt->bindParam(':picture', $prevFile);
        $stnt->bindParam(':id', $_SESSION["id"]);
        $stnt->execute();

        unlink($prevFileName);
        header("location: profile.php");
    }

    $stnt = $conn->prepare("SELECT users.* ,(SELECT COUNT(id) FROM pictures WHERE pictures.creator_id = users.id) AS pics, (SELECT COUNT(id) FROM likes WHERE likes.user_id = users.id) AS likes FROM users WHERE users.id = :id");

    if ($otherProfile) {
        $stnt->bindParam(':id', $otherProfileId);
    } else {
        $stnt->bindParam(':id', $_SESSION['id']);
    }
    $stnt->execute();

    $name = "";
    $email = "";
    $date;
    $img;
    $d;
    $pics;
    $likes;

    $result = $stnt->setFetchMode(PDO::FETCH_ASSOC);
    foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
        $name = $v["username"];
        $email = $v["email"];
        $picture = $v["picture"];
        $date = $v["date"];
        $d = date("d. m. Y", strtotime($date));
        $pics = $v["pics"];
        $likes = $v["likes"];
    }

    $isfav = 0;

    if (isset($_SESSION['id']) && $otherProfileId) {
        $stnt = $conn->prepare("SELECT COUNT(id) AS isfav FROM fav_creators WHERE user_id = :userid && creator_id = :creatorid");
        $stnt->bindParam(':userid', $_SESSION['id']);
        $stnt->bindParam(':creatorid', $otherProfileId);
        $stnt->execute();
        $result = $stnt->setFetchMode(PDO::FETCH_ASSOC);
        foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
            $isfav = $v["isfav"];
        }
    }
    ?>

    <div class="profile-container">
        <h1>Profile</h1>

        <?php
        if (!$otherProfile) {
            echo '<div class="profile-signout-button" onclick="signout()">Sign out</div>';
        }
        ?>

        <div class="profile-content">
            <form action="profile.php" method="post" enctype="multipart/form-data">
                <div class="profile-left" id="profileLeft">
                    <div class="screen-area" id="screenArea">
                        <div class="profile-cover" id="profileCover" onmouseover="cover(<?php echo $otherProfileId; ?>, 0)" onmouseout="cover(<?php echo $otherProfileId; ?>, 1)">
                            <label for="profile-image-input" id="fileInputLabel"></label>
                        </div>

                        <?php if ($picture != null) {
                            echo '<div class="profileImg" id="img-area" style="background-image: url(data:image/jpeg;base64,' . $picture . ') "></div>';
                        } else {
                            echo '<div class="profileImg" id="img-area" style="background-image: url(../icons/profile.png)"></div>';
                        }
                        ?>

                        <input id="profile-image-input" name="file" type="file" style="position: fixed; top: -100em" onchange="confirmChange()" accept="image/*">
                    </div>
                </div>
                <input type="text" name="prev" id="prev" hidden>
                <img alt="preview" src="none" id="screenShot" width="200" height="200" hidden>

                <div class="profile-right">
                    <?php echo '
                    <div class="profile-info">
                        <div class="p-s"></div>
                        <p class="profile-l">Name:</p>
                        <p class="profile-p">' . $name . '</p>
                        <p class="profile-l">Number of pictures:</p>
                        <p class="profile-p">' . $pics . '</p>
                        <p class="profile-l">Number of likes:</p>
                        <p class="profile-p">' . $likes . '</p>
                        <p class="profile-l">Member since:</p>
                        <p class="profile-p">' . $d . '</p>
                        <input type="submit" name="submit" class="profile-save-button" id="profileSave" value="Save">
                    </div>
                    <div class="profile-private">';

                    echo '<div id="isFav" hidden>' . $isfav . '</div>';
                    if (!$otherProfile) {
                        echo '<p class="profile-l">Account email:</p>
                        <p class="profile-p">' . $email . '</p>
                        <div class="profile-upload-button" id="uploadB" onclick="toUpload()">Upload</div>';
                    } else {
                        if (isset($_SESSION['id'])) {
                            if ($isfav) {
                                echo '<img src="../icons/starFull.png" class="profile-fav"  id="profile-fav-img" onclick="favCreator(' . $otherProfileId . ')">';
                            } else {
                                echo '<img src="../icons/star.png" class="profile-fav"  id="profile-fav-img" onclick="favCreator(' . $otherProfileId . ')">';
                            }
                        }
                    }
                    echo '</div>';
                    ?>
                </div>
            </form>
        </div>
    </div>

    <div id="images-container">



    </div>
    <div class="load-more">
        <div class="load-more-button" id="loadMoreButton" onclick="loadMore(
            <?php if (!$otherProfile) {
                echo $_SESSION['id'];
            } else {
                echo $otherProfileId;
            } ?>)">Load more
        </div>
    </div>

    <?php include 'popup.php'; ?>

    <div id="favPageHelper" hidden>0</div>
    <div id="userIdHelper" hidden>
        <?php if (isset($_SESSION['id'])) {
            echo $_SESSION['id'];
        } else {
            echo -1;
        } ?>
    </div>

</body>

</html>