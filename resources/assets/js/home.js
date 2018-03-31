$(document).ready(function() {
  var pathArray = window.location.pathname.split('/');
  $('#file_upload').hide();
  //VIEW PARA IMAGENS
  dataid = null;
  $('.article-img').on('click', 'img', function() {
    var img = $(this).attr('src');
    dataid = $(this).next().attr('data-id');
    img = img.replace('mine_', '');
    //ABRIR MODAL
    $('#myModal').slideToggle();
    //CARREGAR IMAGEM NO MODAL
    $('.img_modal-content').attr('src', img);
  });

  //ESCONDER IMAGENS DA SECTION SELECIONADA
    $('.div-hide').on('click', function() {
      var esconder = $(this).parents('.section-img').find('.div-img');

      if (esconder.is(':hidden')) {
        $(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
      } else {
        $(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
      }
      esconder.slideToggle('slow');
    });



  $('#btn-close, .modal').on('click', function() {
    if ($(this).attr('id') == 'myModal' || $(this).attr('id') == 'btn-close') {
      $('#myModal').slideToggle();
    }
    return false;
  });

  $('.article-img').on('click', 'span', function(e) {
    e.preventDefault();
    var dataid = $(this).attr('data-id');
    console.log(dataid);
    window.location.href = '/saveblackboard/download/' + dataid;
  });

  $('#upload').on('click', function() {
    $('#files:file').trigger('click');
  });

  $('#files:file').on('change', function(e) {
    e.preventDefault();
    $.ajax({
      url: '/saveblackboard/upload',
      method: 'post',
      type: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data: new FormData($('#form_upload')[0]),
      beforeSend: function() {
         //var percentVal = '0%';
         //console.log(percentVal);
      },
      progress: function(e) {
        $('#span-loading').css('opacity', '1');
        if (e.lengthComputable) {
          var pct = (e.loaded / e.total) * 100;
          $('.progress-bar').css('width', Math.trunc(pct)+'%');
          $('#span-loading').text(Math.trunc(pct)+'%');
        } else {
          //console.warn('Content Length not reported!');
        }
      },
      success: function(xhr) {
        var obj = jQuery.parseJSON(xhr);
        if (obj['status'] == true) {
          $('.progress-bar').css('width', '0');
          $('#span-loading').css('opacity', '0');
          Mensagem('Imagem enviada!', 'success');
          var data_div = $('.article-img [data-div="' + obj['data'] + '"]').val();
          if (data_div == '') {
            $('.article-img [data-div="' + obj['data'] + '"]').after('<div class="div-img"><img src="' + obj['file'] + '" alt=""><span data-id="' + obj['id_file'] + '"><i class="fas fa-download fa-lg"></i></span></div>');
          } else if (data_div == undefined) {
            $('.article-img').prepend('<section class="section-img"><div class="div-hide" data-div="' + obj['data'] + '"><h1>' + obj['data'] + '</h1><i class="fas fa-chevron-up"></i></div><div class="div-img"><img src="' + obj['file'] + '" alt=""><span data-id="' + obj['id_file'] + '"><i class="fas fa-download fa-lg"></i></span></div></section>');
          }
        } else {
          Mensagem(obj, 'warning');
        }
      },
      error: function(data) {
        Mensagem('Erro ao enviar a imagem! Entre em contato com o ADM.', 'danger');
      }
    });
  });

  //REMOVER IMAGEM
  $('#btn-remove').on('click', function(e) {
    e.preventDefault();
    $.ajax({
      url: '/saveblackboard/remove',
      method: 'post',
      type: 'json',
      data: ({
        'id': dataid
      }),
      success: function(xhr) {
        var obj = jQuery.parseJSON(xhr);
        if (obj == true) {
          Mensagem('Imagem removida!', 'danger');
          $('*[data-id="'+dataid+'"]').parent('.div-img').remove();
        } else {
          Mensagem(obj, 'warning');
        }
      },
      error: function(data) {
        Mensagem('Erro ao remover a imagem! Entre em contato com o ADM.', 'danger');
      }
    });
  });
});

function Mensagem(text, type) {
  $.notify({
    // options
    message: text
  }, {
    // settings
    type: type,
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
