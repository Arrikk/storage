<?php
function __mainScript()
{
    ?>
    <script>
        $('#update').on('submit', function(e){
            e.preventDefault()
            let btn = $(this).find('button')
            bruiz.process({
                link: './account/update',
                form: $(this),btn,
                btnText: 'Update',
                message: {
                    success: 'Profile Updated',
                    error: "Update Failed"
                }
            })
        })
    </script>
    <?php
}