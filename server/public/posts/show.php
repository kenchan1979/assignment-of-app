<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
require_once __DIR__ . '/../../models/Topic.php';

session_start();

$token = generate_token();
$alert = get_alert();
$notice = get_notice();
$current_user = get_login_user();

$id = filter_input(INPUT_GET, 'id');
$post = Post::findWithUser($id);

if (empty($post)) {
    redirect_alert(
        '/',
        MSG_POST_DOES_NOT_EXIST
    );
}

$comments = $post->findCommentsWithUser();

?>
<!DOCTYPE html>
<html lang="ja">

<?php include_once __DIR__ . '/../common/_head.php' ?>

<body>
    <?php include_once __DIR__ . '/../common/_header.php' ?>

    <div class="wrapper posts">
        <?php include_once __DIR__ . '/../common/_notice.php' ?>
        <?php include_once __DIR__ . '/../common/_alert.php' ?>

        <article class="post-detail">
            <div class="post-user-area">
                <div class="post-user">
                    <img src="<?= h($post->getUser()->getAvatarPath()) ?>" alt="">
                    <p class="post-user-name"><?= h($post->getUser()->getName()) ?></p>
                </div>
                <div class="post-date"><?= h($post->getCreatedAt()) ?></div>
            </div>
            <div class="post-btn-edit-area">
                <div class="planarea">
                    <div>
                        <h2 class="sub-title">プラン詳細</h2>
                        <a href="/" class="btn btn-show">一覧へ</a>
                    </div>
                    <section class="plan-condition">
                        <div class="plan-content">
                            <h3 class="plan-date">プラン</h3>
                            <p class="contents"><?= h($post->getBody()) ?></p>
                        </div>
                        <h4 class="plan-date">期限日</h4>
                        <?php if (h($post->getCompletionDate())) : ?>
                            <div class="post-index-duedate greydate contents"><?= 'completion😄' . h($post->getDueDate()) ?></div><br>
                        <?php elseif (date("Y-m-d") >= h($post->getDueDate())) : ?>
                            <div class="post-index-duedate reddate contents"><?= h($post->getDueDate()) ?></div><br>
                        <?php else : ?>
                            <div class="post-index-duedate contents"><?= h($post->getDueDate()) ?></div><br>
                        <?php endif; ?>
                        <h4 class="plan-date">完了日</h4>
                        <p class="contents"><?= h($post->getCompletionDate()) ?></p>

                        <?php if ($current_user['id'] === $post->getUserId()) : ?>
                            <ul class="main-nav">
                                <?php if (h($post->getCompletionDate())) : ?>
                                    <li>
                                        <a href="/posts/done_cancel.php?id=<?= h($post->getId()) ?>" class="btn notyet-btn">未完了</a>
                                    </li>
                                <?php else : ?>
                                    <li>
                                        <a href="/posts/done.php?id=<?= h($post->getId()) ?>" class="btn done-btn">完了</a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a href="/posts/edit.php?id=<?= h($post->getId()) ?>" class="btn edit-btn">編集</a>
                                </li>
                                <li>
                                    <form action="/posts/delete.php" method="post">
                                        <input type="hidden" name="token" value="<?= h($token) ?>">
                                        <input type="hidden" name="id" value="<?= h($post->getId()) ?>">
                                        <input type="submit" value="削除" class="btn delete-btn" onclick="return confirm('学習内容を削除しますか?')">
                                    </form>
                                </li>
                            </ul>
                        <?php endif; ?>
                        <hr class="comment-hr">

                        <div class="comment">
                            <div class="comment-header">
                                <h3 class="comment-count">
                                    コメント(<?= h($post->getCommentsCount()) ?>)
                                </h3>
                                <?php if ($current_user['id']) : ?>
                                    <a href="/comments/new.php?post_id=<?= h($post->getId()) ?>" class="btn-comment-new new-comment">コメントする</a>
                                <?php endif; ?>
                            </div>
                            <hr class="comment-hr">
                            <?php if ($comments) : ?>
                                <ul class="comment-list">
                                    <?php foreach ($comments as $i => $c) : ?>
                                        <li class="comment-list-item">
                                            <div class="comment-no"><?= ++$i ?></div>
                                            <div class="comment-detail">
                                                <p class="comment-body"><?= nl2br(h($c->getComment())) ?></p>
                                                <div class="comment-user-area">
                                                    <div class="comment-user">
                                                        <img src="<?= h($c->getUser()->getAvatarPath()) ?>" alt="">
                                                        <h4 class="comment-user-name"><?= h($c->getUser()->getName()) ?></h4>
                                                    </div>
                                                    <p class="comment-date"><?= h($c->getCreatedAt()) ?></p>
                                                    <?php if ($c->getUserId() == $current_user['id']) : ?>
                                                        <div class="comment-btn-area">
                                                            <a href="/comments/edit.php?id=<?= $c->getId() ?>" class="comment-edit btn-edit">編集</a>
                                                            <form action="/comments/delete.php" method="post">
                                                                <input type="hidden" name="token" value="<?= h($token) ?>">
                                                                <input type="hidden" name="id" value="<?= h($c->getId()) ?>">
                                                                <input type="submit" value="削除" class="comment-delete btn-delete" onClick="return confirm('ブログのコメントを削除しますか？')">
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </section>
                </div><br>
            </div>
        </article><br>
    </div>
    <?php include_once __DIR__ . '/../common/_footer.php' ?>
</body>

</html>