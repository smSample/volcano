<?php

namespace Yihaotong\Volcano\Application;

use Yihaotong\Volcano\Base\VolcanoClient;

/**
 * 推理接入点
 */
class Endpoint
{
    private const LIST_ACTION = 'ListEndpoints';
    private const INFO_ACTION = 'GetEndpoint';
    private const DELETE_ACTION = 'DeleteEndpoint';
    private const START_ACTION = 'StartEndpoint';
    private const STOP_ACTION = 'StopEndpoint';
    private const CREATE_ACTION = 'CreateEndpoint';

    public function __construct(string $ak, string $sk)
    {
        $this->ak     = $ak;
        $this->sk     = $sk;
        $this->client = new VolcanoClient($this->ak, $this->sk);
    }

    /**
     * 获取推理接入点详情
     * @param int $endpointId
     * @return array|string
     */
    public function getEndpoint(int $endpointId)
    {
        return $this->client->setQuery([
            'Id' => $endpointId
        ])->request(self::INFO_ACTION);
    }

    /**
     * 获取推理接入点列表
     * @param int $pageNumber
     * @param int $pageSize
     * @return array|string
     */
    public function getEndpointList(int $pageNumber = 1, int $pageSize = 10)
    {
        return $this->client->setBody([
            'PageNumber' => $pageNumber,
            'PageSize'   => $pageSize
        ])->request(self::LIST_ACTION);
    }

    public function createEndpoint(string $name, string $modelId, string $modelVersion, string $modelType, string $modelPath, string $modelConfig, string $modelInput, string $modelOutput, string $modelPreprocess, string $modelPostprocess)
    {
        return $this->client->setBody([
            'Name'             => $name,
            'ModelId'          => $modelId,
            'ModelVersion'     => $modelVersion,
            'ModelType'        => $modelType,
            'ModelPath'        => $modelPath,
            'ModelConfig'      => $modelConfig,
            'ModelInput'       => $modelInput,
            'ModelOutput'      => $modelOutput,
            'ModelPreprocess'  => $modelPreprocess,
            'ModelPostprocess' => $modelPostprocess
        ])->request(self::CREATE_ACTION);
    }

    /**
     * 删除推理接入点
     * @param int $endpointId
     * @return array|string
     */
    public function deleteEndpoint(int $endpointId)
    {
        return $this->client->setQuery([
            'Id' => $endpointId
        ])->request(self::DELETE_ACTION);
    }

    public function updateEndpoint()
    {

    }

    /**
     * 启动推理接入点
     * @param int $endpointId
     * @return array|string
     */
    public function startEndpoint(int $endpointId)
    {
        return $this->client->setQuery([
            'Id' => $endpointId
        ])->request(self::START_ACTION);
    }

    /**
     * 停止推理接入点
     * @param int $endpointId
     * @return array|string
     */
    public function stopEndpoint(int $endpointId)
    {
        return $this->client->setQuery([
            'Id' => $endpointId
        ])->request(self::STOP_ACTION);
    }
}
