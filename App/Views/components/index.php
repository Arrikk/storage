<?php

function __links($props = [])
{
    $default = [
        '__title' => 'Home',
        '__icon' => 'icon',
        '__class' => ''
    ];

    extract($default);
    extract($props);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="./Public/install/style.css" />
        <link rel="stylesheet" href="./Public/install/fa/css/font-awesome.min.css" />
        <title><?= $__title ?></title>
    </head>

    <body class="<?= $__class ?>">
    <?php
}

function __script()
{
    ?>
        <script src="./Public/install/js/jquery.js"></script>
        <script src="./Public/install/js/jquery.validate.min.js"></script>
        <script src="./Public/install/js/showhide.js"></script>
        <script src="./Public/install/js/script.js"></script>
    </body>
    </html>

<?php

    __genScript();
    __authScript();
    __mainScript();
}
function __genScript()
{
?>
    <script>
        window.bruiz = {
            notification: (arg) => {
                return $('.alert-box').append(`
                    <span>
                        <div class="alert alert-${arg.type ?? 'default'}">
                        ${arg.mssg ?? ''}
                            <span class="cls-btn">
                                <i class="fa fa-close"></i>
                            </span>
                        </div>
                    </span>`)
            },
            process: (item) => {
                $.ajax({
                    url: item.link,
                    method: 'POST',
                    data: item.form.serialize(),
                    beforeSend: function() {
                        item.btn.text('...').attr('disabled', 'disabled')
                    },
                    success: function(e) {
                        item.btn.text(item.btnText ?? '').attr('disabled', false)
                        item.form[0].reset()
                        bruiz.notification({
                            mssg: item.message.success,
                            type: 'success'
                        })
                        item.location ?? ''
                    },
                    error: function(e) {
                        item.btn.text(item.btnText ?? '').attr('disabled', false)
                        if (e.status == 400) {
                            e.responseJSON.forEach(error => {
                                bruiz.notification({
                                    mssg: error,
                                    type: 'warning'
                                })
                            });
                        } else if (e.status == 500) {
                            bruiz.notification({
                                mssg: item.message.error,
                                type: 'danger'
                            })
                        }
                    }
                })
            },
            BASE_URL: ''
        }

        $(document).ready(function() {

            $(document).on('click', '#toggle2', function() {
                document.body.classList.toggle('show-nav')
            })
            $(document).on('click', '#toggle', function() {
                document.body.classList.toggle('show-nav')
            })

            $(document).on('click', '.cls-btn', function(e) {
                //    if(e.target.classList.contains('close-btn')){
                let parent = e.target.parentNode;
                parent.classList.add('dismiss-alert')
                setTimeout(() => {
                    parent.classList.add('dismiss-alerts')
                }, 500)
                //    }
            })

            $(document).on('click', '#open', function() {
                $('.modal-container').addClass('show-modal')
            })
            $(document).on('click', '.close-btn', function() {
                $('.modal-container').removeClass('show-modal')
            })
        })
    </script>
<?php
}
