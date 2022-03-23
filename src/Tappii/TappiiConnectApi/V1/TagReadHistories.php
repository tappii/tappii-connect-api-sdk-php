<?php

namespace Tappii\TappiiConnectApi\V1;

use Countable;
use Iterator;

class TagReadHistories implements Iterator, Countable
{
    private int $position = 0;
    private array $tag_read_histories = [];

    public function push(TagReadHistory $history): void
    {
        $this->tag_read_histories[] = $history;
    }

    public function current(): Tag
    {
        return $this->tag_read_histories[$this->position];
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
        return count($this->tag_read_histories);
    }
}