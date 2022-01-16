<?php
require_once __DIR__ . '/../common/functions.php';
require_once __DIR__ . '/../models/Topic.php';
require_once __DIR__ . '/../models/User.php';

session_start();

$token = generate_token();
$alert = get_alert();
$notice = get_notice();
$current_user = get_login_user();

$page = filter_input(INPUT_GET, 'page') ?? 1;
$posts = Post::findIndexView($page);
$total_count = Post::findIndexViewCount();

?>
<!DOCTYPE html>
<html lang="ja">

<?php include_once __DIR__ . '/common/_head.php' ?>

<body>
    <?php include_once __DIR__ . '/common/_header.php' ?>
    <div class="wrapper">
        <?php include_once __DIR__ . '/common/_alert.php' ?>
        <?php include_once __DIR__ . '/common/_notice.php' ?>
        <?php if ($current_user['id']) : ?>
            <div class="mobile-area">
                <a href="/posts/new.php" class="btn btn-block btn-new">プラン登録</a>
            </div>
        <?php endif; ?>
        <div class="post-index-main">
            <article class="post-index-group">
                <?php foreach ($posts as $p) : ?>
                    <section class="post-index-list">
                        <?php if (!h($p->getCompletionDate())) : ?>
                            <section class="plan-condition">
                                <div class="plan-content">
                                    <h3 class="plan-date">プラン</h3>
                                    <p class="contents"><?= mb_substr(h($p->getBody()), 0, 30) ?><span><a href="/posts/show.php?id=<?= h($p->getId()) ?>">続きを読む</a></span></p><br>
                                </div>
                                <h4 class="plan-date">期限日</h4>
                                <p class="contents"><?= h($p->getDueDate()) ?></p><br>
                                <?php if ($current_user['id']) : ?>
                                    <ul class="main-nav">
                                        <li>
                                            <a href="/posts/done.php?id=<?= h($p->getId()) ?>" class="btn done-btn">完了</a>
                                        </li>
                                        <li>
                                            <a href="/posts/edit.php?id=<?= h($p->getId()) ?>" class="btn edit-btn">編集</a>
                                        </li>
                                        <li>
                                            <form action="/posts/delete.php" method="post">
                                                <input type="hidden" name="token" value="<?= h($token) ?>">
                                                <input type="hidden" name="id" value="<?= h($p->getId()) ?>">
                                                <input type="submit" value="削除" class="btn delete-btn" onclick="return confirm('学習内容を削除しますか?')">
                                            </form>
                                        </li>
                                    </ul>
                                <?php endif; ?>
                            </section>
                        <?php else : ?>
                            <section class="plan-condition">
                                <div class="good plan-content">good job!!</div>
                                <div class="plan-content">
                                    <h3 class="plan-date">プラン</h3>
                                    <p class="contents"><?= mb_substr(h($p->getBody()), 0, 30) ?><span><a href="/posts/show.php?id=<?= h($p->getId()) ?>">続きを読む</a></span></p><br>
                                </div>
                                <h4 class="plan-date">完了日</h4>
                                <p class="contents"><?= h($p->getCompletionDate()) ?></p>
                            </section>
                        <?php endif; ?>
                        <div class="comment">
                            <div class="comment-header">
                                <h3 class="comment-count">
                                    コメント(<?= h($p->getCommentsCount()) ?>)
                                </h3>
                                <?php if ($current_user['id']) : ?>
                                    <a href="/comments/new.php?post_id=<?= h($p->getId()) ?>" class="btn-comment-new new-comment">コメントする</a>
                                <?php endif; ?>
                            </div>
                            <hr>
                        </div><br>
                        
                        <div class="post-index-detail">
                            <div class="post-index-user">
                                <img src="<?= $p->getUser()->getAvatarPath() ?>" alt="">
                                <h4 class="post-index-user-name"><?= $p->getUser()->getName() ?></h4>
                            </div>
                            <div class="post-index-meta">
                                <p class="post-index-date"><?= $p->getCreatedAt() ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="detail">
                            <a href="/posts/show.php?id=<?= h($p->getId()) ?>" class="btn-comment-detail">詳細</a>
                        </div>
                    </section>
                <?php endforeach; ?>
            </article>
            <aside class="log-in-user-area">
                <?php if ($current_user['id']) : ?>
                    <div class="log-in-user-group">
                        <h3 class="loginuser">ログインユーザー</h3><br>
                        <div class="log-in-user">
                            <img src="<?= h($current_user['avatar']) ?>" alt="">
                            <h4 class="log-in-user-name">
                                <?= h($current_user['name']) ?>
                            </h4>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
        <div class="pager"><?= create_pager($page, $total_count) ?></div>
    </div>
    <?php include_once __DIR__ . '/common/_footer.php' ?>
</body>

</html>