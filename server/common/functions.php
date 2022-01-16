<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/Topic.php';

function connect_db()
{
    try {
        return new PDO(DSN, USER, PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        echo 'システムエラーが発生しました';
        error_log($e->getMessage());
        exit;
    }
}

function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function create_pager($page, $total_count)
{
    $html = "";
    if ($total_count > Post::PER_PAGE) {
        $c_href = "";

        $max_page = ceil($total_count / Post::PER_PAGE);
        $prev = $page - 1;
        $next = min($page + 1, $max_page);

        $html = "<nav class=\"pager\">\n";
        $html .= "<div class=\"pagination\">\n";

        if ($page > 1) {
            $html .= "<a href=\"index.php?page=" . $prev . $c_href . "\" class=\"page-prev\">&lt; PREV</a>\n";
        }

        if ($page > 2) {
            $html .= "<a href=\"index.php?page=1" . $c_href . "\">1</a>\n";
        }

        if ($page > 3) {
            $html .= "<span>...</span>\n";
        }

        if ($page > 1) {
            $html .= "<a href=\"index.php?page=" . $prev . $c_href . "\">" . $prev . "</a>\n";
        }

        $html .= "<a href=\"index.php?page=" . $page . $c_href . "\" class=\"current-page\">" . $page . "</a>\n";

        if ($page < $max_page) {
            $html .= "<a href=\"index.php?page=" . $next . $c_href . "\">" . $next . "</a>\n";
        }

        if ($max_page - $page > 2) {
            $html .= "<span>...</span>\n";
        }

        if ($max_page - $page > 1) {
            $html .= "<a href=\"index.php?page=" . $max_page . $c_href . "\">" . $max_page . "</a>\n";
        }

        if ($page < $max_page) {
            $html .= "<a href=\"index.php?page=" . $next . $c_href . "\" class=\"page-next\">NEXT &gt;</a>\n";
        }

        $html .= "</div>\n";
        $html .= "</nav>\n";
    }
    return $html;
}

function generate_token()
{
    if (empty($_SESSION['token'])) {
        $token = bin2hex(random_bytes(24));
        $_SESSION['token'] = $token;
    } else {
        $token = $_SESSION['token'];
    }
    return $token;
}

function redirect_alert($url, $alert, $post_data = [], $errors = [])
{
    $_SESSION['alert'] = $alert;
    $_SESSION['post_data'] = $post_data;
    $_SESSION['errors'] = $errors;
    header("Location: {$url}");
    exit;
}

function redirect_notice($url, $notice)
{
    $_SESSION['notice'] = $notice;
    header("Location: {$url}");
    exit;
}

function get_alert()
{
    $alert = $_SESSION['alert'];
    unset($_SESSION['alert']);
    return $alert;
}

function get_notice()
{
    $notice = $_SESSION['notice'];
    unset($_SESSION['notice']);
    return $notice;
}

function get_errors()
{
    $errors = $_SESSION['errors'] ?? [];
    unset($_SESSION['errors']);
    return $errors;
}

function get_post_data()
{
    $post_data = $_SESSION['post_data'];
    unset($_SESSION['post_data']);
    return $post_data;
}

function get_login_user()
{
    if (is_array($_SESSION['current_user'])) {
        return $_SESSION['current_user'];
    } else {
        return [];
    }
}

function create_err_msg($errors)
{
    $err_msg = "<div class=\"error-message-area\">\n";
    $err_msg .= "<ul>\n";

    foreach ((array)$errors as $error) {
        $err_msg .= "<li>" . h($error) . "</li>\n";
    }

    $err_msg .= "</ul>\n";
    $err_msg .= "</div>\n";
    return $err_msg;
}
