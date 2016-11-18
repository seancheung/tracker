<?php

return [

    /*
    |--------------------------------------------------------------
    | Literally
    |--------------------------------------------------------------
    |
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------
    | Record table name
    |--------------------------------------------------------------
    |
    |
    */
    'records_table' => 'laratracker_records',

    /*
    |--------------------------------------------------------------
    | Operatiopn whitelist
    |--------------------------------------------------------------
    |
    | Operations in this array will be recorded.
    | Available operations are: created, updating, deleting, restored
    |
    */
    'operations' => [
        'created', 'updating', 'deleting', 'restored',
    ],

    /*
    |--------------------------------------------------------------
    | Agent blacklist
    |--------------------------------------------------------------
    |
    | Operations performed by agents in this array will NOT be recorded.
    | Please add the whole class names. Example: \App\User
    | Use 'nobody' to bypass unauthenticated operations
    |
    */
    'agent_ignore' => [
        
    ],

    /*
    |--------------------------------------------------------------
    | Enabled when application running in console
    |--------------------------------------------------------------
    |
    | When application is running in console(include seeding)
    |
    */
    'console' => false,

    /*
    |--------------------------------------------------------------
    | Enabled when application running in unit tests
    |--------------------------------------------------------------
    |
    | When application is running unit tests
    |
    */
    'unit_test' => false,

    /*
    |--------------------------------------------------------------
    | Enviroments blacklist
    |--------------------------------------------------------------
    |
    | When application's environment is in the list, tracker will be disabled
    |
    */
    'env_ignore' => [
        
    ],
    
];