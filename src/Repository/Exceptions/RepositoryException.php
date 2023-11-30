<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/2 18:55
 */

namespace App\Lib\Repository\Exceptions;

use App\Lib\Repository\Base\src\JsonCallBack;
use Throwable;

class RepositoryException extends \Exception
{
    use JsonCallBack;

    protected $message;
    protected $code;

    public function __construct($message = null, $code = 400, Throwable $previous = null)
    {
        $this->message = $this->getMessage();
        $this->code = $code;

        parent::__construct($message, $code, $previous);

        return $this->render();
    }

    public function render()
    {
        return $this->JsonMain(200004, $this->message);
    }
}
