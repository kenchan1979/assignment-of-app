<?php
require_once __DIR__ . '/../../common/config.php';
require_once __DIR__ . '/../../common/functions.php';
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

    $current_user = get_login_user();
    if (empty($current_user)) {
        redirect_alert(
            '/users/log_in.php',
            MSG_PLEASE_SIGN_IN
        );
    }

    $id = $current_user['id'];
    $user = User::find($id);

    $input_params =
        filter_input(
            INPUT_POST,
            'user',
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY
        );
    $input_params['avatar_tmp'] = $_FILES['avatar'];

    $user->updateProperty($input_params);

    if ($user->updateValidate() && $user->update()) {
        redirect_notice(
            '/',
            MSG_USER_UPDATE
        );
    } else {
        redirect_alert(
            "edit.php?id={$id}",
            MSG_USER_CANT_UPDATE,
            $input_params,
            $user->get_errors()
        );
    }
}
