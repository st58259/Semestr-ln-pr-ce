<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign in</title>
    <link rel="stylesheet" type="text/css" href="../css/reg.css">
    <script src="../js/menu.js" defer></script>
</head>

<body>

    <?php include 'connection.php'; ?>
    <?php include 'nav.php';

    $outLogErr = false;


    if (isset($_POST["signin"])) {
        if (empty($_POST["email"]) || empty($_POST["password"])) {
            $inLogErr = true;
        } else {

            $email = $_POST["email"];
            $password = $_POST["password"];
            $role = 0;
            $outLogErr = false;


            $stnt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stnt->bindParam(':email', $email);
            $stnt->execute();
            $rows = $stnt->rowCount();

            if ($rows == 0) {
                $outLogErr = true;
            }

            if (!$outLogErr) {
                $result = $stnt->setFetchMode(PDO::FETCH_ASSOC);
                foreach (new RecursiveArrayIterator($stnt->fetchAll()) as $k => $v) {
                    $passHashed = $v["password"];
                    $role = $v["role"];
                    $userid = $v["id"];
                    $username = $v["username"];
                }

                if (password_verify($password, $passHashed)) {

                    $_SESSION["username"] = $username;
                    $_SESSION["email"] = $email;
                    $_SESSION["role"] = $role;
                    $_SESSION["id"] = $userid;

                    header("location: index.php");
                } else {
                    $outLogErr = true;
                }
            }
        }
    }


    ?>

    <div class="login-container">
        <h1>Sign in</h1>
        <form action="signin.php" method="post">
            <input type="text" placeholder="Email" name="email">
            <input type="password" placeholder="Password" name="password">
            <?php
            if ($outLogErr) {
                echo '
            <div class="err">
                <p>Incorrect email or password.</p>
            </div>';
            }
            if ($inLogErr) {
                echo '
            <div class="err">
                <p>Please fill in all the required fields.</p>
            </div>';
            }
            ?>
            <input type="submit" name="signin" value="Sign in">
        </form>
        <div class="to-reg">
            <p>Don't have an account? Create one <a href="signup.php">here</a>.</p>
        </div>
    </div>
</body>

</html>