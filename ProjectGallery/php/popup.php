

<?php
echo '<div id="image-popup-container">
<div class="image-popup-content" id="popup-cont">
    <div class="popup-body" id="popup-body">
        <div class="popup-left">
            <div class="popup-middle" id="popup-middle">
                <img alt="photo" src="../images/4.jpg" id="popupImg">
            </div>
        </div>
        <div class="spacer" id="popup-spacer"></div>
        <div class="popup-right">
            <div class="popup-id" id="popupId" hidden></div>
            <div id="popupIsLiked" hidden></div>
            <div id="popupIsFav" hidden></div>
            <div class="popup-title" id="popupTitle"></div>
            <div class="popup-description" id="popupDesc"></div>
            <div class="popup-right-bottom">
                <div class="popup-like-middle">
                    <img alt="like" src="../icons/likered.png" id="popup-like-img" onclick="like()">
                    <div class="popup-like" id="popupLikes"></div>
                    <img alt="favourite" src="../icons/star.png" id="popup-fav-img" onclick="fav()">
                </div>
                <img alt="delete" src="../icons/trash.png" id="popupDelImg" class="popup-delete" onclick="del()">
                <div class="popup-comments" id="popupComms">
                    <div class="new-comment" id="newComm">
                        <div class="comm-img" id="popupShowCommImg" onclick="showComm()"></div>
                        <input type="text" placeholder="Comment..." id="popupCommIput" autocomplete="off">
                        <div class="popup-add-button" onclick="AddComm()" id="popupAddCommButton">Add comment</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>';
?>