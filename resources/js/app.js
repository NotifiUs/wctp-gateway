
require('./bootstrap');

require('@fortawesome/fontawesome-free/js/fontawesome')
require('@fortawesome/fontawesome-free/js/solid');
require('@fortawesome/fontawesome-free/js/brands');


$(window).ready(function(){
    $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
    });

    $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "auto" );
    })
});
