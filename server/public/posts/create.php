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

    $input_params =
        filter_input(
            INPUT_POST,
            'post',
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY
        );
    $input_params['current_user'] = $current_user;

    $post = new Post(Post::setParams($input_params));

    if ($post->validate() && $post->insert()) {
        redirect_notice(
            "show.php?id={$post->getId()}",
            MSG_POST_REGISTER
        );
    } else {
        redirect_alert(
            'new.php',
            MSG_POST_CANT_REGISTER,
            $input_params,
            $post->get_errors()
        );
    }
    var_dump($post);
}
