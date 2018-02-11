<?php
/*
 * in the meantime-> will just redirect to index.php
 * on the next level-> will clear user cookie(or something else) and will forget the user until he'll login again
*/
header('Location: index.php');
?>