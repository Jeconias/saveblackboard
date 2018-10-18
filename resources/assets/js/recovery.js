var pathArray = window.location.pathname.split('/');

//SORTEAR A IMAGEM DE FUNDO;
sortear();

$('#form_recovery').submit(function(e) {
  e.preventDefault();
  var dados = $(this).serialize();

  $.ajax({
    url: '/' + pathArray[1] + '/' + pathArray[2] + '/recovery/password',
    method: 'post',
    type: 'json',
    data: dados,
    beforeSend: function(){
      $('#email').attr('disabled', true);
      $('#form_recovery button, .g-recaptcha').hide();
      $('.gif-loading').show();
    },
    success: function(data) {
      console.log(data);
      var obj = jQuery.parseJSON(data);
      if (obj['status'] === true) {
        Mensagem(obj['msg'], 'success');
      }else{
        Mensagem(obj['msg'], 'danger');
      }
      $('#email').val('').attr('disabled', false);
      $('#form_recovery button, .g-recaptcha').show();
      $('.gif-loading').hide();
      grecaptcha.reset();
    },
    error: function(data) {
      console.log('Erro ao verificar email:');
      console.log(data);
      Mensagem('Erro ao verificar email, entre em contato com o ADM.', 'danger');
      $('#form_recovery button, .g-recaptcha').show();
      $('.gif-loading').hide();
      $('#email').val('').attr('disabled', false);
      grecaptcha.reset();
    }
  })
});

function Mensagem(text, tipo){
  $.notify({
    // options
    message: text
  }, {
    // settings
    type: tipo,
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
