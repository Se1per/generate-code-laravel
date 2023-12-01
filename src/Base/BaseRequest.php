<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/9/26 9:29
 */
namespace Japool\Genconsole\Base;

use Japool\Genconsole\Base\src\JsonCallBack;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseRequest extends FormRequest
{
    use JsonCallBack;

    /**
     * 定义验证规则
     * @return array
     */
    public function rules(): array
    {
        $rule_action = 'getRulesBy' . $this->route()->getActionMethod();

        if (method_exists($this, $rule_action))
            return $this->$rule_action();

        return $this->getDefaultRules();
    }

    /**
     * 默认 验证规则
     * @return array
     */
    protected function getDefaultRules(): array
    {
        return [];
    }

    /**
     * 验证消息通过json抛出（api开发时用到）
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->JsonMain(200001, $validator->errors()->first())
        );
    }
}
