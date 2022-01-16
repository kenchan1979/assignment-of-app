<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
require_once __DIR__ . '/../../models/Comments.php';
require_once __DIR__ . '/../../models/Topic.php';

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

$id = filter_input(INPUT_GET, 'id');
$comment = Comment::find($id);

if (empty($comment)) {
    redirect_alert(
        '/',
        MSG_COMMENT_DOES_NOT_EXIST
    );
}

if ($current_user['id'] !== $comment->getUserId()) {
    redirect_alert(
        "/posts/show.php?id={$comment->getPostId()}",
        MSG_COMMENT_CANNOT_BE_MODIFIED
    );
}

$post_data = get_post_data();
if ($post_data) {
    $comment->updateProperty($post_data);
}

$post = $comment->getPost();
?>
<!DOCTYPE html>
<html lang="ja"><?php include_once __DIR__ . '/../common/_head.php' ?>

<body> <?php include_once __DIR__ . '/../common/_header.php' ?>
    <div class="wrapper wrapper-comment">
        <div>
        <?php include_once __DIR__ . '/_post.php' ?>
        <?php include_once __DIR__ . '/../common/_alert.php' ?>

        <form action="update.php" method="post">
            <?php include_once __DIR__ . '/_form.php' ?>
            <input type="hidden" name="comment[id]" value="<?= h($comment->getId()) ?>">
            <div class="form-group comment-btn">
                <input type="submit" class="btn" value="更新">
            </div>
        </form>
        </div>
    </div>

    <?php include_once __DIR__ . '/../common/_footer.php' ?>
</body>

</html>