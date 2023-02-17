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
}
