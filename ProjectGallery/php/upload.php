<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gallery</title>
    <link rel="stylesheet" type="text/css" href="../css/index.css">
    <link rel="stylesheet" type="text/css" href="../css/upload.css">
    <script src="../js/html2canvas.js?ver=1.1"></script>
    <script src="../js/menu.js" defer></script>
</head>

<body>

    <?php include 'connection.php';

    if (isset($_GET["signout"])) {
        session_destroy();
        header("location: index.php");
    }

    if (isset($_POST["upload"])) {
        $file = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $file = file_get_contents(addslashes($file));

        $prevFileName = uniqid('', true) . '.txt';
        $prevFile = $prevFileName;

        $prevData = $_POST['prev'];
        $prevDataArr = explode(',', $prevData);
        $current = $prevDataArr[1];

        file_put_contents($prevFile, $current);
        $prevFile = file_get_contents(addslashes($prevFile));

        $fileExt = explode('.', $fileName);
        $fileExt = strtolower(end($fileExt));

        $stnt = $conn->prepare("INSERT INTO pictures (title, description, image, preview, created, size, type, creator_id)
                    VALUES (:title, :description, :image, :preview, :created, :size, :type, :creator)");

        $a = '';
        $date = date("Y-m-d H:i:s");
        $c = 1;
        if (isset($_POST['description'])) {
            $desc = $_POST['description'];
        } else {
            $desc = "";
        }
        $title = $_POST['title'];

        $stnt->bindParam(':title', $title);
        $stnt->bindParam(':description', $desc);
        $stnt->bindParam(':image', $file);
        $stnt->bindParam(':preview', $prevFile);
        $stnt->bindParam(':created', $date);
        $stnt->bindParam(':size', $fileSize);
        $stnt->bindParam(':type', $fileExt);
        $stnt->bindParam(':creator', $_SESSION["id"]);
        $stnt->execute();

        unlink($prevFileName);
    }






    ?>

    <?php include 'nav.php'; ?>


    <div class="upload-container">
        <h1>Upload</h1>
        <div class="upload-content">
            <div class="img-border" id="img-border">
                <div class="img-con" id="img-con">
                    <div class="img-area" id="img-area"></div>
                </div>
            </div>

            <img alt="preview" src="none" id="screenShot" width="200" height="200" hidden>
            <input type='button' id='but_screenshot' value='Take screenshot' onclick='screenshot();' hidden>

            <div class="upload-inputs">
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="upload-inputs-left">
                        <div class="p-s"></div>
                        <p class="profile-l">Title:</p>
                        <input type="text" id="titleName" name="title" required>
                        <p class="profile-l">Description:</p>
                        <textarea name="description" rows="4" maxlength="200"></textarea>
                    </div>

                    <div class="upload-inputs-right">
                        <input type="file" class="file-input" id="file-input" name="file" onchange="loadPrev()" accept="image/*" required>
                        <!--<input type="file" class="file-input" id="file-preview" name="file-preview" accept="image/*">-->
                        <input type="text" name="prev" id="prev" hidden>
                        <input type="submit" name="upload" value="Upload" id="submit-button">
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function screenshot() {
            html2canvas(document.getElementById('img-con')).then(function(canvas) {
                var image = canvas.toDataURL('image/jpeg', 0.8);
                document.getElementById('screenShot').src = image;
                document.getElementById('prev').value = image;
                var b = document.getElementById("submit-button");
                b.disabled = false;
            });
        }

        function loadPrev() {
            var x = document.getElementById("file-input").files;
            if (x.length > 0) {
                var fileReader = new FileReader();

                fileReader.onload = function(event) {
                    document.getElementById("img-area").style.backgroundImage = 'url(' + event.target.result + ')';
                };

                var b = document.getElementById("submit-button");
                b.disabled = true;
                fileReader.readAsDataURL(x[0]);

                setTimeout(screenshot, 50);
            }
        }

        function signout() {
            window.location.replace("profile.php?signout=true");
        }


        /*function movelr() {
            var x = document.getElementById("vollr").value;
            document.getElementById('baseImg').style.marginLeft = x + "px";
            //console.log(x);
            //document.write(x);
        }

        function moveud() {
            var x = document.getElementById("volud").value;
            document.getElementById('baseImg').style.marginTop = -x + "px";
            // console.log(x);
        }

        function resize() {
            var x = parseInt(document.getElementById("resize").value);
            var z = x + 2;
            document.getElementById('box').style.width = z + "px"
            document.getElementById('box').style.height = z + "px"
            document.getElementById('img-box').style.width = x + "px";
            document.getElementById('img-box').style.height = x + "px";
            var iw = document.getElementById('img-area').offsetWidth;

            var c = document.getElementById('img-box').style.top = (iw / 2) - (x / 2) - 4 + "px";
            var c = document.getElementById('img-box').style.left = (ih / 2) - (x / 2) - 4 + "px";
            console.log(z);
        }*/
    </script>
</body>

</html>