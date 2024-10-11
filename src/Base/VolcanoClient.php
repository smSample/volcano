<?php

namespace Yihaotong\Volcano\Base;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Yihaotong\Volcano\Exception\VolcanoException;

/**
 * ak & sk方式鉴权调用
 * 适用于其他 OpenAPI
 */
class VolcanoClient
{
    protected string $ak;
    protected string $sk;
    protected string $service = "iam";
    protected string $version = "2024-01-01";
    protected string $region = "cn-beijing";
    protected string $host = "open.volcengineapi.com";
    protected string $contentType = "application/json";
    private array $query = [];
    private array $header = [];
    private array $body = [];
    private string $method = 'POST';
    private Client $client;

    public function __construct(string $accessKey, string $secretKey)
    {
        $this->ak     = $accessKey;
        $this->sk     = $secretKey;
        $this->client = new Client(['verify' => false, 'timeout' => 30]);
    }

    public function setQuery(array $query): VolcanoClient
    {
        $this->query = $query;
        return $this;
    }

    public function setHeader(array $header): VolcanoClient
    {
        $this->header = $header;
        return $this;
    }

    public function setBody(array $body): VolcanoClient
    {
        $this->body = $body;
        return $this;
    }

    public function setMethod(string $method): VolcanoClient
    {
        $this->method = $method;
        return $this;

    }

    public function setRegion(string $region): VolcanoClient
    {
        $this->region = $region;
        return $this;

    }

    public function setHost(string $host): VolcanoClient
    {
        $this->host = $host;
        return $this;

    }

    /**
     * 请求
     * @param string $action
     * @return array|string
     */
    // 第一步：创建一个  API 请求函数。签名计算的过程包含在该函数中。
    public function request(string $action)
    {
        // 第二步：创建身份证明。其中的 Service 和 Region 字段是固定的。ak 和 sk 分别代表
        // AccessKeyID 和 SecretAccessKey。同时需要初始化签名结构体。一些签名计算时需要的属性也在这里处理。
        // 初始化身份证明结构体
        $credential = [
            'accessKeyId' => $this->ak,
            'secretKeyId' => $this->sk,
            'service'     => $this->service,
            'region'      => $this->region,
        ];

        // 初始化签名结构体
        $query = array_merge($this->query, [
            'Action'  => $action,
            'Version' => $this->version
        ]);
        ksort($query);
        $requestParam = [
            // body是http请求需要的原生body
            'body'        => $this->body,
            'host'        => $this->host,
            'path'        => '/',
            'method'      => $this->method,
            'contentType' => $this->contentType,
            'date'        => gmdate('Ymd\THis\Z'),
            'query'       => $query
        ];
        // 第三步：接下来开始计算签名。在计算签名前，先准备好用于接收签算结果的 signResult 变量，并设置一些参数。
        // 初始化签名结果的结构体
        $xDate          = $requestParam['date'];
        $shortXDate     = substr($xDate, 0, 8);
        $xContentSha256 = hash('sha256', json_encode($requestParam['body']));
        $signResult     = [
            'Host'             => $requestParam['host'],
            'X-Content-Sha256' => $xContentSha256,
            'X-Date'           => $xDate,
            'Content-Type'     => $requestParam['contentType']
        ];
        // 第四步：计算 Signature 签名。
        $signedHeaderStr             = join(';', ['content-type', 'host', 'x-content-sha256', 'x-date']);
        $canonicalRequestStr         = join("\n", [
            $requestParam['method'],
            $requestParam['path'],
            http_build_query($requestParam['query']),
            join("\n", ['content-type:' . $requestParam['contentType'], 'host:' . $requestParam['host'], 'x-content-sha256:' . $xContentSha256, 'x-date:' . $xDate]),
            '',
            $signedHeaderStr,
            $xContentSha256
        ]);
        $hashedCanonicalRequest      = hash("sha256", $canonicalRequestStr);
        $credentialScope             = join('/', [$shortXDate, $credential['region'], $credential['service'], 'request']);
        $stringToSign                = join("\n", ['HMAC-SHA256', $xDate, $credentialScope, $hashedCanonicalRequest]);
        $kDate                       = hash_hmac("sha256", $shortXDate, $credential['secretKeyId'], true);
        $kRegion                     = hash_hmac("sha256", $credential['region'], $kDate, true);
        $kService                    = hash_hmac("sha256", $credential['service'], $kRegion, true);
        $kSigning                    = hash_hmac("sha256", 'request', $kService, true);
        $signature                   = hash_hmac("sha256", $stringToSign, $kSigning);
        $signResult['Authorization'] = sprintf("HMAC-SHA256 Credential=%s, SignedHeaders=%s, Signature=%s", $credential['accessKeyId'] . '/' . $credentialScope, $signedHeaderStr, $signature);
        $header                      = array_merge($this->header, $signResult);
        // 第五步：将 Signature 签名写入 HTTP Header 中，并发送 HTTP 请求。

        try {
            $queryString = http_build_query($requestParam['query']);
            $request     = new Request($this->method, 'https://' . $requestParam['host'] . $requestParam['path'] . '?' . $queryString, $header, json_encode($requestParam['body']));
            $response    = $this->client->send($request);
            return [
                'code' => 200,
                'data' => json_decode($response->getBody()->getContents(), true),
                'msg'  => '请求成功'
            ];
        } catch (GuzzleException $e) {
            // 处理请求异常
            if ($e->hasResponse()) {
                $response     = $e->getResponse();
                $responseBody = $response->getBody()->getContents();
                $responseCode = $response->getStatusCode();
                $errorData    = json_decode($responseBody, true);
                return [
                    'code' => $responseCode,
                    'data' => $errorData['ResponseMetadata'],
                    'msg'  => $errorData['ResponseMetadata']['Error']['Message'] ?? '未知错误'
                ];
            } else {
                echo $e->getMessage();
                throw new VolcanoException('请求发生错误：' . $e->getMessage(), 500);
            }
        }
    }
}
