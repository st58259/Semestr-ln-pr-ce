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
    <?php include 'nav.php'; ?>
    <?php
    if (isset($_POST["signup"])) {

        if (empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"])) {
            $inRegErr = true;
        } else {

            $username = $_POST["username"];
            $password = $_POST["password"];
            $password = password_hash($password, PASSWORD_BCRYPT);
            $email = $_POST["email"];
            $role = 1;

            $stnt = $conn->prepare("SELECT email FROM users WHERE email = :email");
            $stnt->bindParam(':email', $email);
            $stnt->execute();
            $rows = $stnt->rowCount();

            if ($rows > 0) {
                $outRegErr = true;
            }

            if (!$outRegErr) {
                $stnt = $conn->prepare("INSERT INTO users (username, password, email, role)
                    VALUES (:username, :password, :email, :role)");

                $stnt->bindParam(':username', $username);
                $stnt->bindParam(':password', $password);
                $stnt->bindParam(':email', $email);
                $stnt->bindParam(':role', $role);

                $stnt->execute();

                header("location: signin.php");
            }
        }
    }
    ?>

    <div class="login-container">
        <h1>Sign up</h1>
        <form action="signup.php" method="post">
            <input type="text" placeholder="Email" name="email" autocomplete="off">
            <input type="text" placeholder="Name" name="username" autocomplete="off">
            <input type="password" placeholder="Password" name="password">
            <?php
            if ($outRegErr) {
                echo '
            <div class="err">
                <p>This email is already in use.</p>
            </div>';
            }
            if ($inRegErr) {
                echo '
            <div class="err">
                <p>Please fill in all the required fields.</p>
            </div>';
            }
            ?>
            <input type="submit" name="signup" value="Sign up">
        </form>
        <div class="to-reg">
            <p>Already have an account? Sign in <a href="signin.php">here</a>.</p>
        </div>


    </div>

</body>

</html>