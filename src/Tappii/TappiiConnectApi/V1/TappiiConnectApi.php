<?php

namespace Tappii\TappiiConnectApi\V1;

class TappiiConnectApi
{
    private const endpoint = "https://tappii.link";
    private const version = "v1";
    private readonly string $access_token;

    public function __construct(string $accessToken)
    {
        $this->access_token = $accessToken;
    }

    public function getTags(): Tags
    {
        $endpoint = self::endpoint;
        $version = self::version;

        $tags = new Tags();

        $curl = curl_init("$endpoint/$version/tag");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=UTF-8",
            "Authorization: Bearer $this->access_token",
        ]);
        $json = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($json);
        if (isset($data->tags)) {
            foreach ($data->tags as $tag) {
                $tags->push(
                    new Tag($tag->id, $tag->type, $tag->name, $tag->description, $tag->issuance_limit, $tag->motion));
            }
        }

        return $tags;
    }

    public function getTagInfo(string $id): ?Tag
    {
        $endpoint = self::endpoint;
        $version = self::version;

        $curl = curl_init("$endpoint/$version/tag/$id/info");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=UTF-8",
            "Authorization: Bearer $this->access_token",
        ]);
        $json = curl_exec($curl);

        $tag = json_decode($json, true);
        if (isset($tag["id"]) && isset($tag["type"]) && isset($tag["name"]) && isset($tag["issuance_limit"]) && isset($tag["motion"])) {
            return new Tag($tag["id"], $tag["type"], $tag["name"], $tag["description"] = null, $tag["issuance_limit"], $tag["motion"], $tag["detail"] ?? null);
        }

        return null;
    }

    public function getTagReadHistories(string $id, ?string $start = null, ?string $end = null): TagReadHistories
    {
        $endpoint = self::endpoint;
        $version = self::version;

        $histories = new TagReadHistories;

        if (is_null($start) && is_null($end)) {
            $curl = curl_init("$endpoint/$version/tag/$id/read/histories");
        } else if (is_null($end)) {
            $start = urlencode($start);
            $curl = curl_init("$endpoint/$version/tag/$id/read/histories/$start");
        } else {
            $start = urlencode($start);
            $end = urlencode($end);
            $curl = curl_init("$endpoint/$version/tag/$id/read/histories/$start/$end");
        }
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=UTF-8",
            "Authorization: Bearer $this->access_token",
        ]);
        $json = curl_exec($curl);

        $data = json_decode($json);
        if (isset($data->histories)) {
            foreach ($data->histories as $history) {
                if (isset($history->tag_id) && isset($history->friend_id) && isset($history->accessed_at)) {
                    $histories->push(
                        new TagReadHistory($history->tag_id, $history->friend_id, $history->accessed_at));
                }
            }
        }

        return $histories;
    }

    public function makeNoneTag(string $name, ?string $description = null, ?int $issuance_limit = null): ?Tag
    {
        $endpoint = self::endpoint;
        $version = self::version;

        $curl = curl_init("$endpoint/$version/tag");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            "type" => TagType::NFC,
            "name" => $name,
            "description" => $description,
            "motion" => TagMotion::None,
            "issuance_limit" => $issuance_limit,
        ]));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=UTF-8",
            "Authorization: Bearer $this->access_token",
        ]);
        $json = curl_exec($curl);
        curl_close($curl);

        $tag = null;
        $data = json_decode($json);
        if (isset($data->id) && isset($data->redirect_url)) {
            $tag = new Tag(
                $data->id,
                $name,
                TagType::NFC->value,
                $description,
                $issuance_limit ?? 0,
                TagMotion::None->value);
            $tag->redirect_url = $data->redirect_url;
        }

        return $tag;
    }

    public function makeRedirectTag(string $name, string $redirect_irl, bool $external = false, ?string $description = null, ?int $issuance_limit = null): ?Tag
    {
        $endpoint = self::endpoint;
        $version = self::version;

        $curl = curl_init("$endpoint/$version/tag");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            "type" => TagType::NFC,
            "name" => $name,
            "description" => $description,
            "motion" => TagMotion::Redirect,
            "issuance_limit" => $issuance_limit,
            "redirect_url" => $redirect_irl,
            "external" => $external,
        ]));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=UTF-8",
            "Authorization: Bearer $this->access_token",
        ]);
        $json = curl_exec($curl);
        curl_close($curl);

        $tag = null;
        $data = json_decode($json);
        if (isset($data->id) && isset($data->redirect_url)) {
            $tag = new Tag(
                $data->id,
                $name,
                TagType::NFC->value,
                $description,
                $issuance_limit ?? 0,
                TagMotion::Redirect->value,
                [
                    "redirect_url" => $redirect_irl,
                    "external" => $external,
                ]);
            $tag->redirect_url = $data->redirect_url;
        }

        return $tag;
    }

    public function removeTag(string $id): bool
    {
        $endpoint = self::endpoint;
        $version = self::version;

        $curl = curl_init("$endpoint/$version/tag/$id/delete");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=UTF-8",
            "Authorization: Bearer $this->access_token",
        ]);
        $json = curl_exec($curl);
        curl_close($curl);

        return $json === "{}";
    }
}