<?php

namespace Tappii\TappiiConnectApi\V1;

class Tag
{
    public readonly string $id;
    public readonly TagType $type;
    public readonly string $name;
    public readonly ?string $description;
    public readonly string $issuance_limit;
    public readonly TagMotion $motion;
    public readonly ?array $detail; // TODO

    public function __construct(string $id, string $type, string $name, ?string $description, int $issuance_limit, string $motion, ?array $detail = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->issuance_limit = $issuance_limit;
        $this->detail = $detail;

        foreach (TagType::cases() as $case) {
            if (!is_null($case::tryFrom($type))) {
                $this->type = $case::from($type);
                break;
            }
        }

        foreach (TagMotion::cases() as $case) {
            if (!is_null($case::tryFrom($motion))) {
                $this->motion = $case::from($motion);
                break;
            }
        }
    }
}