var pathArray = window.location.pathname.split('/');

//SORTEAR A IMAGEM DE FUNDO;
sortear();
//DEFININDO O TAMANHO DE CADA SEXTION
//$('.apresentacao > section').css('height', $(document).height());

$('#form_login').submit(function(e) {
  var email = $('#email').val();
  var pass = $('#password').val();

  e.preventDefault();

  $.ajax({
    url: '/' + pathArray[1] + '/login',
    method: 'post',
    type: 'json',
    data: ({
      'email': email,
      'password': pass
    }),
    success: function(data) {
      var obj = jQuery.parseJSON(data);
      console.log(data);
      if (obj === true) {
        window.location.href = '/' + pathArray[1] + '/';
      } else {
        $.notify({
          // options
          message: obj
        }, {
          // settings
          type: 'danger',
          position: 'fixed',
          delay: 5000,
          timer: 1000,
          mouse_over: 'pause',
          animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
          },
          placement: {
            from: "top",
            align: "center"
          },

        });
      }
    },
    error: function(data) {
      console.log(data);
    }
  })
});

if ($('#email').val() != '') {
  $('#password').focus();
} else {
  $('#email').focus();
}

function sortear() {
  var list = ['classroom.jpeg', 'story.jpeg', 'study.jpeg'];
  var number = Math.trunc(Math.random() * (2 - 0 + 1) + 0);
  $('body').css({
    'background': 'url("/' + pathArray[1] + '/resources/assets/images/' + list[number] + '")',
    'background-position': 'center',
    'background-repeat': 'no-repeat',
    'background-size': 'cover'
  });
}
