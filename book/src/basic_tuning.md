# Basic Tuning

<!-- cspell:ignore unixsocket,unixsocketperm,usermod,ondemand,curlopt,cainfo -->

> [!IMPORTANT]
> These guides are intended for UNIT3D v8.0.0 + instances. While these are better than defaults be careful blindly following them. Proper tuning requires understanding your server, running tests and monitoring the results.

## Redis Single Server

| Category       | Severity   | Time To Fix  |
| -------------  |:----------:| ------------:|
| :rocket: Performance | Major | 30 minutes  |

### Introduction

If your Redis service is running on your web server, it is highly recommended that you use **Unix sockets** instead of **TCP ports** to communicate with your web server.

Based on the Redis official benchmark, you can **improve performance by up to 50%** using unix sockets (versus TCP ports) on Redis.

Of course, unix sockets can only be used if both your Laravel application and Redis are running on the same server.

### How To Enable Unix Sockets

First, create the redis folder that the unix socket will be in and set appropriate permissions:

```bash
sudo mkdir -p /var/run/redis/
sudo chown -R redis:www-data /var/run/redis
sudo usermod -aG redis www-data
```

Next, add the unix socket path and permissions in your Redis configuration file (typically at `/etc/redis/redis.conf`):

```ini
unixsocket /var/run/redis/redis.sock
unixsocketperm 770
```

Finally, set your corresponding env variables to the socket path as above:

```bash
REDIS_HOST=/var/run/redis/redis.sock
REDIS_PORT=-1
REDIS_SCHEME=unix
```

Ensure that you have your `config/database.php` file refer to the above variables (notice the `scheme` addition below):

```php{7}
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
        'scheme' => env('REDIS_SCHEME', 'tcp'),
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
],
```

Once that's all done simply restart redis.

```bash
sudo systemctl restart redis
```


### References

- [Redis Official Benchmark on Socket vs TCP](https://redis.io/topics/benchmarks)
- [Laravel Documentation on Redis](https://laravel.com/docs/redis)

> [!NOTE]
> Keep in mind that when using unix socket you will now connect to redis-cli in terminal like so: `redis-cli -s /var/run/redis/redis.sock`


## MySQL Single Server

| Category       | Severity   | Time To Fix  |
| -------------  |:----------:| ------------:|
| :rocket: Performance | Major | 10 minutes  |

### Introduction

If your MySQL database is running on your web server, it is highly recommended that you use **Unix sockets** instead of **TCP ports** to communicate with your web server.

Based on Percona's benchmark, you can **improve performance by up to 50%** using unix sockets (versus TCP ports on MySQL).

Of course, unix sockets can only be used if both your UNIT3D application and database are running on the same server which is by default.

### How To Enable Unix Sockets

First, open your MySQL configuration file.

```bash
nano /etc/mysql/my.cnf
```

Then, uncomment and change (if needed) the socket parameter in the `[mysqld]` section of one of the above configuration files:

```ini
[mysqld]
user            = mysql
pid-file        = /var/run/mysqld/mysqld.pid
socket          = /var/run/mysqld/mysqld.sock
port            = 3306
```

Close this file, then ensure that the mysqld.sock file exists by running an ls command on the directory where MySQL expects to find it:

`ls -a /var/run/mysqld/`

If the socket file exists, you will see it in this command’s output:

Output
`.  ..  mysqld.pid  mysqld.sock  mysqld.sock.lock`
If the file does not exist, the reason may be that MySQL is trying to create it, but does not have adequate permissions to do so. You can ensure that the correct permissions are in place by changing the directory’s ownership to the mysql user and group:

`sudo chown mysql:mysql /var/run/mysqld/`

Then ensure that the mysql user has the appropriate permissions over the directory. Setting these to 775 will work in most cases:

`sudo chmod -R 755 /var/run/mysqld/`

Finally, set your `database.connections.mysql.unix_socket` configuration variable or the corresponding env variable to the socket path as above:

```bash
DB_SOCKET=/var/run/mysqld/mysqld.sock
```

Once that's all done simply refresh your cache and then restart the services.

```bash
php artisan set:all_cache
```

```bash
sudo systemctl restart mysql && sudo systemctl restart php8.3-fpm && sudo systemctl restart nginx
```

### References

- [Percona Benchmark (Unix vs TCP)](https://www.percona.com/blog/2020/04/13/need-to-connect-to-a-local-mysql-server-use-unix-domain-socket/)
- [MySQL Unix Socket Setup](https://www.digitalocean.com/community/tutorials/how-to-troubleshoot-socket-errors-in-mysql)
- [MySQL Shell Connections Guide](https://dev.mysql.com/doc/mysql-shell/8.0/en/mysql-shell-connection-socket.html)

## Composer Autoloader Optimization

| Category       | Severity   | Time To Fix  |
| -------------  |:----------:| ------------:|
| :rocket: Performance | Moderate | 5 minutes  |

### Introduction

Due to the way PSR-0/4 autoloading rules are defined, the Composer autoloader checks the filesystem before resolving a classname conclusively.

In production, Composer allows for optimization to convert the PSR-0 and PSR-4 autoloading rules into classmap rules, making autoloading quite a bit faster. In production we also don't need all the require-dev dependencies loaded up!

### How To Optimize?
It's really simple. SSH to your server and run the following commands.
```bash
composer install --prefer-dist --no-dev
```

```bash
composer dump-autoload --optimize
```

### References

- https://getcomposer.org/doc/articles/autoloader-optimization.md

## PHP8 OPCache

| Category       | Severity   | Time To Fix  |
| -------------  |:----------:| ------------:|
| :rocket: Performance | Major | 10 minutes  |


### Introduction

Opcache provides massive performance gains. One of the benchmarks suggest it can provide a 5.5X performance gain in a Laravel application!

`What is OPcache?`
Every time you execute a PHP script, the script needs to be compiled to byte code. OPCache leverages a cache for this bytecode, so the next time the same script is requested, it doesn’t have to recompile it. This can save some precious execution time, and thus make UNIT3D faster.

`Sounds awesome, so how can you use it?`
Easy. SSH to your server and run the following command. `sudo nano /etc/php/8.3/fpm/php.ini` This is assuming your on PHP 8.3. If not then adjust the command. Once you have the config open search for `opcache`.

Now you can change some values, I will walk you through the most important ones.

`opcache.enable=1`
This of course, enables OPcache for php-fpm. Make sure it is uncommented. AKA remove the`;`

`opcache.enable_cli=1`
This of course, enables OPcache for php-cli. Make sure it is uncommented. AKA remove the`;`

`opcache.memory_consumption=256M`
How many Megabyte you want to assign to OPCache. Choose anything higher than 64 (default value) depending on your needs. 2GB is sufficient but if have more RAM then make use of it! Make sure it is uncommented. AKA remove the`;`

`opcache.interned_strings_buffer=64`
How many Megabyte you want to assign to interned strings. Choose anything higher than 16 (default value). 1GB is sufficient but if have more RAM then make use of it! Make sure it is uncommented. AKA remove the`;`

`opcache.validate_timestamps=0`
This will revalidate the script. If you set this to 0 (best performance), you need to manually clear the OPcache every time your PHP code changes. So if you update UNIT3D using `php artisan git:update` or manually make changes yourself you need to run `sudo systemctl restart php8.2-fpm` afterwords for your changes to take effect and show. Make sure it is uncommented. AKA remove the`;`

`opcache.save_comments=1`
This will preserve comments in your script, I recommend to keep this enabled, as some libraries depend on it, and I couldn’t find any benefits from disabling it (except from saving a few bytes RAM). Make sure it is uncommented. AKA remove the`;`

And there you have it folks. Experiment with these values, depending on the resources of your server. Save the file and exit and restart PHP `sudo systemctl restart php8.3-fpm`.

Enjoy! 🖖

## PHP 8 Preloading

| Category       | Severity   | Time To Fix  |
| -------------  |:----------:| ------------:|
| :rocket: Performance | Major | 5 minutes  |


### Introduction

This is chaining off `Want More Performance? Lets talk about OPCache!` guide. You must have OPCache enabled to use preloading.

PHP preloading for PHP >=7.4. Preloading is a feature of php that will pre-compile php functions and classes to opcache. Thus, this becomes available in your programs with out needing to require the files, which improves speed. To read more on php preloading you can see the [opcache.preloading documentation](https://www.php.net/manual/en/opcache.preloading.php).

### Enabling Preloading

SSH to your server and run the following command. `sudo nano /etc/php/8.3/fpm/php.ini` This is assuming your on PHP 8.3. If not then adjust the command. Once you have the config open search for `preload`.

Now you can change some values.

```
; Specifies a PHP script that is going to be compiled and executed at server
; start-up.
; https://php.net/opcache.preload
opcache.preload = '/var/www/html/preload.php';

; Preloading code as root is not allowed for security reasons. This directive
; facilitates to let the preloading to be run as another user.
; https://php.net/opcache.preload_user
opcache.preload_user=ubuntu
```

As you can see we are calling the preload file included in UNIT3D located in `/var/www/html/preload.php`.
`opcache.preload_user=ubuntu` you should changed to your server user. Not root!!!!

And there you have it folks. Save the file and exit and restart PHP `sudo systemctl restart php8.3-fpm`. You are now preloading Laravel thus making UNIT3D faster.

## PHP8 JIT

| Category       | Severity   | Time To Fix  |
| -------------  |:----------:| ------------:|
| :rocket: Performance | Moderate | 5 minutes  |

### Introduction

PHP 8 adds a JIT compiler to PHP's core which has the potential to speed up performance dramatically.

First of all, the JIT will only work if opcache is enabled, this is the default for most PHP installations, but you should make sure that `opcache.enable` is set to `1` in your php.ini file. Enabling the JIT itself is done by specifying `opcache.jit_buffer_size` in php.ini. **_So I recommend checking the OPcache guide I made first then coming back here._**

### How To Enable JIT
SSH to your server and run the following command. `sudo nano /etc/php/8.3/fpm/php.ini` This is assuming your on PHP 8.2. If not then adjust the command. Once you have the config open search for `opcache.jit`.

If you do not get any results then search for `[curl]` you should see the following.

```
[curl]
; A default value for the CURLOPT_CAINFO option. This is required to be an
; absolute path.
;curl.cainfo =
```

Right above it add:

```
opcache.jit_buffer_size=256M
```

Its as simple as that. Save and exit and restart PHP. `sudo systemctl restart php8.2-fpm`

## PM Static

| Category       | Severity   | Time To Fix  |
| -------------  |:----------:| ------------:|
| :rocket: Performance | Major | 10 minutes  |

> [!IMPORTANT]
> This guide is intended for high traffic sites.

### Introduction

Lets give a basic description on what these options are:

`pm = dynamic` – the number of child processes is set dynamically based on the following directives: pm.max_children, pm.start_servers,pm.min_spare_servers, pm.max_spare_servers.

`pm = ondemand` – the processes spawn on demand (when requested, as opposed to dynamic, where pm.start_servers are started when the service is started.

`pm = static` – the number of child processes is fixed by pm.max_children.

The PHP-FPM pm static setting depends heavily on how much free memory your server has. Basically if you are suffering from low server memory, then pm ondemand or dynamic maybe be better options. On the other hand, if you have the memory available you can avoid much of the PHP process manager (PM) overhead by setting pm static to the max capacity of your server. In other words, when you do the math, pm.static should be set to the max amount of PHP-FPM processes that can run without creating memory availability or cache pressure issues. Also, not so high as to overwhelm CPU(s) and have a pile of pending PHP-FPM operations.

### Enabling Static

Lets open up our PHP configuration file. `sudo nano /etc/php/8.3/fpm/pool.d/www.conf`

Set `pm = static`
Set `pm.max_children = 25`

Save, Exit and Restart `sudo systemctl restart php8.3-fpm`

### Conclusion

When it comes to PHP-FPM, once you start to serve serious traffic, ondemand and dynamic process managers for PHP-FPM can limit throughput because of the inherent overhead. Know your system and set your PHP-FPM processes to match your server’s max capacity.
