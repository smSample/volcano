<?php

declare(strict_types=1);

namespace Yihaotong\Volcano\Application;

use Yihaotong\Volcano\Base\ChatClient;

/**
 * 会话.
 */
class Conversation
{
    protected const CHAT_PATH = '/api/v3/chat/completions';
    protected const BOTS_CHAT_PATH = '/api/v3/bots/chat/completions';
    protected const TOKENIZATION_PATH = '/api/v3/tokenization';
    private ChatClient $client;

    public function __construct(string $apiKey)
    {
        $this->client = new ChatClient($apiKey);
    }

    public function setModel(string $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * 大模型会话.
     * @param string $message 会话消息
     * @param bool $stream 是否以流式接口的形式返回数据，默认false
     * @return array|string
     */
    public function chat(string $message = '', bool $stream = false)
    {
        $client = $this->client->setBody([
            'model'    => $this->model,
            'stream'   => $stream,
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => $message
                ]
            ]
        ]);
        $stream && $client->setBody([
            'stream_options' => [
                'include_usage'=>true
            ]
        ]);
        return $client->request(self::CHAT_PATH);
    }

    /**
     * 智能体会话.
     * @param string $message 会话消息
     * @param bool $stream 是否以流式接口的形式返回数据，默认false
     * @return array|string
     */
    public function botsChat(string $message = '', bool $stream = false)
    {
        $client = $this->client->setBody([
            'model'    => $this->model,
            'stream'   => $stream,
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => $message
                ]
            ]
        ]);
        $stream && $client->setBody([
            'stream_options' => [
                'include_usage'=>true
            ]
        ]);
        return $client->request(self::BOTS_CHAT_PATH);
    }


    /**
     * 获取会话所需token数
     * @return array
     */
    public function getTokens($message)
    {
        return $this->client->setBody([
            'model' => $this->model,
            'text'  => $message
        ])->request(self::TOKENIZATION_PATH);

    }
}
