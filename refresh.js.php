<?php
    /**
     * Automatically refresh on QR scan
     * ~ktemkin
     */
    require_once(dirname(__FILE__).'/../../../config.php');

    //get the QUBA id we're to monitor
    $quba = required_param('quba', PARAM_INT);

    //set the content type to JS
    header("content-type: application/x-javascript");
?>

M.autorefresh = {};


<?php


    //and set the URL which will handle refreshes
    echo 'M.autorefresh.detectURL="'.$CFG->wwwroot.'/question/type/checkoff/reqrefresh.php";'."\n";    

    //and set the QUBA id
    echo 'M.autorefresh.qubaid='.$quba.';'."\n";

?>


M.autorefresh.reloadPage = function()
{
    $('#dimmer').fadeIn();
    $('.refreshpopup').slideDown();
    location.reload();
}

M.autorefresh.handleResponse = function(data)
{
    if(data == '1')
        M.autorefresh.reloadPage();
}

M.autorefresh.checkForRefresh = function()
{
    //if no QR code is onscreen, don't check for refresh
    if($('.qrcode:onScreen').length == 0)
        return;

    $.get(M.autorefresh.detectURL, {quba: M.autorefresh.qubaid}, M.autorefresh.handleResponse);
}

M.autorefresh.init = function()
{

    //add the jQuery extension onScreen, (c) 2011 Ben Pickles
    (function(a){a.expr[":"].onScreen=function(b){var c=a(window),d=c.scrollTop(),e=c.height(),f=d+e,g=a(b),h=g.offset().top,i=g.height(),j=h+i;return h>=d&&h<f||j>d&&j<=f||i>e&&h<=d&&j>=f}})(jQuery);

    setInterval(M.autorefresh.checkForRefresh, 1000);
}
