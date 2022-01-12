// Get the current year for the copyright
$('#year').text(new Date().getFullYear());

// Configure Slider
$('.carousel').carousel({
  interval: 2000,
  pause: 'null'
});

// Lightbox Init
$(document).on('click', '[data-toggle="lightbox"]', function(event) {
  event.preventDefault();
  $(this).ekkoLightbox();
});

//navbar animation on utilise jquery si on va scroller de plus de 30px du haut de l'ecran alors on ajoute la class opaque au navbar sinon $navbar on enleve la navbar avec removeClass
$(windows).scroll(function(){
  if($(this).scrollTop() > 30) {
    $('.navbar').addClass('opaque');
  } else {
    $('.navbar').removeClass('opaque');
  }

})




// remove probaly

$(window).scroll(function () {
  if ($(this).scrollTop() > 30) {
    $('.navbar').addClass('opaque');
  } else {
    $('.navbar').removeClass('opaque');
  }
});