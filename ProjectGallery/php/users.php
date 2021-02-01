<?php
session_start();

if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] != 2) {
        header("location: index.php");
    }
} else {
    header("location: index.php");
}

?>


<!DOCTYPE html>
<html>

<head>
    <title>Gallery</title>
    <link rel="stylesheet" type="text/css" href="../css/index.css">
    <link rel="stylesheet" type="text/css" href="../css/profile.css">
    <link rel="stylesheet" type="text/css" href="../css/users.css">
    <script src="../js/menu.js" defer></script>
    <script src="../js/users.js" defer></script>
</head>


<body>

    <?php include 'connection.php'; ?>
    <?php include 'nav.php'; ?>

    <?php

    if (isset($_GET["signout"])) {
        session_destroy();
        header("location: signin.php");
    }

    if (isset($_POST['saveEdit'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $stnt = $conn->prepare("SELECT email FROM users WHERE email = :email");
        $stnt->bindParam(':email', $email);
        $stnt->execute();
        $rows = $stnt->rowCount();

        if ($rows == 0) {
            $stnt = $conn->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id");
            $stnt->bindParam(':username', $username);
            $stnt->bindParam(':email', $email);
            $stnt->bindParam(':role', $role);
            $stnt->bindParam(':id', $id);
            $stnt->execute();
            header("location: users.php");
        }
    }

    if (isset($_GET['del'])) {
        $delid = $_GET['id'];

        $stnt = $conn->prepare("DELETE comments FROM comments JOIN pictures ON comments.picture_id = pictures.id WHERE user_id = :id OR pictures.creator_id = :id");
        $stnt->bindParam(':id', $delid);
        $stnt->execute();

        $stnt = $conn->prepare("DELETE fav_creators FROM fav_creators WHERE creator_id = :id OR user_id = :id");
        $stnt->bindParam(':id', $delid);
        $stnt->execute();

        $stnt = $conn->prepare("DELETE fav_pictures FROM fav_pictures JOIN pictures ON fav_pictures.picture_id = pictures.id WHERE user_id = :id OR pictures.creator_id = :id");
        $stnt->bindParam(':id', $delid);
        $stnt->execute();

        $stnt = $conn->prepare("DELETE likes FROM likes JOIN pictures ON likes.picture_id = pictures.id WHERE user_id = :id OR pictures.creator_id = :id");
        $stnt->bindParam(':id', $delid);
        $stnt->execute();

        $stnt = $conn->prepare("DELETE FROM pictures WHERE creator_id = :id");
        $stnt->bindParam(':id', $delid);
        $stnt->execute();

        $stnt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stnt->bindParam(':id', $delid);
        $stnt->execute();
        header("location: users.php");
    }





    ?>



    <h1>Users</h1>

    <table>
        <tr>
            <th>id</th>
            <th>username</th>
            <th>email</th>
            <th>member since</th>
            <th>role</th>
        </tr>

        <?php

        $stnt = $conn->prepare("SELECT users.*, roles.role AS role FROM users JOIN roles ON users.role = roles.id");
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
            $date = $v["date"];
            $d = date("d. m. Y", strtotime($date));
            $id = $v["id"];
            $role = $v["role"];
            echo '
            <tr>
            <td>' . $id . '</td>
            <td>' . $name . '</td>
            <td>' . $email . '</td>
            <td>' . $date . '</td>
            <td>' . $role . '</td>
            <td class="del" onclick="editUser(' . $id . ')">⚙</td>
            <td class="del" onclick="delUser(' . $id . ')">✘</td>
            </tr>';
        }


        if (isset($_GET['exp'])) {

            $stnt = $conn->prepare("SELECT users.*, roles.role AS role FROM users JOIN roles ON users.role = roles.id");
            $stnt->execute();

            $name = "";
            $email = "";
            $date;
            $img;
            $d;
            $pics;
            $likes;

            $result = $stnt->setFetchMode(PDO::FETCH_ASSOC);

            $dom = new DomDocument('1.0', 'UTF-8');
            $root = $dom->appendChild($dom->createElement('users'));

            foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
                $name = $v["username"];
                $email = $v["email"];
                $date = $v["date"];
                $d = date("d. m. Y", strtotime($date));
                $id = $v["id"];
                $role = $v["role"];

                $user = $dom->createElement('user');
                $root->appendChild($user);

                $attr = $dom->createAttribute('id');
                $attr->appendChild($dom->createTextNode($id));
                $user->appendChild($attr);
                $attr = $dom->createAttribute('username');
                $attr->appendChild($dom->createTextNode($name));
                $user->appendChild($attr);
                $attr = $dom->createAttribute('email');
                $attr->appendChild($dom->createTextNode($email));
                $user->appendChild($attr);
                $attr = $dom->createAttribute('member_since');
                $attr->appendChild($dom->createTextNode($d));
                $user->appendChild($attr);
                $attr = $dom->createAttribute('role');
                $attr->appendChild($dom->createTextNode($role));
                $user->appendChild($attr);
            }

            $dom->formatOutput = true;

            $exp = $dom->saveXML();
            $dom->save('../download/users_export.xml');
        }

        ?>

    </table>

    <div class="users-table">
        <div class="user-item">
        </div>
    </div>

    <div class="editPopup" id="editPopup">
        <h2>Edit user</h2>
        <div class="closeEdit" onclick="closeEdit()">x</div>
        <form action="users.php" method="post">
            <label>username</label>
            <input type="text" name="username" id="usernameInput">
            <label>email</label>
            <input type="text" name="email" id="emailInput">
            <label>role</label>
            <select name="role" id="roleInput">
                <option value="1">user</option>
                <option value="2">admin</option>
            </select>
            <input type="text" name="id" id="idInput" hidden>
            <input type="submit" name="saveEdit" value="Save">
        </form>
    </div>

    <div class="export-buttons">
        <div class="export-button" onclick="exportXml()">Export to XML</div>
        <?php
        if (isset($_GET['exp'])) {
            echo '
        <a href="../download/users_export.xml" download>
            <div class="download-button">Download XML</div>
        </a>';
        }
        ?>
    </div>






    <script>

    </script>

    <?php





    ?>

</body>



</html>