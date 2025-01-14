<?php require 'header.php'; ?>
<h1>ネイルデザインアルバム</h1>
<h2>新規登録</h2>
<form action="sign_up-output.php" method="post">
    <table>
        <tr>
            <td>ログイン名</td>
            <td><input type="text" name="login" value=""></td>
        </tr>
        <tr>
            <td>パスワード</td>
            <td><input type="password" name="password" value=""></td>
        </tr>
    </table>
    <p><input type="submit" value="登録"></p>
</form>

<hr>
<p>既にユーザー登録がお済みの方はこちらからログインしてください。</p>
<p>
    <form action="login-input.php" method="post">
        <input type="submit" value="ログイン画面に移動">
    </form>
</p>
<?php require 'footer.php'; ?>