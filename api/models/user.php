<?php

class User
{
    private string $handle;
    public string $display_name;
    public string $email;
    public string $password_hash;
    public string $biography;
    public string $avatar_path;
    public string $banner_path;
    public DateTime $created_at;

    public function __construct(string $handle)
    {
        $this->handle = $handle;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }
}
