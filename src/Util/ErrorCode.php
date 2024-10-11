<?php

namespace Yihaotong\Volcano\Util;

class ErrorCode
{
    /**
     * 公共错误码说明
     * @var array|string[]
     */
    public static array $codes = [
        'MissingParameter'                             => '请求缺少必要参数，请查阅 API 文档。',
        'InvalidParameter'                             => '请求包含非法参数，请查阅 API 文档。',
        'InvalidEndpoint.ClosedEndpoint'               => '推理接入点处于已被关闭或暂时不可用， 请稍后重试，或联系推理接入点管理员。',
        'AuthenticationError'                          => '请求携带的 API Key 或 AK/SK 校验未通过，请您重新检查设置的 鉴权凭证，或者查看 API 调用文档来排查问题。',
        'AccountOverdueError'                          => '当前账号欠费，如需继续调用，请前往火山交易中心进行充值。',
        'AccessDenied'                                 => '没有访问该资源的权限，请检查权限设置，或联系管理员添加白名单。',
        'InvalidEndpoint.NotFound'                     => '推理接入点不存在或者非法，请检查输入的推理接入点信息。',
        'RateLimitExceeded.FoundationModelRPMExceeded' => '请求所关联的基础模型已超过账户 RPM (Requests Per Minute) 限制, 请稍后重试。',
        'RateLimitExceeded.FoundationModelTPMExceeded' => '请求所关联的基础模型已超过账户 TPM (Tokens Per Minute) 限制, 请稍后重试。',
        'RateLimitExceeded.EndpointRPMExceeded'        => '请求所关联的推理接入点已超过 RPM (Requests Per Minute) 限制, 请稍后重试。',
        'RateLimitExceeded.EndpointTPMExceeded'        => '请求所关联的推理接入点已超过 TPM (Tokens Per Minute) 限制, 请稍后重试。',
        'QuotaExceeded'                                => '当前账号 %s 对 %s 模型的免费试用额度已消耗完毕，如需继续调用，请前往火山方舟控制台开通管理页开通对应模型服务。',
        'ModelLoadingError'                            => '模型加载中，请您稍后重试。',
        'ServerOverloaded'                             => '服务资源紧张，请您稍后重试。常出现在调用流量突增或刚开始调用长时间未使用的推理接入点。',
        'InternalServiceError'                         => '内部系统异常，请您稍后重试。',
    ];
}
