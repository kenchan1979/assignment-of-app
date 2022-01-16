<?php 
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
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

if ($post->update_status_to_done()) {
        redirect_notice(
            "show.php?id={$post->getId()}",
            GOOD_JOB
        );
    } else {
        redirect_alert(
            "show.php?id={$id}",
            MSG_POST_CANT_DELETE
        );
    }

