# Environment Variables


| Key                   | Description                                               |
|-----------------------|-----------------------------------------------------------|
| APP_NAME              | The name of the application; used in email templates.     |
| APP_ENV               | The current application environment; local, production.   |
| APP_KEY               | A salt used for hashing (`php artisan key:generate`).     |
| APP_DEBUG             | Enable or disable debugging. `true` or `false`.           |
| APP_URL               | The url the application will be reachable at.             |
| LOG_CHANNEL           | The method used for recording logs. ([See Laravel Docs](https://laravel.com/docs/5.7/logging#building-log-stacks)) |
| DB_CONNECTION         | The name of the default configured database connection.   |
| DB_HOST               | The database connection host                              |
| DB_PORT               | The database connection port                              |
| DB_DATABASE           | The name of the database                                  |
| DB_USERNAME           | Database credentials username                             |
| DB_PASSWORD           | Database credentials password                             |
| BROADCAST_DRIVER      | The broadcast message delivery method                     |
| CACHE_DRIVER          | The type of caching tool to be used (Redis, memcached)    |
| QUEUE_CONNECTION      | The type of queue proccessor (Redis, SQS, Beanstalkd)     |
| SESSION_DRIVER        | The method used for storing session data                  |
| SESSION_LIFETIME      | The length of time sessions will last before expiring     |
| REDIS_HOST            | The redis connection host                                 |
| REDIS_PASSWORD        | Redis credentials password                                |
| REDIS_PORT            | The redis connection port                                 |
| MAIL_DRIVER           | The desired mail handling tool (mailgun, sqs, etc)        |
| MAIL_HOST             | The mail service host url                                 |
| MAIL_PORT             | The mail service connection port                          |
| MAIL_USERNAME         | The mail service credentials username                     |
| MAIL_PASSWORD         | The mail service credentials password                     |
| MAIL_ENCRYPTION       | Should we use an encrypted connection? ('tls', '')        |
| FRONTEND_BASE_URL     | The url the api web client will be reachable at           |
