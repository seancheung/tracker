# LaraTracker
Eloquent CRUD records tracking for Laravel

## Installation

You can install this package via composer

```shel
composer require panoscape/laratracker
```

First, register service provider

> config/app.php

```php
'providers' => [
    ...
    Panoscape\LaraTracker\LaraTrackerServiceProvider::class,
];
```

Next, you may publish the config file with

```shell
php artisan vendor:publish --provider="Panoscape\LaraTracker\LaraTrackerServiceProvider" --tag=config
```

Migration

```shell
php artisan vendor:publish --provider="Panoscape\LaraTracker\LaraTrackerServiceProvider" --tag=migrations
php artisan migrate
```

Publish localization

```shell
php artisan vendor:publish --provider="Panoscape\LaraTracker\LaraTrackerServiceProvider" --tag=translations
```

## Usage

Add `Panoscape\LaraTracker\Context` trait to any model(s) you'd like to track.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Panoscape\LaraTracker\Context;

class Project extends Model
{
    use Context;

    ...
}
```

Optionally you can add  `Panoscape\LaraTracker\Recordable` interface to its implements, and implement `getContextLabel` method.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Panoscape\LaraTracker\Context;
use Panoscape\LaraTracker\Recordable;

class Project extends Model implements Recordable
{
    use Context;

    public function getContextLabel()
    {
        return $this->display_name;
    }
    ...
}
```
This will add the specified name value to the records.

> Created new Project project_001

If `Panoscape\LaraTracker\Recordable` interface is not found on the model, the record would be

> Created new Project

### Context

A `Context` is a model which is tracked on CURD actions. Like in `User 001 Deleted Project 001`, `Project 001` is the `Context` we're talking about here.

To get all records of a contexted model

```php
$project->records();
```

or via dynamic property

```php
$project->records;
```

You may change the name of `records` if you like(or to resolve method name conflicts)

```php
use Context { records as logs; }
```

### Agent

An `Agent` is a user who performs an action. Like in `User 001 Deleted Project 001`, `User 001` is the `Agent`.

The agent in a record is always got from `Auth`, thus the authorized user when the action is being performed.

You might have multiple user types in your application, and this package take well care of it.

To be able to getting related records from an agent, all you need is to add `Panoscape\LaraTracker\Agent` trait to that model.

```php
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Panoscape\LaraTracker\Agent;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, Agent;

    ...
}
```

Get all records performed by an agent

```php
$user->records();
```

or via dynamic property

```php
$user->records;
```

You may change the name of `records` if you like(or to resolve method name conflicts)

```php
use Agent { records as logs; }
```

**A record's agent might be null if it's tracked while unauthenticated**

### Record

The default strcuture of a record

|     Field    |         Type         | Nullable |
|:------------:|:--------------------:|:--------:|
|      id      | big unsigned integer |     N    |
|  context_id  |   unsigned integer   |     N    |
| context_type |        string        |     N    |
|   agent_id   |   unsigned integer   |     Y    |
|  agent_type  |        string        |     Y    |
|    message   |        string        |     N    |
|     meta     |         text         |     Y    |
| performed_at |       timestamp      |     N    |

The `message` filed is the action message in brief. examples

> Created new Project my_project

> Updating Project my_project

> Deleting Project my_project

> Restoring Project my_project

The `meta` field is a json context of modified properties of a model. It's only available when the record is `Updating ...`.

The `performed_at` is a timestamp which indicates the moment the record is created, thus the moment the action being performed.


Get context of a record

```php
$record->context();
```

or via dynamic property

```php
$record->context;
```

Check agent of a record

```php
$record->hasAgent();
```

Get agent of a record

```php
$record->agent();
```

or via dynamic property

```php
$record->agent;
```

The `meta` property on Record is deserialized to an array, which hold the modification history

```php
$record->meta;
```

```php
[
    ['key' => 'name', 'old' => 'myName', 'new' => 'myNewName'],
    ['key' => 'age', 'old' => 10, 'new' => 100],
    ...
]
```


### Localization

You may lolcalize the record output

This is the default localization

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laratracker Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used across application for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'created' => 'Created new :context :name',

    'updating' => 'Updating :context :name',

    'deleting' => 'Deleting :context :name',

    'restored' => 'Restored :context :name',
];

```

Simplified Chinese

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laratracker Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used across application for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'created' => '创建新的:context :name',

    'updating' => '更新:context :name',

    'deleting' => '删除:context :name',

    'restored' => '恢复:context :name',
];
```

The `:context` is by default the class's base name. 

To localize it, you can append a new language line to the localization file with the key being **the class base name in snake case**

```php
<?php

return [

    ...

    'project' => '项目',
    'component_template' => '组件模板',
];

```

The `:name` is provided by `Panoscape\LaraTracker\Recordable` interface which we have mentioned above.


## Configuration

Here is the default Configuration

```php
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
```

To change the table hodling the records, set `records_table`. Remember to update your migration after that.

To apply filters to operations, set `operations`. Only operations in the list will be tracked.

To apply filters to agents, set `agent_ignore`. Operations performed by agents in the list will **NOT** be tracked.

If you'd like to bypass an unauthenticated operation, add `'nobody'` to `agent_ignore`.

If you need to bypass a specific agent type, add  it's class name to `agent_ignore`.