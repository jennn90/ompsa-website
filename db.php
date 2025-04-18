<?php

#conn=mysqli_connect(
    'localhost',
    'root',
    '', 
    'admin_system'
);
if($conn) {
    echo "successfull";

} else{
    echo "not connected";
}
?>
