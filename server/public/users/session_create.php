<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../models/User.php';

session_start();

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $token = filter_input(INPUT_POST, 'token');
    if (empty($_SESSION['token']) || $_SESSION['token'] !== $token) {
        redirect_alert(
            '/',
            MSG_BAD_REQUEST
        );
    }

    $input_params =
        filter_input(
            INPUT_POST,
            'user',
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY
        );
    $input_params['avatar_tmp'] = $_FILES['avatar'];

    $user = new User(User::setParams($input_params));

    if ($user->loginValidate() && $user->logIn()) {
        redirect_notice(
            '/',
            MSG_SIGN_IN
        );
    } else {
        redirect_alert(
            'log_in.php',
            MSG_CANT_SIGN_IN,
            $input_params,
            $user->get_errors()
        );
    }
}
