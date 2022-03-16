<?php 
require_once __DIR__ . '/../common/functions.php';

define('RAND_VALUE', '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

echo "データベースをset up しますか？ [yes] or [no]" . PHP_EOL;

$answer = trim(fgets(STDIN));

if($answer !== 'yes') exit;

try {
    $dbh = connect_db();
    $dbh->query('SET foreign_key_checks = 0');
    $sql_dir = __DIR__ . '/sql/';
    foreach(glob($sql_dir . "*.sql") as $file) {
        $sql = file_get_contents($file);
        var_dump($sql);
        $dbh->exec($sql);
    }
    echo '===テーブル削除完了===' . PHP_EOL;
    
    $image_dir = __DIR__ . '/../public/images/';
    foreach(glob($image_dir . "*") as $dir) {
        if(is_dir($dir)) {
            foreach(glob($dir . "/*") as $file) {
                if(basename($file) != 'no_image.png') {
                    unlink($file);
                }
            }
        }
    }

    echo '===画像ファイル削除完了===' . PHP_EOL;

    // usersテーブル設定
    $sql = <<<EOM
    INSERT INTO 
        users (email, password, name, profile, avatar)
    VALUES
        (:email, :password, :name, :profile, :avatar)
    EOM;
    $stmt = $dbh->prepare($sql);

    $images_dir = __DIR__ . '/images/users/'; 
    $copy_dir = __DIR__ . '/../public/images/users/';
    foreach(glob($images_dir . "*") as $i => $file) {
        $file_name = basename($file);
        $image = date('YmdHis') . '_' . $file_name;
        copy($file, $copy_dir . $image);

        $id = ++$i;
        $email = "test_" . (string) $id . "example.com";
        $name = substr(str_shuffle(RAND_VALUE), 0, 10);
        $profile = substr(str_shuffle(RAND_VALUE), 0, 50);
        $password = password_hash("password" . (string)$id, PASSWORD_DEFAULT);

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':profile', $profile, PDO::PARAM_STR);
        $stmt->bindParam(':avatar', $avatar, PDO::PARAM_STR);
        $stmt->execute();
    }
    echo '===usersテーブル set up完了 ===' . PHP_EOL;

    // postsテーブル設定
    $sql = 'SELECT id FROM users';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $user_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $sql = <<<EOM
    INSERT INTO
        posts (user_id, body, due_date, completion_date)
    VALUES
        (:user_id, :body, :due_date, :completion_date)
    EOM;
    $stmt = $dbh->prepare($sql);

    for ($i = 1; $i <= 40; $i++) {

        $user_id = $user_ids[array_rand($user_ids)];
        $body = substr(str_shuffle(RAND_VALUE), 0, 10);
        $due_date = substr(date("Ymd"), 0, 10);
        // var_dump($user_id);

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->bindParam(':completion_date', $completion_date, PDO::PARAM_STR);
        $stmt->execute();
    };
    echo '===postsテーブル set up完了===' . PHP_EOL;

    // commentsテーブル設定
    $sql = 'SELECT id FROM posts';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $post_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $sql = <<<EOM
    INSERT INTO
        comments (post_id, user_id, comment)
    VALUES
        (:post_id, :user_id, :comment)
    EOM;
    $stmt = $dbh->prepare($sql);
    for ($i = 1; $i <= 100; $i++) {
        $post_id = $post_ids[array_rand($post_ids)];
        $user_id = $user_ids[array_rand($user_ids)];
        $comment = substr(str_shuffle(RAND_VALUE), 0, 100);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->execute();
    }
    echo '=== commentsテーブル set up完了===' . PHP_EOL;

    // postsテーブル comments_countの更新
    $sql = <<<EOM
    UPDATE
        posts AS p
        INNER JOIN
        (
            SELECT 
                c.post_id,
                COUNT(c.id) AS cnt
            FROM 
                comments c
            GROUP BY c.post_id
        ) cm
        ON 
        p.id = cm.post_id
    SET 
        p.comments_count = cm.cnt
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    echo'=== comments_count set up完了===' . PHP_EOL;

    $dbh->query('SET foreign_key_checks = 1');

    echo '===データベース set up完了===' . PHP_EOL;
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}

?>