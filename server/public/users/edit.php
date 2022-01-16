<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
require_once __DIR__ . '/../../models/User.php';

session_start();

$token = generate_token();
$alert = get_alert();
$errors = get_errors();
$post_data = get_post_data();
$current_user = get_login_user();
if (empty($current_user)) {
    redirect_alert(
        '/users/log_in.php',
        MSG_PLEASE_SIGN_IN
    );
}

$user = User::find($current_user['id']);
if ($post_data) {
    $user->updateProperty($post_data);
}
?>
<!DOCTYPE html>
<html lang="ja">

<?php require_once __DIR__ . '/../common/_head.php' ?>

<body>
    <?php include_once __DIR__ . '/../common/_header.php' ?>

    <div class="wrapper user-wrapper">
        <div class="form-main">
            <h2 class="title">アカウント更新</h2>
            <?php include_once __DIR__ . '/../common/_alert.php' ?>
            <form action="update.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="email">メールアドレス<span class="required">必須</span></label>
                    <input type="email" id="email" name="user[email]" placeholder="メールアドレスを入力してください" required value="<?= h($user->getEmail()) ?>" <?php if ($errors['email']) echo 'class="error-field"' ?>>
                    <?php if ($errors['email']) echo (create_err_msg($errors['email'])) ?>
                </div>
                <div class="form-group">
                    <label for="now_password">現在のパスワード(パスワード変更時に入力)<span class="required">必須</span></label>
                    <input type="password" id="now_password" name="user[now_password]" placeholder="半角英数を組み合わせた8文字以上" <?php if ($errors['now_password']) echo 'class="error-field"' ?>>
                    <?php if ($errors['now_password']) echo (create_err_msg($errors['now_password'])) ?>
                </div>
                <div class="form-group">
                    <label for="password">新しいパスワード<span class="required">必須</span></label>
                    <input type="password" id="password" name="user[password]" placeholder="半角英数を組み合わせた8文字以上" <?php if ($errors['password']) echo 'class="error-field"' ?>>
                    <?php if ($errors['password']) echo (create_err_msg($errors['password'])) ?>
                </div>
                <div class="form-group">
                    <label for="comfirm_password">確認用パスワード<span class="required">必須</span></label>
                    <input type="password" id="confirm_password" name="user[confirm_password]" placeholder="半角英数を組み合わせた8文字以上" <?php if ($errors['confirm_password']) echo 'class="error-field"' ?>>
                    <?php if ($errors['confirm_password']) echo (create_err_msg($errors['confirm_password'])) ?>
                </div>
                <div class="form-group">
                    <label for="name">ユーザー名<span class="required">必須</span></label>
                    <input type="text" id="name" name="user[name]" placeholder="ユーザー名を入力してください" required value="<?= h($user->getName()) ?>" <?php if ($errors['name']) echo 'class="error-field"' ?>>
                    <?php if ($errors['name']) echo (create_err_msg($errors['name'])) ?>
                </div>
                <div class="form-group">
                    <label for="profile">自己紹介<span class="required">必須</span></label>
                    <textarea id="profile" name="user[profile]" rows="5" placeholder="自己紹介を入力してください" required <?php if ($errors['profile']) echo 'class="error-field"' ?>><?= h($user->getProfile()) ?></textarea>
                    <?php if ($errors['profile']) echo (create_err_msg($errors['profile'])) ?>
                </div>
                <div class="form-group">
                    <label for="avtar">プロフィール画像</label>
                    <?php if ($user->getAvatar()) : ?>
                        <img src="<?= $user->getAvatarPath() ?>" alt="" class="image-preview">
                    <?php endif; ?>
                    <input type="file" id="avtar" name="avtar">
                    <?php if ($errors['avatar']) echo (create_err_msg($errors['avatar'])) ?>
                </div>
                <input type="hidden" name="token" value="<?= h($token) ?>">
                <div class="form-group">
                    <input type="submit" class="btn" value="更新">
                </div>
            </form>
            <form action="delete.php" method="post">
                <input type="hidden" name="token" value="<?= h($token) ?>">
                <input type="hidden" name="id" value="<?= h($user->getId()) ?>">
                <input type="submit" value="削除" class="btn btn-delete" onclick="return confirm('アカウントを削除しますか?')">
            </form>
        </div>
    </div>
    <?php include_once __DIR__ . '/../common/_footer.php' ?>
</body>

</html>