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
    location.reload();
}

M.autorefresh.handleResponse = function(data)
{
    if(data == '1')
        M.autorefresh.reloadPage();
}

M.autorefresh.checkForRefresh = function()
{
    $.get(M.autorefresh.detectURL, {quba: M.autorefresh.qubaid}, M.autorefresh.handleResponse);
}

M.autorefresh.init = function()
{
    setInterval(M.autorefresh.checkForRefresh, 5000);
}
