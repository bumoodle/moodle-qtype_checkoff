<link rel="stylesheet" type="text/css" href="ckstyles.css" />

<?php
   
    //start a HTML table
    echo '<table class="crpairs"><tr><th>Challenge</th><th>Response</th></tr>';
    
    if(!isset($_GET))
        die();
    
    //add each challenge / response pair to the table
    foreach($_GET as $challenge => $response)
        echo "<tr><td>$challenge</td><td>$response</td></tr>\n";
            
    echo '</table>';
    
?>
