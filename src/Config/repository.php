<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default name
    |--------------------------------------------------------------------------
    | project name
    |
    */
    'name' => env('APP_NAME', 'Laravel'),

    'console' => 'App\\Lib\\Repository\\Console',
    'apiDoc' => 'App\\Http\\ApiDocDefinitions',
    'controller' => 'App\\Http\\Controllers\\Api',
    'repository' => 'App\\Http\\Repository',
    'services' => 'App\\Http\\Services',
    'request' => 'App\\Http\\Request',
    'model' => 'App\\Models',
    'route' => [
        'patch' => base_path('routes'),
        'route_api' => 'api.php',
        'api_url'=>'/api/'.env('APP_NAME')
    ],
    # 中间表 包含表名不生成接口以及CRUD
    'intermediate_table'=>['commons'],
];
