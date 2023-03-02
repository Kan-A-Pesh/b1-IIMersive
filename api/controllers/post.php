<?php

class Post
{
    public string $id;
    public ?string $reply_to;
    public string $author_handle;
    public int $tag;
    public string $content;
    public ?array $media_paths;
    public int $likes;
    public int $comments;
    public int $views;
    public DateTime $created_at;

    public function __construct(string $id)
    {
        global $MYSQL_POST_TABLE, $MYSQL_LIKE_TABLE;

        $this->id = $id;

        // Get the likes
        $stmt = Database::$pdo->prepare(
            "SELECT COUNT(*) FROM $MYSQL_LIKE_TABLE
            WHERE PFK_post_id = :id"
        );

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $this->likes = $stmt->fetchColumn();

        // Get the comments
        $stmt = Database::$pdo->prepare(
            "SELECT COUNT(*) FROM $MYSQL_POST_TABLE
            WHERE FK_reply_to = :id"
        );

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $this->comments = $stmt->fetchColumn();
    }

    /**
     * Convert the media paths string to an array of paths
     *
     * @param ?string $media_list_paths The media paths string or null
     * @return ?array The media paths array
     */
    private static function get_media_paths(?string $media_list_paths): ?array
    {
        if ($media_list_paths === null)
            return null;

        $media_list_count = strlen($media_list_paths) / 20;

        $media_paths = [];
        for ($i = 0; $i < $media_list_count; $i++) {
            $media_paths[] = substr($media_list_paths, $i * 20, 20);
        }

        return $media_paths;
    }

    /**
     * Get a post by its ID
     * 
     * @param string $id The post ID
     * @return Post|int The post or an error code
     */
    public static function get(string $id): Post|int
    {
        global $MYSQL_POST_TABLE;

        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_POST_TABLE
                WHERE PK_post_id = :id"
            );

            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $row = $stmt->fetch();

            if ($row === false)
                return 404;

            $post = new Post($row["PK_post_id"]);
            $post->reply_to = $row["FK_reply_to"];
            $post->author_handle = $row["FK_author_handle"];
            $post->tag = $row["tag"];
            $post->content = $row["content"];
            $post->views = $row["views_count"];
            $post->created_at = new DateTime($row["created_at"]);
            $post->media_paths = self::get_media_paths($row["media_list_paths"]);

            return $post;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Get all posts that match the given criteria (and add a view to each post)
     *
     * @param ?string $query The search query (null for no query)
     * @param ?User $fromUsers The user to get posts from (null for all users)
     * @param ?User $excludeUsers The user to exclude posts from (null for no users)
     * @param ?Post $replyTo The post to get replies to (null for no replies)
     * @param ?boolean $hasMedia Whether to get posts with/without media (null for all posts)
     * @param integer $limit The maximum number of posts to get
     * @param integer $offset The offset of the posts to get
     * @return Post[]|integer The posts or an error code
     */
    public static function get_all(
        ?string $query = null,
        ?User $fromUsers = null,
        ?User $excludeUsers = null,
        ?string $replyTo = null,
        ?bool $hasMedia = null,
        int $limit = 25,
        int $offset = 0
    ): array|int {
        global $MYSQL_POST_TABLE;

        $query = $query ? "%$query%" : null;
        $hasMedia = $hasMedia !== null ? ($hasMedia ? "NOT" : "") : null;

        $fromUsersQuery = $fromUsers ? $fromUsers->handle : null;
        $excludeUsersQuery = $excludeUsers ? $excludeUsers->handle : null;

        if ($replyTo === "none") {
            $replyToQuery = "AND FK_reply_to IS NULL";
        } else if ($replyTo === "any") {
            $replyToQuery = "AND FK_reply_to IS NOT NULL";
        } else {
            $replyToQuery = $replyTo ?
                "AND FK_reply_to = $replyTo" :
                null;
        }

        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_POST_TABLE
                WHERE (:query IS NULL OR content LIKE :query)
                AND (:fromUsers IS NULL OR FK_author_handle = :fromUsers)
                AND (:excludeUsers IS NULL OR FK_author_handle != :excludeUsers)
                $replyToQuery
                AND (:hasMedia IS NULL OR media_list_paths $hasMedia LIKE '')
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset"
            );

            $stmt->bindParam(":query", $query);
            $stmt->bindParam(":fromUsers", $fromUsersQuery);
            $stmt->bindParam(":excludeUsers", $excludeUsersQuery);
            $stmt->bindParam(":hasMedia", $hasMedia);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
            $stmt->execute();

            $posts = [];
            while ($row = $stmt->fetch()) {
                $post = new Post($row["PK_post_id"]);
                $post->reply_to = $row["FK_reply_to"];
                $post->author_handle = $row["FK_author_handle"];
                $post->tag = $row["tag"];
                $post->content = $row["content"];
                $post->views = $row["views_count"];
                $post->created_at = new DateTime($row["created_at"]);
                $post->media_paths = self::get_media_paths($row["media_list_paths"]);

                $posts[] = $post;
            }

            if (count($posts) > 0) {
                // Add views to posts
                $stmt = Database::$pdo->prepare(
                    "UPDATE $MYSQL_POST_TABLE
                    SET views_count = views_count + 1
                    WHERE PK_post_id IN (" . implode(",", array_map(fn ($post) => "'$post->id'", $posts)) . ")"
                );

                $stmt->execute();
            }

            return $posts;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Create a new post and return it
     *
     * @param string $author_handle The author's handle
     * @param integer $tag The post tag
     * @param string $content The post content
     * @param array $media_paths The media paths
     * @param ?string $reply_to The post ID to reply to (null for no reply)
     * @return Post|integer The post or an error code
     */
    public static function create(
        string $author_handle,
        int $tag,
        string $content,
        array $media_paths,
        ?string $reply_to = null
    ): Post|int {
        global $MYSQL_POST_TABLE;

        $post_id = generate_uuid();
        $media_list_paths = implode("", $media_paths);

        try {
            $stmt = Database::$pdo->prepare(
                "INSERT INTO $MYSQL_POST_TABLE
                (PK_post_id, FK_author_handle, tag, content, media_list_paths, FK_reply_to)
                VALUES (:post_id, :author_handle, :tag, :content, :media_list_paths, :reply_to)"
            );

            $stmt->bindParam(":post_id", $post_id);
            $stmt->bindParam(":author_handle", $author_handle);
            $stmt->bindParam(":tag", $tag);
            $stmt->bindParam(":content", $content);
            $stmt->bindParam(":media_list_paths", $media_list_paths);
            $stmt->bindParam(":reply_to", $reply_to);
            $stmt->execute();

            return self::get(Database::$pdo->lastInsertId());
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Update a post and return it
     *
     * @param string $id The post ID
     * @return ?integer Null or an error code
     */
    public static function delete(string $id): ?int
    {
        global $MYSQL_POST_TABLE;

        try {
            $stmt = Database::$pdo->prepare(
                "DELETE FROM $MYSQL_POST_TABLE
                WHERE PK_post_id = :id"
            );

            $stmt->bindParam(":id", $id);
            $stmt->execute();

            return null;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Like a post
     * 
     * @param string $post_id The post ID
     * @param string $user_handle The user handle
     * @return ?int Null or an error code
     */
    public static function like_post(string $post_id, string $user_handle): ?int
    {
        global $MYSQL_LIKE_TABLE;

        try {
            $stmt = Database::$pdo->prepare(
                "INSERT INTO $MYSQL_LIKE_TABLE
                (PFK_post_id, PFK_user_handle)
                VALUES (:post_id, :user_handle)"
            );

            $stmt->bindParam(":post_id", $post_id);
            $stmt->bindParam(":user_handle", $user_handle);
            $stmt->execute();

            return null;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Unlike a post
     *
     * @param string $post_id The post ID
     * @param string $user_handle The user handle
     * @return ?integer Null or an error code
     */
    public static function unlike_post(string $post_id, string $user_handle): ?int
    {
        global $MYSQL_LIKE_TABLE;

        try {
            $stmt = Database::$pdo->prepare(
                "DELETE FROM $MYSQL_LIKE_TABLE
                WHERE PFK_post_id = :post_id AND PFK_user_handle = :user_handle"
            );

            $stmt->bindParam(":post_id", $post_id);
            $stmt->bindParam(":user_handle", $user_handle);
            $stmt->execute();

            return null;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Check if a post is liked by a user
     *
     * @param string $post_id The post ID
     * @param string $user_handle The user handle
     * @return bool|int True if liked, false if not, or an error code
     */
    public static function is_liked(string $post_id, string $user_handle): bool
    {
        global $MYSQL_LIKE_TABLE;

        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_LIKE_TABLE
                WHERE PFK_post_id = :post_id AND PFK_user_handle = :user_handle"
            );

            $stmt->bindParam(":post_id", $post_id);
            $stmt->bindParam(":user_handle", $user_handle);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return 500;
        }
    }
}
