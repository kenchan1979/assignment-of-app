<h2 class="title">コメントするプラン</h2>
<div class="comment-blog">
    <p><?= mb_substr(nl2br(h($post->getBody())), 0, 200) ?></p>
    <div class="comment-post-show"><a href="/posts/show.php?id=<?= h($post->getId()) ?>">プランを確認する</a></div>
</div>