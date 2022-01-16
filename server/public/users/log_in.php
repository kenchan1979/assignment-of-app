<?php
require_once __DIR__ . '/../../common/functions.php';
require_once __DIR__ . '/../../models/User.php';

session_start();

$token = generate_token();
$alert = get_alert();
$errors = get_errors();
$user = new User(get_post_data());
?>
<!DOCTYPE html>
<html lang="ja">

<?php include_once __DIR__ . '/../common/_head.php' ?>

<body>
    <?php include_once __DIR__ . '/../common/_header.php' ?>

    <div class="wrapper user-wrapper">
        <div class="form-main">
            <h2 class="title">ログイン</h2>
            <?php include_once __DIR__ . '/../common/_alert.php' ?>
            <form action="session_create.php" method="post">
                <div class="form-group">
                    <label for="email">メールアドレス<span class="required">必須</span></label>
                    <input type="email" id="email" name="user[email]" placeholder="メールアドレスを入力してください" required value="<?= h($user->getEmail()) ?>" <?php if ($errors['email']) echo 'class="error-field"' ?>>
                    <?php if ($errors['email']) echo (create_err_msg($errors['email'])) ?>
                </div>
                <div class="form-group">
                    <label for="password">パスワード<span class="required">必須</span></label>
                    <input type="password" id="password" name="user[password]" placeholder="パスワードを入力してください" required <?php if ($errors['password']) echo 'class="error-field"' ?>>
                    <?php if ($errors['password']) echo (create_err_msg($errors['password'])) ?>
                </div>
                <input type="hidden" name="token" value="<?= h($token) ?>">
                <div class="form-group">
                    <input type="submit" class="btn" value="ログイン">
                </div>
            </form>
        </div>
    </div>
    <?php include_once __DIR__ . '/../common/_footer.php' ?>

</body>

</html>