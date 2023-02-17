<?php

class Message
{
    private string $id;
    public string $author_handle;
    public string $recipient_handle;
    public string $content;
    public DateTime $created_at;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Load the lastest conversations for a user
     *
     * @param string $user_handle The user handle
     * @param integer $limit The number of conversations to return
     * @param integer $offset The offset to start from
     * @return array|int An array of Message objects (latest message in each conversation) or an error code
     */
    public static function list_latest_for_user(string $user_handle, int $limit = 25, int $offset = 0): array|int
    {
        global $MYSQL_MESSAGE_TABLE;

        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_MESSAGE_TABLE
                WHERE FK_author_handle = :user_handle OR FK_recipient_handle = :user_handle
                GROUP BY FK_author_handle, FK_recipient_handle
                ORDER BY created_at DESC
                LIMIT :limit
                OFFSET :offset"
            );

            $stmt->bindParam(":user_handle", $user_handle);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
            $stmt->execute();

            $messages = [];
            while ($row = $stmt->fetch()) {
                $message = new Message($row["PK_message_id"]);
                $message->author_handle = $row["FK_author_handle"];
                $message->recipient_handle = $row["FK_recipient_handle"];
                $message->content = $row["content"];
                $message->created_at = new DateTime($row["created_at"]);

                $messages[] = $message;
            }

            return $messages;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Load the messages in a conversation
     *
     * @param string $author_handle The author handle
     * @param string $recipient_handle The recipient handle
     * @param int $limit The number of messages to return
     * @param int $offset The offset to start from
     * @return array|int An array of Message objects or an error code
     */
    public static function list_conversation(string $author_handle, string $recipient_handle, int $limit = 25, int $offset = 0): array|int
    {
        global $MYSQL_MESSAGE_TABLE;

        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_MESSAGE_TABLE
                WHERE (FK_author_handle = :author_handle AND FK_recipient_handle = :recipient_handle)
                OR (FK_author_handle = :recipient_handle AND FK_recipient_handle = :author_handle)
                ORDER BY created_at DESC
                LIMIT :limit
                OFFSET :offset"
            );

            $stmt->bindParam(":author_handle", $author_handle);
            $stmt->bindParam(":recipient_handle", $recipient_handle);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
            $stmt->execute();

            $messages = [];
            while ($row = $stmt->fetch()) {
                $message = new Message($row["PK_message_id"]);
                $message->author_handle = $row["FK_author_handle"];
                $message->recipient_handle = $row["FK_recipient_handle"];
                $message->content = $row["content"];
                $message->created_at = new DateTime($row["created_at"]);

                $messages[] = $message;
            }

            return $messages;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Send a message
     *
     * @param string $author_handle The author handle
     * @param string $recipient_handle The recipient handle
     * @param string $content The message content
     * @return Message|int The message ID or an error code
     */
    public static function send(string $author_handle, string $recipient_handle, string $content): Message|int
    {
        global $MYSQL_MESSAGE_TABLE;

        // Get the message ID
        $message_id = generate_uuid();

        try {
            $stmt = Database::$pdo->prepare(
                "INSERT INTO $MYSQL_MESSAGE_TABLE
                (PK_message_id, FK_author_handle, FK_recipient_handle, content)
                VALUES (:message_id, :author_handle, :recipient_handle, :content)"
            );

            $stmt->bindParam(":message_id", $message_id);
            $stmt->bindParam(":author_handle", $author_handle);
            $stmt->bindParam(":recipient_handle", $recipient_handle);
            $stmt->bindParam(":content", $content);
            $stmt->execute();

            $message = new Message($message_id);
            $message->author_handle = $author_handle;
            $message->recipient_handle = $recipient_handle;
            $message->content = $content;
            $message->created_at = new DateTime();

            return $message;
        } catch (PDOException $e) {
            return 500;
        }
    }
    
}
