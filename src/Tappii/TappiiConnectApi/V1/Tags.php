<?php

namespace Tappii\TappiiConnectApi\V1;

use Countable;
use Iterator;

class Tags implements Iterator, Countable
{
    private int $position = 0;
    private array $tags = [];

    public function push(Tag $tag): void
    {
        $this->tags[] = $tag;
    }

    public function current(): Tag
    {
        return $this->tags[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->tags);
    }
}