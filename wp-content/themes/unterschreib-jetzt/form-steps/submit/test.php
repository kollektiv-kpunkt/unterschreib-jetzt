<?php

system( "which gs > /dev/null", $retval );
if ( $retval == 0 ) {
    echo("Installed");
} else {
    echo("Not installed");
}

phpinfo();

?>