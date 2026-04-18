<?php

namespace App\Notifications\Messages;

class WhatsAppMessage
{
    public $content;
    public $mediaUrl;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public static function create(string $content = '')
    {
        return new static($content);
    }

    public function content(string $content)
    {
        $this->content = $content;
        return $this;
    }

    public function attachMediaUrl(string $url)
    {
        $this->mediaUrl = $url;
        return $this;
    }
}
