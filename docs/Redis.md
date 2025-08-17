# Использование Redis

## Простой сценарий для примера

```php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

function getUserStep($chatId) {
    global $redis;
    return $redis->get("user:{$chatId}:step") ?: 1;
}

function setUserStep($chatId, $step) {
    global $redis;
    $redis->set("user:{$chatId}:step", $step);
}
```