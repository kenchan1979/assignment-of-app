<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
require_once __DIR__ . '/../../models/Topic.php';

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
            MSG_POST_CANNOT_BE_DELETE
        );
    }

    if ($post->delete()) {
        redirect_notice(
            '/',
            MSG_POST_DELETE
        );
    } else {
        redirect_alert(
            "show.php?id={$id}",
            MSG_POST_CANT_DELETE
        );
    }
}
