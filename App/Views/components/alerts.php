<?php

use App\Flash;

function __alert()
{
    $message = Flash::getMessage();
?>
    <div class="alert-box">
        <?php if (!empty($message)) :  ?>
            <?php foreach ($message as $message) : ?>
                <span>
                    <div class="alert alert-<?= $message['type'] ?>">
                        <?= $message['message'] ?>
                        <span class="cls-btn">
                            <i class="fa fa-close"></i>
                        </span>
                    </div>
                </span>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php
}
