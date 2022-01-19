<?php
require_once __DIR__ . '/../common/config.php';
require_once __DIR__ . '/../common/functions.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Comments.php';

class Post
{
    public const PER_PAGE = 8;

    private $id;
    private $user_id;
    private $body;
    private $due_date;
    private $completion_date;
    private $comments_count;
    private $created_at;
    private $updated_at;
    private $user;
    private $comments;
    private $errors = [];

    public function __construct($params)
    {
        $this->id = $params['id'];
        $this->user_id = $params['user_id'];
        $this->body = $params['body'];
        $this->due_date = $params['due_date'];
        $this->completion_date = $params['completion_date'];
        $this->comments_count = $params['comments_count'];
        $this->created_at = $params['created_at'];
        $this->updated_at = $params['updated_at'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBody()
    {
        return $this->body;
    }
    public function getDueDate()
    {
        return $this->due_date;
    }
    public function getCompletionDate() {
        return $this->completion_date;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getCommentsCount()
    {
        return $this->comments_count;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function get_errors()
    {
        return $this->errors;
    }

    public function findCommentsWithUser()
    {
        $this->findComments();

        $this->findCommentUsers();

        return $this->comments;
    }

    public function validate()
    {
        $this->bodyValidate();
        $this->duedateValidate();

        return $this->errors ? false : true;
    }

    public function insert()
    {
        try {
            $dbh = connect_db();
            $dbh->beginTransaction();

            $this->insertMe($dbh);

            $dbh->commit();
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            $dbh->rollBack();
            return false;
        }
    }


    public function updateProperty($params)
    {
        $this->updateMyProperty($params);
    }

    public function update()
    {
        try {
            $dbh = connect_db();
            $dbh->beginTransaction();

            $this->updateMe($dbh);

            $dbh->commit();
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());

            $dbh->rollBack();
            return false;
        }
    }

    public function update_status_to_done()
    {
        try {
            $dbh = connect_db();
            $dbh->beginTransaction();

            $this->update_status_to_doneMe($dbh);

            $dbh->commit();

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $dbh->rollBack();
            return false;
        }
    }

    public function not_yet()
    {
        try {
            $dbh = connect_db();
            $dbh->beginTransaction();

            $this->not_yetMe($dbh);

            $dbh->commit();

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $dbh->rollBack();
            return false;
        }
    }

    public function delete()
    {
        try {
            $dbh = connect_db();
            $dbh->beginTransaction();

            $this->deleteMe($dbh);

            $dbh->commit();

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $dbh->rollBack();
            return false;
        }
    }

    private function findUser()
    {
        $this->user = User::find($this->user_id);
    }

    private function findComments()
    {
        $this->comments = Comment::findByPostId($this->id);
    }

    private function findCommentUsers()
    {
        if (empty($this->comments)) {
            return;
        }

        $ids = array_map(
            function ($comment) {
                return $comment->getUserId();
            },
            $this->comments
        );

        $find_user_ids = array_values(array_unique($ids));

        $users = User::findByIdsAsArray($find_user_ids);

        $user_ids = array_column($users, 'id');

        foreach ($this->comments as $c) {
            $comment_user = $users[array_search(
                $c->getUserId(),
                $user_ids
            )];
            $c->setUser(new User($comment_user));
        }
    }

    private function bodyValidate()
    {
        if ($this->body == '') {
            $this->errors['body'][] = MSG_BODY_REQUIRED;
        }
        if (mb_strlen($this->body) > 50) {
            $this->errors['body'][] = MSG_PLAN_MAX;
        }
    }

    private function duedateValidate()
    {
        if ($this->due_date == '') {
            $this->errors['due_date'][] = MSG_DUE_DATE_REQUIRED;
        }
    }

    private function insertMe($dbh)
    {
        $sql = <<<EOM
        INSERT INTO
            posts (user_id, body, due_date, completion_date)
        VALUES
            (:user_id, :body, :due_date, :completion_date)
        EOM;

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':body', $this->body, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $this->due_date, PDO::PARAM_STR);
        $stmt->bindParam(':completion_date', $this->completion_date, PDO::PARAM_STR);
        $stmt->execute();

        $this->id = $dbh->lastInsertId();
    }

    private function updateMe($dbh)
    {
        $sql = <<<EOM
        UPDATE
            posts
        SET
            body = :body,
            due_date = :due_date,
            completion_date = :completion_date
        WHERE
            id = :id
        EOM;

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':body', $this->body, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $this->due_date, PDO::PARAM_STR);
        $stmt->bindParam(':completion_date', $this->completion_date, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function update_status_to_doneMe($dbh)
    {
        $sql = 'UPDATE posts SET completion_date = CURRENT_TIMESTAMP WHERE id = :id';

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function not_yetMe($dbh) {
        $dbh = connect_db();

        $sql = <<<EOM
        UPDATE
            posts
        SET
            completion_date = null
        WHERE
            id = :id
        EOM;

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function deleteMe($dbh)
    {
        $sql = 'DELETE FROM posts WHERE id = :id';

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function updateMyProperty($params)
    {
        $this->body = $params['body'];
        $this->due_date = $params['due_date'];
        $this->completion_date = $params['completion_date'];
    }

    public static function findWithUser($id)
    {
        $post = self::findById($id);

        if ($post) {
            $post->findUser();
        }

        return $post;
    }

    public static function findIndexView($page)
    {
        $posts = self::findPostIndexView($page);

        $find_user_ids = self::getPostUserIds($posts);

        $users = User::findByIdsAsArray($find_user_ids);

        self::setPostUsers($posts, $users);

        return $posts;
    }

    public static function findIndexViewCount()
    {
        return self::findPostIndexViewCount();
    }

    public static function setParams($input_params)
    {
        return self::setInputParams($input_params);
    }

    public static function find($id)
    {
        return self::findById($id);
    }

    public static function updatePostCommentsCountByIds($dbh, $ids)
    {
        return self::updateCommentCountByIds($dbh, $ids);
    }

    private static function findById($id)
    {
        $instance = [];
        try {
            $dbh = connect_db();

            $sql = 'SELECT * FROM posts WHERE id = :id';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($post) {

                $instance = new static($post);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        return $instance;
    }

    private static function findPostIndexView($page)
    {
        $instances = [];
        try {
            $dbh = connect_db();

            $sql = 'SELECT p.* FROM posts p ORDER BY due_date DESC, 
            completion_date DESC';
            $sql .= ' LIMIT :par_page OFFSET :offset_count';
            $stmt = $dbh->prepare($sql);

            $par_page = self::PER_PAGE;
            $stmt->bindParam(':par_page', $par_page, PDO::PARAM_INT);
            $offset = ($page - 1) * self::PER_PAGE;
            $stmt->bindParam(':offset_count', $offset, PDO::PARAM_INT);

            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($posts as $p) {
                $instances[] = new static($p);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        return $instances;
    }

    private static function getPostUserIds($posts)
    {
        $user_ids = array_map(
            function ($post) {
                return $post->user_id;
            },
            $posts
        );

        return array_values(array_unique($user_ids));
    }

    private static function setPostUsers($posts, $users)
    {
        $user_ids = array_column($users, 'id');
        foreach ($posts as $p) {
            $post_user = $users[array_search(
                $p->user_id,
                $user_ids
            )];
            $p->user = new User($post_user);
        }
    }

    private static function findPostIndexViewCount()
    {
        $count = 0;
        try {
            $dbh = connect_db();

            $sql = <<<EOM
            SELECT
                COUNT(*) AS count
            FROM
                posts p

            EOM;

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $post['count'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        return $count;
    }

    private static function setInputParams($input_params)
    {
        $params = [];
        $params['user_id'] = $input_params['current_user']['id'];
        $params['body'] = $input_params['body'];
        $params['due_date'] = $input_params['due_date'];
        $params['completion_date'] = $input_params['completion_date'];

        return $params;
    }

    private static function updateCommentCountByIds($dbh, $ids)
    {
        if (empty($ids)) {
            return;
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $where_in = substr(str_repeat(',?', count($ids)), 1);

        $sql = '';
        $sql .= 'UPDATE ';
        $sql .= '    posts AS p ';
        $sql .= 'LEFT JOIN ';
        $sql .= '   ( ';
        $sql .= '    SELECT ';
        $sql .= '        c.post_id, ';
        $sql .= '        COUNT(c.id) AS cnt ';
        $sql .= '    FROM ';
        $sql .= '        comments c ';
        $sql .= '    WHERE ';
        $sql .= '        c.post_id IN (' . $where_in . ') ';
        $sql .= '    GROUP BY c.post_id ';
        $sql .= '   ) cm ';
        $sql .= 'ON ';
        $sql .= '    p.id = cm.post_id ';
        $sql .= 'SET ';
        $sql .= '    p.comments_count = COALESCE(cm.cnt, 0) ';
        $sql .= 'WHERE ';
        $sql .= '    p.id IN (' . $where_in . ')';

        $stmt = $dbh->prepare($sql);
        $stmt->execute(array_merge($ids, $ids));
    }

}