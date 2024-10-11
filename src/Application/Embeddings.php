<?php

namespace Yihaotong\Volcano\Application;

use Yihaotong\Volcano\Base\ChatClient;

/**
 * 语义向量化模型
 */
class Embeddings
{
    private ChatClient $client;
    private string $model;
    protected const EMBEDDINGS_PATH = '/api/v3/chat/completions';

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
     * 获取文本的embeddings
     * @param array $input
     * @return array
     */
    public function getEmbeddings(array $input)
    {
        return $this->client->setBody([
            'model' => $this->model,
            'input' => $input
        ])->request(self::EMBEDDINGS_PATH);
    }
}
