<?php

class Notification
{
    private string $id;
    public string $recipient_handle;
    public string $icon_path;
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
     * Create a new notification
     * 
     * @param string $recipient_handle The recipient handle
     * @param string $icon_path The path to the icon
     * @param string $content The content of the notification
     * @return Notification|int The created notification or an error code
     */
    public static function create(string $recipient_handle, string $icon_path, string $content): Notification|int
    {
        global $MYSQL_NOTIFICATION_TABLE;

        // Get the notification ID
        $notification_id = generate_uuid();

        try {
            $stmt = Database::$pdo->prepare(
                "INSERT INTO $MYSQL_NOTIFICATION_TABLE
                (PK_notification_id, FK_recipient_handle, icon_path, content)
                VALUES (:notification_id, :recipient_handle, :icon_path, :content)"
            );

            $stmt->bindParam(":notification_id", $notification_id);
            $stmt->bindParam(":recipient_handle", $recipient_handle);
            $stmt->bindParam(":icon_path", $icon_path);
            $stmt->bindParam(":content", $content);
            $stmt->execute();

            $notification = new Notification($notification_id);
            $notification->recipient_handle = $recipient_handle;
            $notification->icon_path = $icon_path;
            $notification->content = $content;
            $notification->created_at = new DateTime();

            return $notification;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Get all notifications for a user and delete them
     * 
     * @param string $recipient_handle The recipient handle
     * @return array|int The notifications or an error code
     */
    public static function pop_all(string $recipient_handle): array|int
    {
        global $MYSQL_NOTIFICATION_TABLE;

        try {
            // Get the notifications
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_NOTIFICATION_TABLE
                WHERE FK_recipient_handle = :recipient_handle
                ORDER BY created_at DESC"
            );

            $stmt->bindParam(":recipient_handle", $recipient_handle);
            $stmt->execute();

            $notifications = [];
            while ($row = $stmt->fetch()) {
                $notification = new Notification($row["PK_notification_id"]);
                $notification->recipient_handle = $row["FK_recipient_handle"];
                $notification->icon_path = $row["icon_path"];
                $notification->content = $row["content"];
                $notification->created_at = new DateTime($row["created_at"]);

                $notifications[] = $notification;
            }

            // Delete the notifications
            $stmt = Database::$pdo->prepare(
                "DELETE FROM $MYSQL_NOTIFICATION_TABLE
                WHERE FK_recipient_handle = :recipient_handle"
            );

            $stmt->bindParam(":recipient_handle", $recipient_handle);
            $stmt->execute();

            return $notifications;
        } catch (PDOException $e) {
            return 500;
        }
    }
}
