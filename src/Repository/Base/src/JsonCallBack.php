<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/2/17
 * Time: 16:29
 */

namespace App\Lib\Repository\Base\src;

trait JsonCallBack
{
    protected $code;

    protected $msg;

    protected $data = null;

    protected $count = null;

    protected $custom = null;

    public function JsonMain(int $code, string $msg = null, $data = null, $count = null , $custom = null
    ): \Illuminate\Http\JsonResponse|string
    {
        $this->code = $code;

        try {
            $this->msg = $msg !== null ? config('repository.code')[$code] . ':' . $msg : config('returnCode.code')[$code];
        } catch (\Exception $e) {
            return '无法匹配到规定的实例返回代码,请查询config.returnCode'.$e->getMessage();
        }

        $this->data = $data !== null ? $data : null;

        $this->count = $count !== null ? $count : null;

        $this->custom = $custom !== null ? $custom : null;

        switch ($this->code)
        {
            case 200000:
                $status = 'success';
                break;
            case 200004:
                $status = 'error';
                break;
            default:
            case 200001:
            case 200002:
                $status = 'warning';
                break;
        }

        $layout = ($this->data === null ? [
            'status' => $status,
            'code' => $this->code,
            'message' => $this->msg,
        ] : (($this->count === null ) ? [
            'status' => $status,
            'code' => $this->code,
            'message' => $this->msg,
            'data' => $this->data,
        ] : [
            'status' => $status,
            'code' => $this->code,
            'message' => $this->msg,
            'data' => $this->data,
            'count' => $this->count,
        ]));

        if(!empty($this->custom)){
            $layout['custom'] = $this->custom;
        }

        return response()->json($layout);

    }
}
