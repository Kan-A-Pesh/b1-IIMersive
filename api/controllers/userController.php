<?php

class UserController
{

    /**
     * Create a new user
     *
     * @param string $handle - The user's handle
     * @param string|null $display_name - The user's display name
     * @param string $email - The user's email
     * @param string $password_hash - The user's password hash
     * @return void
     */
    public static function create(
        string $handle,
        string $display_name = null,
        string $email,
        string $password_hash,
    ) {
        global $MYSQL_USER_TABLE;

        // Check if user exists
        if (self::exists($handle)) {
            Response::error(409, "User already exists");
        }

        // Set default display name
        if ($display_name === null) {
            $display_name = $handle;
        }

        // Create user
        try {
            $stmt = Database::$pdo->prepare(
                "INSERT INTO $MYSQL_USER_TABLE (handle, display_name, email, password_hash)
                VALUES (:handle, :display_name, :email, :password_hash)"
            );

            $stmt->bindParam(":handle", $handle);
            $stmt->bindParam(":display_name", $display_name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password_hash", $password_hash);

            $stmt->execute();

            // Return
            return;
        } catch (PDOException $e) {
            // Send an error message
            Response::error(500, $e->getMessage());
        }
    }

    /**
     * Check if a handle is used by a user or not
     *
     * @param string $handle - The user's handle
     * @return boolean - True if the handle is used by a user, false otherwise
     */
    public static function exists(string $handle): bool
    {
        global $MYSQL_USER_TABLE;

        // Check if user exists
        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_USER_TABLE
                WHERE handle = :handle"
            );

            $stmt->bindParam(":handle", $handle);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return true if user exists
            return $result !== false;
        } catch (PDOException $e) {
            // Send an error message
            Response::error(500, $e->getMessage());
        }
    }

    /**
     * Get a user by their handle
     *
     * @param string $handle - The user's handle
     * @return User - The user
     */
    public static function get(string $handle): User
    {
        global $MYSQL_USER_TABLE;

        // Get user
        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_USER_TABLE
                WHERE handle = :handle"
            );

            $stmt->bindParam(":handle", $handle);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if user exists
            if ($result === false) {
                Response::error(404, "User not found");
            }

            // Create user
            $user = new User($result["handle"]);
            $user->display_name = $result["display_name"];
            $user->email = $result["email"];
            $user->password_hash = $result["password_hash"];
            $user->biography = $result["biography"];
            $user->avatar_path = $result["avatar_path"];
            $user->banner_path = $result["banner_path"];
            $user->created_at = new DateTime($result["created_at"]);

            // Return user
            return $user;
        } catch (PDOException $e) {
            // Send an error message
            Response::error(500, $e->getMessage());
        }
    }

    /**
     * Fetch users from the database
     * 
     * @param string $query - The query to search for (handle, display name, email)
     * @param int $limit - The maximum number of users to fetch
     * @param int $offset - The number of users to skip
     * @return User[] - The users
     */
    public static function fetch(string $query, int $limit, int $offset): array
    {
        global $MYSQL_USER_TABLE;

        if ($limit > 25) {
            $limit = 25;
        }

        // Fetch users
        try {
            $stmt = Database::$pdo->prepare(
                "SELECT * FROM $MYSQL_USER_TABLE
                WHERE handle LIKE :query
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
                $user = new User($result["handle"]);
                $user->display_name = $result["display_name"];
                $user->email = $result["email"];
                $user->password_hash = $result["password_hash"];
                $user->biography = $result["biography"];
                $user->avatar_path = $result["avatar_path"];
                $user->banner_path = $result["banner_path"];
                $user->created_at = new DateTime($result["created_at"]);

                $users[] = $user;
            }

            // Return users
            return $users;
        } catch (PDOException $e) {
            // Send an error message
            Response::error(500, $e->getMessage());
        }
    }

    /**
     * Update a user data
     * 
     * @param string $handle - The user's handle
     * @param string|null $display_name - The user's display name
     * @param string|null $email - The user's email
     * @param string|null $password_hash - The user's password hash
     * @param string|null $biography - The user's biography
     * @param string|null $avatar_path - The user's avatar path
     * @param string|null $banner_path - The user's banner path
     * @return void
     */
    public static function update(
        string $handle,
        string $display_name = null,
        string $email = null,
        string $password_hash = null,
        string $biography = null,
        string $avatar_path = null,
        string $banner_path = null,
    ) {
        global $MYSQL_USER_TABLE;

        // Check if user exists
        if (!self::exists($handle)) {
            Response::error(404, "User not found");
        }

        // Update user
        try {
            $update_text = "";

            if ($display_name !== null)
                $update_text .= "display_name = :display_name, ";

            if ($email !== null)
                $update_text .= "email = :email, ";

            if ($password_hash !== null)
                $update_text .= "password_hash = :password_hash, ";

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
                WHERE handle = :handle"
            );

            if ($display_name !== null)
                $stmt->bindParam(":display_name", $display_name);

            if ($email !== null)
                $stmt->bindParam(":email", $email);

            if ($password_hash !== null)
                $stmt->bindParam(":password_hash", $password_hash);

            if ($biography !== null)
                $stmt->bindParam(":biography", $biography);

            if ($avatar_path !== null)
                $stmt->bindParam(":avatar_path", $avatar_path);

            if ($banner_path !== null)
                $stmt->bindParam(":banner_path", $banner_path);

            $stmt->bindParam(":handle", $handle);

            $stmt->execute();

            // Return
            return;
        } catch (PDOException $e) {
            // Send an error message
            Response::error(500, $e->getMessage());
        }
    }
}
