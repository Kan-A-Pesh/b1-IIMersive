<?php

class Post
{
    private string $id;
    public string $reply_to;
    public string $author_handle;
    public int $tag;
    public string $content;
    public array $media_paths;
    public int $likes;
    public int $comments;
    public int $views;
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
