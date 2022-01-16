<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
require_once __DIR__ . '/../../models/Comments.php';

session_start();

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $token = filter_input(INPUT_POST, 'token');
    if (empty($_SESSION['token']) || $_SESSION['token'] !== $token) {
        redirect_alert(
            '/',
            MSG_BAD_REQUEST
        );
    }

    $current_user = get_login_user();
    if (empty($current_user)) {
        redirect_alert(
            '/users/log_in.php',
            MSG_PLEASE_SIGN_IN
        );
    }

    $id = filter_input(INPUT_POST, 'id');
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
            MSG_COMMENT_CANNOT_BE_DELETE
        );
    }

    if ($comment->delete()) {
        redirect_notice(
            "/posts/show.php?id={$comment->getPostId()}",
            MSG_COMMENT_DELETE
        );
    } else {
        redirect_alert(
            "/posts/show.php?id={$comment->getPostId()}",
            MSG_COMMENT_CANT_DELETE
        );
    }
}
