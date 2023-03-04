<?php

class Media
{

    public string $base64data;
    public string $base64meta;
    public string $extension;

    public function __construct(string $base64data, string $extension)
    {
        // $data[0] == "data:image/png;base64"
        // $data[1] == <actual base64 string>
        $data = explode(",", $base64data);

        if (count($data) !== 2)
            throw new Exception("Invalid base64 data");

        $this->base64meta = $data[0];
        $this->base64data = $data[1];

        $this->extension = $extension;
    }

    public function save(string $path): bool
    {
        $data = base64_decode($this->base64data);
        if ($data === false)
            return false;

        $file = fopen(__DIR__ . "/../../" . $path, "wb");
        if ($file === false)
            return false;

        $result = fwrite($file, $data);
        fclose($file);

        return $result !== false;
    }

    public static function delete(string $path): bool
    {
        return unlink(__DIR__ . "/../../" . $path);
    }
}
