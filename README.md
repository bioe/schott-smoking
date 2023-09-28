## Windows Production Requirement
### Sqlsrv Driver
Depends on the PHP in the system

xx = TS/NTS 

xxx = x86/x64

php_pdo_sqlsrv_82_xx_xxx.dll

php_sqlsrv_82_xx_xxx.dll

Copy the .dll to php ext folder and add the extension in php.ini

### Download ODBC Driver and install
https://learn.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server?view=sql-server-ver16

Enable the extension in php.ini

```extension=pdo_odbc```

```extension=odbc```

### Window Task Scheduler
1 - Copy ```scheduler.bat``` at setup folder, (modify the path if need)

2 - Go to Windows Task Scheduler (fast way is press Win+R and enter taskschd.msc).

3 - Click Create basic task, choose When I logon trigger and then choose Start a program -> your .bat file.
4 - Check Open properties dialog option and click Finish.

5 - In task properties click Triggers, then click New and add new trigger Repeat task every - 1 minute.

Now this task will run Laravel scheduler every one minute.
## Serve

```npm run dev```

```php artisan serve```

## Controller
### Index
For building filter
```
$filters = $this->filterSessions($request, 'THE PREFIX', [
    'keyword' => ''
]);
```

## Eloquent
### Index
Scope `->filterSort($filters)` for index sorting

## Vue
### Template
You can find Index and Edit template at `resources/js/Components/Clone`
