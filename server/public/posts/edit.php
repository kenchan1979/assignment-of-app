<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
require_once __DIR__ . '/../../models/Topic.php';

session_start();

$token = generate_token();
$alert = get_alert();
$errors = get_errors();

// ログイン判定
$current_user = get_login_user();
if (empty($current_user)) {
    redirect_alert(
        '/users/log_in.php',
        MSG_PLEASE_SIGN_IN
    );
}

$id = filter_input(INPUT_GET, 'id');
$post = Post::find($id);

if (empty($post)) {
    redirect_alert(
        '/',
        MSG_POST_DOES_NOT_EXIST
    );
}

if ($current_user['id'] !== $post->getUserId()) {
    redirect_alert(
        "show.php?id={$id}",
        MSG_POST_CANNOT_BE_MODIFIED
    );
}

$post_data = get_post_data();
if ($post_data) {
    $post->updateProperty($post_data);
}
?>
<!DOCTYPE html>
<html lang="ja">

<?php include_once __DIR__ . '/../common/_head.php' ?>

<body>
    <?php include_once __DIR__ . '/../common/_header.php' ?>

    <div class="wrapper posts">
        <div class="form-main">
            <h2 class="body">プラン更新</h2>
            <?php include_once __DIR__ . '/../common/_alert.php' ?>
            <form action="update.php" method="post" enctype="multipart/form-data">
                <?php include_once __DIR__ . '/_form.php' ?>
                <input type="hidden" name="post[id]" value=<?= h($post->getId()) ?>>
                <div class="form-group">
                    <input type="submit" class="btn submit-btn" value="更新"><br>
                </div>
            </form>
        </div>
    </div>
    <?php include_once __DIR__ . '/../common/_footer.php' ?>
</body>

</html>