<?php

class User
{
    public string $handle;
    public string $display_name;
    public string $email;
    private string $password_hash;
    public string $biography;
    public string $avatar_path;
    public string $banner_path;
    public DateTime $created_at;

    public function __construct(string $handle)
    {
        $this->handle = $handle;
    }

    public function get_password_hash(): string
    {
        return $this->password_hash;
    }

    public function set_password_hash(string $password_hash): void
    {
        $this->password_hash = $password_hash;
    }

    /**
     * Create a new user
     *
     * @param string $handle - The user's handle
     * @param string $email - The user's email
     * @param string $password_hash - The user's password hash
     * @param ?string $display_name - The user's display name
     * @return User|int - The user, or an error code
     */
    public static function create(
        string $handle,
        string $email,
        string $password_hash,
        ?string $display_name = null
    ): User|int {
        global $MYSQL_USER_TABLE;

        // Check if user exists
        if (self::exists($handle)) {
            return 409;
        }

        // Set default display name
        if ($display_name === null) {
            $display_name = $handle;
        }

        // Create user
        try {
            $stmt = Database::$pdo->prepare(
                "INSERT INTO $MYSQL_USER_TABLE (PK_user_handle, display_name, email, passhash)
                VALUES (:user_handle, :display_name, :email, :passhash)"
            );

            $stmt->bindParam(":user_handle", $handle);
            $stmt->bindParam(":display_name", $display_name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":passhash", $password_hash);

            $stmt->execute();

            // Return user
            $user = new User($handle);
            $user->display_name = $display_name;
            $user->email = $email;
            $user->set_password_hash($password_hash);
            $user->biography = "";
            $user->avatar_path = "";
            $user->banner_path = "";
            $user->created_at = new DateTime();

            return $user;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Check if a handle is used by a user or not
     *
     * @param string $handle - The user's handle
     * @return bool|int - True if the user exists, false otherwise, or an error code
     */
    public static function exists(string $handle): bool|int
    {
        global $MYSQL_USER_TABLE;

        // Check if user exists
        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_USER_TABLE
                WHERE PK_user_handle = :user_handle"
            );

            $stmt->bindParam(":user_handle", $handle);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return true if user exists
            return $result !== false;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Get a user by their handle
     *
     * @param string $handle - The user's handle
     * @return User|int - The user, or an error code
     */
    public static function get(string $handle): User|int
    {
        global $MYSQL_USER_TABLE;

        // Get user
        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_USER_TABLE
                WHERE PK_user_handle = :user_handle"
            );

            $stmt->bindParam(":user_handle", $handle);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if user exists
            if ($result === false) {
                return 404;
            }

            // Create user
            $user = new User($result["PK_user_handle"]);
            $user->display_name = $result["display_name"];
            $user->email = $result["email"];
            $user->set_password_hash($result["passhash"]);
            $user->biography = $result["biography"] ?? "";
            $user->avatar_path = $result["avatar_path"] ?? "";
            $user->banner_path = $result["banner_path"] ?? "";
            $user->created_at = new DateTime($result["created_at"]);

            // Return user
            return $user;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Fetch users from the database
     * 
     * @param string $query - The query to search for (handle, display name, email)
     * @param int $limit - The maximum number of users to fetch
     * @param int $offset - The number of users to skip
     * @return array|int - An array of users or an error code
     */
    public static function list(string $query = "", int $limit = 25, int $offset = 0): array|int
    {
        global $MYSQL_USER_TABLE;

        if ($limit > 25) {
            $limit = 25;
        }

        // Fetch users
        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_USER_TABLE
                WHERE PK_user_handle LIKE :query
                OR display_name LIKE :query
                OR email LIKE :query
                LIMIT :limit OFFSET :offset"
            );

            $query = "%$query%";
            $stmt->bindParam(":query", $query);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Create users
            $users = [];
            foreach ($results as $result) {
                $user = new User($result["PK_user_handle"]);
                $user->display_name = $result["display_name"];
                $user->email = $result["email"];
                $user->set_password_hash($result["passhash"]);
                $user->biography = $result["biography"];
                $user->avatar_path = $result["avatar_path"];
                $user->banner_path = $result["banner_path"];
                $user->created_at = new DateTime($result["created_at"]);

                $users[] = $user;
            }

            // Return users
            return $users;
        } catch (PDOException $e) {
            return 500;
        }
    }

    /**
     * Update a user data
     * 
     * @param string $handle - The user's handle
     * @param ?string $display_name - The user's display name
     * @param ?string $email - The user's email
     * @param ?string $password_hash - The user's password hash
     * @param ?string $biography - The user's biography
     * @param ?string $avatar_path - The user's avatar path
     * @param ?string $banner_path - The user's banner path
     * @return ?int - Null if successful, the error code otherwise
     */
    public static function update(
        string $handle,
        ?string $display_name = null,
        ?string $email = null,
        ?string $password_hash = null,
        ?string $biography = null,
        ?string $avatar_path = null,
        ?string $banner_path = null,
    ): ?int {
        global $MYSQL_USER_TABLE;

        // Check if user exists
        if (!self::exists($handle)) {
            return 404;
        }

        // Update user
        try {
            $update_text = "";

            if ($display_name !== null)
                $update_text .= "display_name = :display_name, ";

            if ($email !== null)
                $update_text .= "email = :email, ";

            if ($password_hash !== null)
                $update_text .= "passhash = :passhash, ";

            if ($biography !== null)
                $update_text .= "biography = :biography, ";

            if ($avatar_path !== null)
                $update_text .= "avatar_path = :avatar_path, ";

            if ($banner_path !== null)
                $update_text .= "banner_path = :banner_path, ";


            $update_text = substr($update_text, 0, -2);

            $stmt = Database::$pdo->prepare(
                "UPDATE $MYSQL_USER_TABLE
                SET $update_text
                WHERE PK_user_handle = :user_handle"
            );

            if ($display_name !== null)
                $stmt->bindParam(":display_name", $display_name);

            if ($email !== null)
                $stmt->bindParam(":email", $email);

            if ($password_hash !== null)
                $stmt->bindParam(":passhash", $password_hash);

            if ($biography !== null)
                $stmt->bindParam(":biography", $biography);

            if ($avatar_path !== null)
                $stmt->bindParam(":avatar_path", $avatar_path);

            if ($banner_path !== null)
                $stmt->bindParam(":banner_path", $banner_path);

            $stmt->bindParam(":user_handle", $handle);

            $stmt->execute();

            // Return
            return null;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return 500;
        }
    }
}
