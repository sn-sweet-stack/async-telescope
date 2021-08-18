# WORK IN PROGRESS - DO NOT USE IN PRODUCTION!

### Asynchronous storage driver for Laravel Telescope

If you use Telescope in production and you are concerned with the amount of work 
Telescope does to store its entries during the request, you may use this driver 
to offload its storage jobs to the queue worker.

1. Install:

```bash
composer require sweetstack/async-telescope
```

2. Add this line to your `config/telescope.php`:

```php
    ...
    'async' => true,
```

You may use this switch to toggle the async mode on and off according to your
needs.

3. Add these lines to your `app/Providers/TelescopeServiceProvider.php`:

```php
use Sweetstack\AsyncTelescope\AsyncDatabaseEntriesRepository;

...

public function register()
{
    ...

    AsyncDatabaseEntriesRepository::register($this->app);
}
```

You should be all set, now Telescope will queue storage jobs instead of using 
the database directly during the request.

Additionally, if you want to configure a separate connection and / or queue for
the jobs pushed by the async driver, add in `config/telescope.php`:

```php
    'storage' => [
     
        ...
        
        'async' => [
            'connection' => 'redis',
            'queue' => 'default',
        ],
    ],
```


