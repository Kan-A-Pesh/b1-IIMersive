<?php

class MediaSnowflake
{
    private string $id;
    private string $ext;

    /**
     * MediaSnowflake constructor.
     *
     * @param string $id 16 character hex string
     * @param string $ext 3 or 4 character string
     */
    public function __construct(string $id, string $ext)
    {
        if (strlen($id) !== 16)
            throw new Exception("Invalid media snowflake ID");

        if (strlen($ext) !== 3 && strlen($ext) !== 4)
            throw new Exception("Invalid media snowflake extension");

        $this->id = $id;
        $this->ext = $ext;
    }

    public function toString(): string
    {
        $paddedExt = str_pad($this->ext, 4, " ", STR_PAD_LEFT);
        return $this->id . $paddedExt;
    }

    public function toFile(): string
    {
        return "media/" . $this->id . "." . $this->ext;
    }

    // ============================================
    // === Getters and Setters
    // ============================================

    /**
     * Get the ID of the snowflake
     * @return string 16 character hex string
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * Get the extension of the snowflake
     * @return string 3 or 4 character string
     */
    public function getExt(): string
    {
        return $this->ext;
    }

    // ============================================
    // === Filesystem
    // ============================================

    public function exists(): bool
    {
        return file_exists(__DIR__ . "/../../../" . $this->toFile());
    }

    // ============================================
    // === Static
    // ============================================

    /**
     * Parse a MediaSnowflake from a string
     * 
     * @param string $string 20 character string
     * @return MediaSnowflake
     */
    public static function parse(string $string): MediaSnowflake
    {
        if (strlen($string) !== 20)
            throw new Exception("Invalid media snowflake string");

        $id = substr($string, 0, 16);
        $ext = substr($string, 16, 4);
        $ext = trim($ext);

        return new MediaSnowflake($id, $ext);
    }

    /**
     * Generate a new random MediaSnowflake
     * 
     * @param string $ext 3 or 4 character string
     * @return MediaSnowflake
     */
    public static function generate(string $ext): MediaSnowflake
    {
        while (true) {
            $id = bin2hex(random_bytes(8));
            $snowflake = new MediaSnowflake($id, $ext);
            if (!$snowflake->exists())
                return $snowflake;
        }
    }
}
