<div class="form-group">
    <label for="body">プラン内容<span class="required">必須</span></label>
    <textarea name="post[body]" id="body" placeholder="プラン内容を入力してください" required value="<?= h($post->getBody()) ?>" <?php if ($errors['body']) echo 'class="error-field"' ?>></textarea>
    <?php if ($errors['body']) echo (create_err_msg($errors['body'])) ?>
</div>
<div class="form-group">
    <label for="due_date">期限日<span class="required">必須</span></label>
    <input type="date" id="due_date" name="post[due_date]" placeholder="期限日を入力してください" required value="<?= h($post->getDueDate()) ?>" <?php if ($errors['due_date']) echo 'class="error-field"' ?>>
    <?php if ($errors['due_date']) echo (create_err_msg($errors['due_date'])) ?>
</div>
<input type="hidden" name="token" value="<?= h($token) ?>">