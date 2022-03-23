<?php

namespace Tappii\TappiiConnectApi\V1;

class Touch
{
    public readonly string $tag_id;
    public readonly string $friend_id;
    public readonly string $accessed_at;

    public function __construct(string $tag_id, string $friend_id, string $accessed_at)
    {
        $this->tag_id = $tag_id;
        $this->friend_id = $friend_id;
        $this->accessed_at = $accessed_at;
    }
}