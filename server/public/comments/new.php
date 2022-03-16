<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
require_once __DIR__ . '/../../models/Topic.php';
require_once __DIR__ . '/../../models/Comments.php';

session_start();

$token = generate_token();
$alert = get_alert();
$errors = get_errors();

$current_user = get_login_user();
if (empty($current_user)) {
    redirect_alert(
        '/users/log_in.php',
        MSG_PLEASE_SIGN_IN
    );
}

$post_id = filter_input(INPUT_GET, 'post_id');
$post = Post::find($post_id);

if (empty($post)) {
    redirect_alert(
        '/',
        MSG_POST_DOES_NOT_EXIST
    );
}

$comment = new Comment(get_post_data());
?>
<!DOCTYPE html>
<html lang="ja"><?php include_once __DIR__ . '/../common/_head.php' ?>

<body>
    <?php include_once __DIR__ . '/../common/_header.php' ?>
    <div class="wrapper wrapper-comment">
        <div class="">
            <?php include_once __DIR__ . '/_post.php' ?>
            <?php include_once __DIR__ . '/../common/_alert.php' ?>

            <form action="create.php" method="post">
                <?php include_once __DIR__ . '/_form.php' ?>
                <div class="form-group">
                    <input type="submit" class="btn comment-btn" value="登録">
                </div>
            </form>
        </div>
    </div>

    <?php include_once __DIR__ . '/../common/_footer.php' ?>
</body>

</html>