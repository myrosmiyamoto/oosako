<?php
session_start();
require 'header.php';

if (isset($_SESSION['user'])) {
    // unset($_SESSION['user']);
    session_destroy();

    
}
echo'<p>また、来てね</p>';
echo'<p>
<form action="login-input.php" method="post">
    <button type="submit">もう一度ログインする</button>
</form>
</p>';


require 'footer.php'; ?>
