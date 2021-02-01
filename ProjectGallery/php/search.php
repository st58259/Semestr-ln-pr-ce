<?php
session_start();

include 'connection.php';

if (isset($_GET["load"])) {
    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $stnt = $conn->prepare("SELECT id, title, description, created, size, type, creator_id, (SELECT COUNT(id) FROM likes WHERE likes.picture_id = pictures.id) AS likes, (SELECT COUNT(id) FROM comments WHERE comments.picture_id = pictures.id) AS comments FROM pictures ORDER BY id DESC LIMIT :offset , :limit");
    $stnt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stnt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stnt->execute();
    print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
}

if (isset($_GET["searchid"])) {
    $id = $_GET['searchid'];
    $stnt;
    if (isset($_GET['userid'])) {
        $userid = $_GET['userid'];
        $stnt = $conn->prepare("SELECT id, title, description, created, size, type, creator_id, (SELECT COUNT(id) FROM likes WHERE likes.picture_id = pictures.id) AS likes, (SELECT COUNT(id) FROM comments WHERE comments.picture_id = pictures.id) AS comments, (SELECT COUNT(id) FROM likes WHERE likes.picture_id = pictures.id AND likes.user_id = :userid ) AS isliked, (SELECT COUNT(id) FROM fav_pictures WHERE fav_pictures.picture_id = pictures.id AND fav_pictures.user_id = :userid ) AS isfav FROM pictures WHERE id = :id");
        $stnt->bindParam(':userid', $userid);
    } else {
        $stnt = $conn->prepare("SELECT id, title, description, created, size, type, creator_id, (SELECT COUNT(id) FROM likes WHERE likes.picture_id = pictures.id) AS likes, (SELECT COUNT(id) FROM comments WHERE comments.picture_id = pictures.id) AS comments FROM pictures WHERE id = :id");
    }
    $stnt->bindParam(':id', $id);
    $stnt->execute();
    print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
}

if (isset($_GET["searchTitle"])) {
    $title = $_GET['searchTitle'];
    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $param = $title . '%';
    $stnt = $conn->prepare("SELECT id, title, description, created, size, type, creator_id, (SELECT COUNT(id) FROM likes WHERE likes.picture_id = pictures.id) AS likes, (SELECT COUNT(id) FROM comments WHERE comments.picture_id = pictures.id) AS comments FROM pictures WHERE title like :title ORDER BY id DESC LIMIT :offset , :limit");
    $stnt->bindParam(':title', $param);
    $stnt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stnt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stnt->execute();
    print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
}

if (isset($_GET["searchCreator"])) {
    $id = $_GET['searchCreator'];
    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $stnt = $conn->prepare("SELECT id, title, description, created, size, type, creator_id, (SELECT COUNT(id) FROM likes WHERE likes.picture_id = pictures.id) AS likes, (SELECT COUNT(id) FROM comments WHERE comments.picture_id = pictures.id) AS comments FROM pictures WHERE creator_id = :id ORDER BY id DESC LIMIT :offset , :limit");
    $stnt->bindParam(':id', $id);
    $stnt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stnt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stnt->execute();
    print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
}


if (isset($_GET["preview"])) {
    $id = $_GET["preview"];
    $stnt = $conn->prepare("SELECT preview FROM pictures WHERE id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();
    $result = $stnt->setFetchMode(PDO::FETCH_ASSOC);
    foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
        $previmg = $v["preview"];
        //echo '<img src="data:image/jpeg;base64,' . $previmg . '"/>';
        echo $previmg;
    }
}

if (isset($_GET["img"])) {
    $id = $_GET["img"];
    $stnt = $conn->prepare("SELECT image FROM pictures WHERE id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();
    $result = $stnt->setFetchMode(PDO::FETCH_ASSOC);
    foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
        $img = $v["image"];
        //echo '<img src="data:image/*;base64,' . base64_encode($img) . '"/>';
        echo base64_encode($img);
    }
}

if (isset($_GET["like"])) {
    $id = $_GET["id"];
    $userid = $_GET["userid"];

    $stnt = $conn->prepare("SELECT COUNT(id) AS isliked FROM likes WHERE likes.picture_id = :id AND likes.user_id = :userid");
    $stnt->bindParam(':id', $id);
    $stnt->bindParam(':userid', $userid);
    $stnt->execute();
    foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
        $isliked = $v["isliked"];
    }

    if ($isliked == 0) {
        $stnt = $conn->prepare("INSERT INTO likes (user_id, picture_id) VALUES (:userid, :id)");
        $stnt->bindParam(':id', $id);
        $stnt->bindParam(':userid', $userid);
        $stnt->execute();
    } else {
        $stnt = $conn->prepare("DELETE FROM likes WHERE user_id = :userid AND picture_id = :id");
        $stnt->bindParam(':id', $id);
        $stnt->bindParam(':userid', $userid);
        $stnt->execute();
    }
}

if (isset($_GET["comm"])) {
    if (isset($_GET["commId"])) {
        $commId = $_GET["commId"];
        $stnt = $conn->prepare("DELETE FROM comments WHERE id = :commId");
        $stnt->bindParam(':commId', $commId);
        $stnt->execute();
    } else {
        if (isset($_GET["getComms"])) {
            $imgId = $_GET["imgId"];
            $stnt = $conn->prepare("SELECT comments.id, comment, created, user_id, users.username, picture_id FROM comments JOIN users ON users.id = comments.user_id WHERE picture_id = :imgId");
            $stnt->bindParam(':imgId', $imgId);
            $stnt->execute();
            print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
        } else {
            $imgId = $_GET["imgId"];
            $userId = $_GET["userId"];
            $comment = $_GET["comment"];
            $created = date("Y-m-d H:i:s");

            $stnt = $conn->prepare("INSERT INTO comments (comment, created, user_id, picture_id) VALUES (:comment, :created, :userId, :imgId)");
            $stnt->bindParam(':comment', $comment);
            $stnt->bindParam(':created', $created);
            $stnt->bindParam(':imgId', $imgId);
            $stnt->bindParam(':userId', $userId);
            $stnt->execute();
        }
    }
}

if (isset($_GET["fav"])) {
    $id = $_GET["id"];
    $userid = $_GET["userid"];
    //$isfav = $_GET["isfav"];

    $stnt = $conn->prepare("SELECT COUNT(id) AS isfav FROM fav_pictures WHERE fav_pictures.picture_id = :id AND fav_pictures.user_id = :userid");
    $stnt->bindParam(':id', $id);
    $stnt->bindParam(':userid', $userid);
    $stnt->execute();
    foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
        $isfav = $v["isfav"];
    }

    if ($isfav == 0) {
        $stnt = $conn->prepare("INSERT INTO fav_pictures (user_id, picture_id) VALUES (:userid, :id)");
        $stnt->bindParam(':id', $id);
        $stnt->bindParam(':userid', $userid);
        $stnt->execute();
    } else {
        $stnt = $conn->prepare("DELETE FROM fav_pictures WHERE user_id = :userid AND picture_id = :id");
        $stnt->bindParam(':id', $id);
        $stnt->bindParam(':userid', $userid);
        $stnt->execute();
    }
}

if (isset($_GET["loadCreators"])) {
    $name = $_GET['name'];
    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $name = $name . '%';
    $stnt = $conn->prepare("SELECT id, username, (SELECT COUNT(id) FROM pictures WHERE pictures.creator_id = users.id) AS pics, (SELECT COUNT(id) FROM likes WHERE likes.user_id = users.id) AS likes FROM users WHERE username LIKE :name AND (SELECT COUNT(id) FROM pictures WHERE pictures.creator_id = users.id) > 0 ORDER BY likes DESC LIMIT :offset , :limit");
    $stnt->bindParam(':name', $name);
    $stnt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stnt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stnt->execute();
    print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
}


if (isset($_GET["profilePic"])) {
    $id = $_GET["profilePic"];
    $stnt = $conn->prepare("SELECT picture FROM users WHERE id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();
    $result = $stnt->setFetchMode(PDO::FETCH_ASSOC);
    foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
        $profileimg = $v["picture"];
        //echo '<img src="data:image/jpeg;base64,' . $previmg . '"/>';
        echo $profileimg;
    }
}


if (isset($_GET["favCreator"])) {
    $id = $_GET["id"];
    $userid = $_GET["userid"];
    $isfav = $_GET["isfav"];

    if ($isfav == 0) {
        $stnt = $conn->prepare("INSERT INTO fav_creators (user_id, creator_id) VALUES (:userid, :id)");
        $stnt->bindParam(':id', $id);
        $stnt->bindParam(':userid', $userid);
        $stnt->execute();
    } else {
        $stnt = $conn->prepare("DELETE FROM fav_creators WHERE user_id = :userid AND creator_id = :id");
        $stnt->bindParam(':id', $id);
        $stnt->bindParam(':userid', $userid);
        $stnt->execute();
    }
}


if (isset($_GET["loadFavCreators"])) {
    $userid = $_GET['userid'];
    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $stnt = $conn->prepare("SELECT users.id, username, (SELECT COUNT(id) FROM pictures WHERE pictures.creator_id = users.id) AS pics, (SELECT COUNT(id) FROM likes WHERE likes.user_id = users.id) AS likes FROM users RIGHT JOIN fav_creators ON fav_creators.creator_id = users.id WHERE fav_creators.user_id = :userid ORDER BY fav_creators.id DESC LIMIT :offset , :limit");
    $stnt->bindParam(':userid', $userid);
    $stnt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stnt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stnt->execute();
    print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
}

if (isset($_GET["loadFavImages"])) {
    $userid = $_GET['userid'];
    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $stnt = $conn->prepare("SELECT pictures.id, title, description, created, size, type, pictures.creator_id, (SELECT COUNT(id) FROM likes WHERE likes.picture_id = pictures.id) AS likes, (SELECT COUNT(id) FROM comments WHERE comments.picture_id = pictures.id) AS comments FROM pictures RIGHT JOIN fav_pictures ON fav_pictures.picture_id = pictures.id WHERE fav_pictures.user_id = :userid ORDER BY fav_pictures.id DESC LIMIT :offset , :limit");
    $stnt->bindParam(':userid', $userid);
    $stnt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stnt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stnt->execute();
    print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
}




if (isset($_GET["checkForMore"])) {
    $mode = $_GET['mode'];
    if ($mode == 1) {   // index 
        $userId = $_GET['userId'];
        $stnt = $conn->prepare("SELECT COUNT(id) AS count FROM pictures");
        $stnt->bindParam(':userid', $userId);
        $stnt->execute();
        foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
            $x = $v["count"];
            echo $x;
        }
    }
    if ($mode == 2) {   // favourite images
        $userId = $_GET['userId'];
        $stnt = $conn->prepare("SELECT COUNT(pictures.id) AS count FROM pictures RIGHT JOIN fav_pictures ON fav_pictures.picture_id = pictures.id WHERE fav_pictures.user_id = :userid");
        $stnt->bindParam(':userid', $userId);
        $stnt->execute();
        foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
            $x = $v["count"];
            echo $x;
        }
    }
    if ($mode == 3) {   // favourite creators
        $userId = $_GET['userId'];
        $stnt = $conn->prepare("SELECT COUNT(users.id) AS count FROM users RIGHT JOIN fav_creators ON fav_creators.creator_id = users.id WHERE fav_creators.user_id = :userid");
        $stnt->bindParam(':userid', $userId);
        $stnt->execute();
        foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
            $x = $v["count"];
            echo $x;
        }
    }

    if ($mode == 4) {   // profile
        $id = $_GET['userId'];
        $stnt = $conn->prepare("SELECT COUNT(id) AS count FROM pictures WHERE creator_id = :id");
        $stnt->bindParam(':id', $id);
        $stnt->execute();
        foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
            $x = $v["count"];
            echo $x;
        }
    }


    if ($mode == 5) {   // creators 
        $title = $_GET['st'];
        $param = $title . '%';
        $stnt = $conn->prepare("SELECT COUNT(id) AS count FROM users WHERE (SELECT COUNT(id) FROM pictures WHERE pictures.creator_id = users.id) > 0 AND username like :title");
        $stnt->bindParam(':title', $param);
        $stnt->execute();
        foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
            $x = $v["count"];
            echo $x;
        }
    }

    if ($mode == 11) {   // index search
        $title = $_GET['st'];
        $param = $title . '%';
        $stnt = $conn->prepare("SELECT COUNT(id) AS count FROM pictures WHERE title like :title ORDER BY id DESC");
        $stnt->bindParam(':title', $param);
        $stnt->execute();
        /* $rows = $stnt->rowCount();
        echo $rows;*/
        foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
            $x = $v["count"];
            echo $x;
        }
    }
}



if (isset($_GET["delImg"])) {
    $id = $_GET["id"];
    $stnt = $conn->prepare("DELETE FROM pictures WHERE id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();
}

if (isset($_GET["delComm"])) {
    $id = $_GET["id"];
    $stnt = $conn->prepare("DELETE FROM comments WHERE id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();
}


if (isset($_GET["isAdmin"])) {   // index search
    $id = $_GET['id'];
    $stnt = $conn->prepare("SELECT role FROM users WHERE id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();
    foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
        $x = $v["role"];
        echo $x;
    }
}


if (isset($_GET["delImg"])) {
    $id = $_GET['id'];

    $stnt = $conn->prepare("DELETE FROM comments WHERE comments.picture_id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();

    $stnt = $conn->prepare("DELETE FROM fav_pictures WHERE fav_pictures.picture_id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();

    $stnt = $conn->prepare("DELETE FROM likes WHERE likes.picture_id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();

    $stnt = $conn->prepare("DELETE FROM pictures WHERE id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();
}

if (isset($_GET["selectUserInfo"])) {
    $id = $_GET['id'];
    $stnt = $conn->prepare("SELECT username,id,email,role FROM users WHERE id = :id");
    $stnt->bindParam(':id', $id);
    $stnt->execute();
    print_r(json_encode($stnt->FetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));
}

if (isset($_GET["getUserId"])) {
    if (isset($_SESSION['id'])) {
        echo $_SESSION['id'];
    } else {
        echo '-1';
    }
}
