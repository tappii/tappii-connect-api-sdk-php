<?php


use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Tappii\TappiiConnectApi\V1\Tag;
use Tappii\TappiiConnectApi\V1\TappiiConnectApi;

final class TappiiConnectApiTest extends TestCase
{
    private readonly string $access_token;
    private readonly string $tag_info_id;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();
        $this->access_token = $_ENV["ACCESS_TOKEN"];
        $this->tag_info_id = $_ENV["TAG_INFO_ID"];

        parent::__construct($name, $data, $dataName);
    }

    public function testTagGetTags(): void
    {
        $tappii = new TappiiConnectApi($this->access_token);

        $tags = $tappii->getTags();
        $this->assertNotSame(0, count($tags));
    }

    public function testTagGetInfo(): void
    {
        $tappii = new TappiiConnectApi($this->access_token);

        $tag = $tappii->getTagInfo($this->tag_info_id);
        $this->assertNotNull($tag);

        $tag = $tappii->getTagInfo("xxxx");
        $this->assertNull($tag);
    }

    public function testTagGetReadHistories(): void
    {
        $tappii = new TappiiConnectApi($this->access_token);

        $histories = $tappii->getTagReadHistories($this->tag_info_id);
        $this->assertSame(0, count($histories));
    }

    public function testTagGetReadHistoriesWithRange(): void
    {
        $tappii = new TappiiConnectApi($this->access_token);

        $histories = $tappii->getTagReadHistories($this->tag_info_id, "2022-03-23T15:15", "2022-03-23T15:30");
        $this->assertSame(0, count($histories));

        $histories = $tappii->getTagReadHistories($this->tag_info_id, "2022-03-23T15:30", "2022-03-23T16:00");
        $this->assertSame(0, count($histories));
    }

    public function testTagCreateNoneTag(): void
    {
        $tappii = new TappiiConnectApi($this->access_token);

        $before = $tappii->getTags();
        $tag = $tappii->makeNoneTag("None Tag", "description");
        $after = $tappii->getTags();

        $this->assertNotNull($tag);
        $this->assertSame(count($before) + 1, count($after));
    }

    public function testTagCreateRedirectTagAndRemove(): void
    {
        $tappii = new TappiiConnectApi($this->access_token);

        $before = $tappii->getTags();
        $tag = $tappii->makeRedirectTag("Redirect Tag", "http://abehiroshi.la.coocan.jp/", true);
        $after = $tappii->getTags();

        if (!$tag instanceof Tag) {
            $this->assertNotNull(null);
        }
        $this->assertSame(count($before) + 1, count($after));

        $tappii->removeTag($tag->id);
        $after = $tappii->getTags();

        $this->assertSame(count($before), count($after));
    }
}