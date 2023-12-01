<?php

namespace Japool\Genconsole\Console\src;

trait DelCodeHelp
{
    /**
     * 移除生成
     * @return void
     */
    public function getControllerFileList(): void
    {
        $config = config('repository');

        $list = ['controller','repository','services','request','model'];
        foreach ($list as $item)
        {
            $filePath = base_path().'/' . $config[$item];
            if((is_dir($filePath)))
            {
                $handle = opendir($filePath);

                if ($handle) {
                    while (($entry = readdir($handle)) !== FALSE) {
                        if(is_file($filePath.'/'.$entry)){
                            unlink($filePath.'/'.$entry);
                        }
                    }
                }
                $this->info('The '.$item.' folder has been deleted');
            }else{
                $this->error($filePath.'not defined');
            }
        }

    }
}
