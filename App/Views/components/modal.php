<?php
function __modal()
{
    $user = $GLOBALS['user'];
    ?>
        <div class="modal-container">
            <div class="modal">
                <div class="modal-header">
                    <h3>Update Account</h3>
                    <div class="close-btn"><i class="fa fa-close"></i></div>
                </div>
                <div class="modal-content">
                    <form action="" class="modal-form" id="update">
                        <div>
                            <label for="">Email</label>
                            <input type="email" value="<?= $user->email ?? '' ?>" name="email" id="" class="form-input">
                        </div>
                        <div>
                            <label for="">Username</label>
                            <input type="text" value="<?= $user->username ?? '' ?>" name="username" id="" class="form-input">
                        </div>
                        <button type="submit">Update</button>
                    </form>
                </div>
            </div>
        </div>
    <?php
}