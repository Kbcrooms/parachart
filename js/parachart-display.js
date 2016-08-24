jQuery(document).ready(function($){
  $(document).on('click','.parachart-chart-parallax',function(){
    $(this).next('.parachart-chart-content').slideToggle();
  });
});
