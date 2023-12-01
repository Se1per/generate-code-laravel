<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/2/6
 * Time: 16:31
 */

namespace Japool\Genconsole\Controller\src;

use Illuminate\Support\Facades\Validator;

trait Validates
{
    public function checkValidates(array $data,array $rules,array $message): array
    {
        $validator = Validator::make($data,$rules,$message);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return [false,$errors->first()];
        }

        return [true,'验证成功'];
    }
}
