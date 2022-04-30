// const toggle = document.getElementById('toggle')
const close = document.getElementById('close')
const open = document.getElementById('open')
const modal = document.getElementById('modal')

//
// console.log(';LKJHGFDVBNM,');

$(document).ready(function(){

    generate_secret();
    
    $(document).on('click', '.continue', function() {
        let data = $(this).data('step');
        data++
        window.location.href = '&step='+data;
    })

    $(document).on('click', '.install', function() {

        let installText = document.getElementById('installPercent');
        let installOverlay = document.querySelector('.installOverlay');

        installOverlay.style.display = 'block'

       $.ajax({
           xhr: function() {
               var xhr = new window.XMLHttpRequest();
               xhr.upload.addEventListener('progress', (evt) => {
                   if(evt.lengthComputable()){
                       var percentCmp = ((evt.loaded / evt.total) * 100).toFixed(2);
                       installText.innerText = `${percentCmp}%`
                   }
               }, false);
               return xhr;
           },

           method: 'POST',
           url: './install/install',
           timeOut: 10000,
           beforeSend: function(){
               installText.innerText = 'Please wait...'
           },
           success: function(e){
               installText.innerHTML = e
           }
       })
    })

    $(document).on('submit', '#db-setup', function(e){

        let installText = document.getElementById('installPercent');
        let installOverlay = document.querySelector('.installOverlay');

        installOverlay.style.display = 'block'

        e.preventDefault();
        $.ajax({
            url: './install/db-set-up',
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function(){
                installText.innerText = 'Trying to establish Connection'
            },
            success: function(data){
                if(data == 'true'){
                    window.location.href = '&step=2';
                }
                else if(data == 'Creating Config File'){
                    installText.innerText = `${data}`
                } 
                else{
                    installText.innerText = `${data}`
                    installText.className = 'failed'
                    setTimeout(() => {
                        installOverlay.style.display = 'none'
                    }, 2000)
                }
            },
            error: function(){
                installText.innerText = 'Error'
                installOverlay.style.display = 'none'
            }
        })
    })

    function generate_secret(){

        let secret = ''
        let key = 'A4CD-=a/ncpeFUIoPRE354qwertyuifdss/*-+897E_9IO12;=-'
        for(let i = 0; i < 30; i++){
            let rand = Math.floor(Math.random() * key.length);
            secret += key[rand]
        }
        $('#secretKey').val(secret)
        $('#hiddenSecret').val(secret)
    }

    $(document).on('click', '#secret-key', function(){
        generate_secret()
    })

    $(document).on('click', '#completeInstallation', function() {
        $.ajax({
            url: './install/clean-logs',
            success: function(){
                window.location.href = ''
            }
        })
    })

})