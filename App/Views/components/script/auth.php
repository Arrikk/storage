<?php
function __authScript()
{
?>
    <script>
        $(document).on('submit', '#login', function(e) {
            e.preventDefault();
            let btn = $(this).find('button')
            let form = $(this)
            bruiz.process({
                link: './auth/login',
                form,
                btn,
                message: {
                    error: 'Login Failed',
                    success: '<a href="/App/">Click Redirect</a>',
                },
                btnText: 'Login'
            })
        })

        $(document).on('submit', '#register', function(e) {
            e.preventDefault();
            let btn = $(this).find('button')
            let form = $(this)
            bruiz.process({
                link: './auth/register',
                form,
                btn,
                message: {
                    error: 'Registration Failed',
                    success: 'Registration Successful'
                },
                btnText: 'Register'
            })
        })
    </script>
<?php
}
