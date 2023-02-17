<?php

class Session
{

    private string $id;
    public string $user_handle;
    public DateTime $created_at;
    public DateTime $expires_at;
    public string $ip;
    public string $user_agent;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Check if session is valid
     *
     * @return boolean - True if session is valid, false otherwise
     */
    public function isValid(): bool
    {
        return $this->expires_at > new DateTime();
    }

    /**
     * Create a new session
     *
     * @param string $user_handle - The user's handle
     * @param string $ip - The user's IP address
     * @param string $user_agent - The user's user agent
     * @return Session|int - The session or an error code
     */
    public static function create(string $user_handle, string $ip, string $user_agent): Session|int
    {
        global $MYSQL_SESSION_TABLE;

        // Create session id and expiration date
        $session_id = generate_uuid();
        $expires_at = new DateTime("+15 minutes");

        // Create session
        try {
            $stmt = Database::$pdo->prepare(
                "INSERT INTO $MYSQL_SESSION_TABLE (PK_session_id, FK_user_handle, expires_at, ip, user_agent)
                VALUES (:session_id, :user_handle, :expires_at, :ip, :user_agent)"
            );

            $stmt->bindParam(":session_id", $session_id);
            $stmt->bindParam(":user_handle", $user_handle);
            $stmt->bindParam(":expires_at", $expires_at->format("Y-m-d H:i:s"));
            $stmt->bindParam(":ip", $ip);
            $stmt->bindParam(":user_agent", $user_agent);

            $stmt->execute();

            // Return session id
            $session = new Session($session_id);
            $session->user_handle = $user_handle;
            $session->created_at = new DateTime();
            $session->expires_at = $expires_at;
            $session->ip = $ip;
            $session->user_agent = $user_agent;

            return $session;
        } catch (PDOException $e) {
            // Return error code
            return 500;
        }
    }

    /**
     * Update a session's expiration date
     * 
     * @param string $session_id - The session's id
     * @param string $ip - The user's IP address
     * @param string $user_agent - The user's user agent
     * @return null|int - Null if successful, an error code otherwise
     */
    public static function update(string $session_id, string $ip, string $user_agent): null|int
    {
        global $MYSQL_SESSION_TABLE;

        // Create session expiration date
        $expires_at = new DateTime("+15 minutes");

        // 

        // Update session
        try {
            $stmt = Database::$pdo->prepare(
                "UPDATE $MYSQL_SESSION_TABLE
                SET expires_at = :expires_at, ip = :ip, user_agent = :user_agent
                WHERE PK_session_id = :session_id"
            );

            $stmt->bindParam(":session_id", $session_id);
            $stmt->bindParam(":expires_at", $expires_at->format("Y-m-d H:i:s"));
            $stmt->bindParam(":ip", $ip);
            $stmt->bindParam(":user_agent", $user_agent);

            $stmt->execute();

            // Return
            return;
        } catch (PDOException $e) {
            // Return error code
            return 500;
        }
    }
}
