<?php
session_start(); //logout logic 
session_unset();//unset the session and destroys it when logout.php is routed to
session_destroy();  
echo "logged_out";
?>
