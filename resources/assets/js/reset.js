var pathArray = window.location.pathname.split('/');

//SORTEAR A IMAGEM DE FUNDO;
sortear();

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
