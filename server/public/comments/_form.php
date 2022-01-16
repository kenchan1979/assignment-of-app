<div class="form-group">
    <label for="comment">コメント<span class=" required">必須</span></label>
    <textarea name="comment[comment]" id="comment" placeholder="コメントを入力してください" required <?php if ($errors['comment']) echo 'class="error-field"' ?>><?= h($comment->getComment()) ?></textarea>
    <?php if ($errors['comment']) echo (create_err_msg($errors['comment'])) ?>
</div>
<input type="hidden" name="token" value="<?= h($token) ?>">
<input type="hidden" name="comment[post_id]" value="<?= h($post->getId()) ?>"></div>