<?php

namespace Yihaotong\Volcano\Base;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerInterface;
use Yihaotong\Volcano\Exception\VolcanoException;
use Yihaotong\Volcano\Util\ErrorCode;

/**
 * apiKey方式鉴权调用
 * 适用于大模型/智能体调用 API
 */
class ChatClient
{
    private string $apiKey;
    private string $host = 'ark.cn-beijing.volces.com';
    private string $contentType = "application/json";
    private string $method = 'POST';
    private array $query = [];
    private array $header = [];
    private array $body = [];
    private Client $client;
    /**
     * @var LoggerInterface|mixed|null
     */
    private $logger;

    public function __construct(string $apiKey, $logger = null)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client(['verify' => false, 'timeout' => 30]);
        $this->logger = $logger;
    }

    public function setQuery(array $query): ChatClient
    {
        $this->query = $query;
        return $this;
    }

    public function setHeader(array $header): ChatClient
    {
        if ($this->header){
            $this->header = array_merge($this->header, $body);
        }else{
            $this->header = $body;
        }
        return $this;
    }

    public function setBody(array $body): ChatClient
    {
        if ($this->body){
            $this->body = array_merge($this->body, $body);
        }else{
            $this->body = $body;
        }
        return $this;
    }

    public function setMethod(string $method): ChatClient
    {
        $this->method = $method;
        return $this;

    }

    public function setHost(string $host): ChatClient
    {
        $this->host = $host;
        return $this;

    }

    /**
     * 请求
     * @param string $action
     * @return array
     */
    public function request(string $action)
    {
        $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        $headers['Content-Type']  = $this->contentType;
        $headers                  = array_merge($headers, $this->header);

        $queryString = $this->query ? http_build_query($this->query) : '';
        $url         = $queryString ? 'https://' . $this->host . $action . '?' . $queryString : 'https://' . $this->host . $action;
        try {
            $request  = new Request($this->method, $url, $headers, json_encode($this->body));
            $response = $this->client->send($request);
            if (isset($this->body['stream']) && $this->body['stream']) {
                while (!$response->getBody()->eof()) {
                    echo $response->getBody()->read(1024);
                }
            } else {
                return [
                    'code' => 200,
                    'data' => json_decode($response->getBody()->getContents(), true),
                    'msg'  => '请求成功'
                ];
            }
        } catch (GuzzleException $e) {
            $this->setLog('error', 'ChatClient Error：' . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            // 处理请求异常
            if ($e->hasResponse()) {
                $response     = $e->getResponse();
                $responseBody = $response->getBody()->getContents();
                $responseCode = $response->getStatusCode();
                $errorData    = json_decode($responseBody, true);
                return [
                    'code' => $responseCode,
                    'data' => $errorData['error'],
                    'msg'  => ErrorCode::$codes[$errorData['error']['code']] ?? '未知错误'
                ];
            } else {
                throw new VolcanoException('请求发生错误：' . $e->getMessage(), 500);
            }
        }
    }

    private function setLog(string $level, string $message, array $context = []): void
    {
        $this->logger && $this->logger->{$level}($message, $context);
    }
}
