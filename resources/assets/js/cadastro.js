var pathArray = window.location.pathname.split('/');
$(document).ready(function() {
  //SORTEAR IMAGEM DE FUNDO
  sortear();

  //CHECAR SE EMAIL É VALIDO
  $('#email').on('keyup change',function() {
    var email = $(this).val().replace(/ /g, '');

    if (validateEmail(email) && email != '') {
      $.ajax({
        url: '/' + pathArray[1] + '/checkturmas',
        method: 'post',
        type: 'json',
        data: ({
          'email': email
        }),
        beforeSend: function() {
          $('#email').addClass('input-loading');
        },
        success: function(data) {
          var obj = jQuery.parseJSON(data);
          if (obj['email'] == false) {
            $('#email').css({
              'background-image': 'url("' + '/' + pathArray[1] + '/resources/assets/images/check.png")'
            });
            $('#submit').attr('disabled', false).css('cursor', 'pointer');
          } else if (obj['email'] == true) {
            $('#email').css({
              'background-image': 'url("' + '/' + pathArray[1] + '/resources/assets/images/negative.png")'
            });
            $('#submit').attr('disabled', true).css('cursor', 'not-allowed');
          }
        },
        error: function(data) {
          console.log(data)
        }
      });
    } else {
      $('#email').css({
        'background-image': 'none'
      });
    }
  });

  function validateEmail(email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test(email);
  }

  function sortear(){
    var list = ['classroom.jpeg', 'story.jpeg', 'study.jpeg'];
    var number = Math.trunc(Math.random() * (2 - 0 + 1) + 0);
    $('body').css({
    'background':'url("/'+pathArray[1]+'/resources/assets/images/'+list[number]+'")',
    'background-position':'center',
    'background-repeat':'no-repeat',
    'background-size':'cover'});
  }

  /*VERIFICAÇÃO DO CAMPO DE SENHA */
  var password = document.getElementById("senha")
  ,confirm_password = document.getElementById("senha_");
  function validatePassword(){
    if(password.value != confirm_password.value) {
      confirm_password.setCustomValidity("Senhas diferentes!");
    } else {
      confirm_password.setCustomValidity('');
    }
  }
password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
});
