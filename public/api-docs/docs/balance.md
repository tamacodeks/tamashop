# Balance
## How to?

This endpoint retrieves your current balance, credit limit and rate table information

HTTP Request:
```http
GET http://pms-demat.local/api/heartbeat
```

## Example


```php
$ch = curl_init();
$headers = array(
    'Accept: application/json',
    'Authorization: Bearer LQBtT676H8BVB8kqsZJvy9eFiyPSDdzFQW0rCCGXJ',
);
curl_setopt($ch, CURLOPT_URL, "http://pms-demat.local/api/heartbeat");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
```

## Response

### Success
```json
{
    "data": {
        "code": 200,
        "http_code": 200,
        "message": "Balance fetched",
        "result": {
            "balance": "€-12.50",
            "ws_balance": "-12.50",
            "credit_limit": "-50.00",
            "rate_table": "Example"
        }
    }
}
```

### Error
```json
{
    "error": "Unauthenticated!"
}
```

