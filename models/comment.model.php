<?php
require_once(__DIR__ . '/../core/active-record/entity.core.php');
require_once(__DIR__ . '/../core/active-record/active-record.core.php');
require_once(__DIR__ . '/../core/active-record/filter.core.php');
require_once(__DIR__ . '/../core/active-record/order.core.php');

class Comment implements IEntity {
    private int | null $id = null;
    private int $userId, $postId;
    private string $name, $timestamp, $comment;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void {
        self::sync();
        $filter = new Filter();
        $filter->addCondition("id", $userId);
        $user = User::find($filter)[0];
        $this->userId = $userId;
        $this->name = $user->getName();
    }

    /**
     * @param int $postId
     */
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    private function createNew(): bool {
        if (!isset($this->timestamp))
            $this->timestamp = date('Y-m-d H:i:s');

        $query = ActiveRecord::getDatabaseObject()->prepare("
                INSERT INTO Comment(name, timestamp, comment, userId, postId) 
                VALUES(:name, :timestamp, :comment, :userId, :postId);
        ");

        $this->bindValuesToQuery($query, false);
        $res = $query->execute();

        $this->setId(ActiveRecord::getDatabaseObject()->lastInsertId());
        return $res;
    }

    private function updateExisting(): bool {
        if (!isset($this->timestamp))
            $this->timestamp = date('Y-m-d H:i:s');

        $query = ActiveRecord::getDatabaseObject()->prepare("
            UPDATE Comment 
            SET name = :name ,
                timestamp = :timestamp, 
                comment = :comment,
                userId = :userId,
                postId = :postId 
            WHERE id = :id;
        ");

        $this->bindValuesToQuery($query);
        return $query->execute();
    }

    public function save(): bool {
        self::sync();
        return ($this->id)? $this->updateExisting() : $this->createNew();
    }

    function delete(): bool {
        self::sync();
        if (!$this->id) return false;

        $query = ActiveRecord::getDatabaseObject()->prepare("
            DELETE FROM Comment
            WHERE id = :id;
        ");
        $query->bindParam(':id', $this->id);
        return $query->execute();
    }

    static function findById(int $id): array {
        self::sync();
        $idFilter = new Filter();
        $idFilter->addCondition("id", $id);
        return self::find($idFilter);
    }

    static function findForPost(int $postId): array {
        self::sync();
        $idFilter = new Filter();
        $idFilter->addCondition("postId", $postId);
        return self::find($idFilter);
    }

    /** @return Comment[] */
    static function findAll(): array {
        self::sync();
        return self::find(new Filter());
    }

    static function deleteAll(): bool {
        $success = true;
        foreach(Comment::findAll() as $stat) {
            $result = $stat->delete();
            if (!$result)
                $success = false;
        }
        return $success;
    }

    public static function getPageCount(int $recordsPerPage): int {
        self::sync();
        $query = "SELECT COUNT(*) FROM Comment;";
        $statement = ActiveRecord::getDatabaseObject()->query($query);
        if (!$statement)
            return 0;
        $recordCount = $statement->fetch(PDO::FETCH_NUM)[0];

        return ceil($recordCount / $recordsPerPage);
    }

    public static function getCount(): int {
        self::sync();
        $query = "SELECT COUNT(*) FROM Comment;";
        $statement = ActiveRecord::getDatabaseObject()->query($query);
        if (!$statement)
            return 0;
        return $statement->fetch(PDO::FETCH_NUM)[0];
    }

    public static function setRows($row): Comment {
        $newObject = new Comment();
        $newObject->setId($row["id"]);
        $newObject->setName($row["name"]);
        $newObject->setTimestamp($row["timestamp"]);
        $newObject->setComment($row['comment']);
        $newObject->setUserId($row['userId']);
        $newObject->setPostId($row['postId']);
        return $newObject;
    }

    private function bindValuesToQuery($query, bool $includeId = true): void {
        if ($includeId)
            $query->bindParam(":id", $this->id);
        $query->bindParam(":name", $this->name);
        $query->bindParam(":timestamp", $this->timestamp);
        $query->bindParam(":comment", $this->comment);
        $query->bindParam(":userId", $this->userId);
        $query->bindParam(":postId", $this->postId);
    }

    static function findAllForPage(int $page, int $recordsPerPage): array {
        self::sync();
        $filter = new Filter();
        $filter->setLimit(new Limit($page, $recordsPerPage));
        return self::find($filter);
    }

    public static function sync() {
        User::sync();
        BlogMessage::sync();
        $query = ActiveRecord::getDatabaseObject()->prepare("
            CREATE TABLE IF NOT EXISTS Comment(
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(500) NOT NULL,
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                comment VARCHAR(500) NOT NULL,
                postId INTEGER NOT NULL,
                userId INTEGER NOT NULL,
                FOREIGN KEY (postId) REFERENCES BlogMessage(id) ON DELETE CASCADE ,
                FOREIGN KEY (userId) REFERENCES User(id) ON DELETE CASCADE
            );
        ");
        $query->execute();
    }

    /** @return Comment | Comment[] */
    static function find(Filter $filter, bool $fetchAll = true): Comment | array {
        return ActiveRecord::find("Comment",
            "Comment::sync",
            "Comment::setRows",
            $filter,
            $fetchAll,
            new DescendingOrder("timestamp")
        );
    }
}