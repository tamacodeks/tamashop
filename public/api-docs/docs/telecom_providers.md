# Telecom Providers
## How to?

This endpoint retrieves all calling card telecom providers

HTTP Request:
```http
GET http://pms-demat.local/api/telecom-providers
```

## Example


```php
$ch = curl_init();
$headers = array(
    'Accept: application/json',
    'Authorization: Bearer LQBtT676H8BVB8kqsZJvy9eFiyPSDdzFQW0rCCGXJ',
);
curl_setopt($ch, CURLOPT_URL, "http://pms-demat.local/api/telecom-providers");
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
    "data": [
        {
            "provider_id": 13,
            "name": "Auchan",
            "image": "http://pms-demat.local/storage/5/conversions/auchan-thumb.jpg"
        },
        {
            "provider_id": 14,
            "name": "Bouygues",
            "image": "http://pms-demat.local/storage/6/conversions/bouygues-thumb.jpg"
        },
        {
            "provider_id": 15,
            "name": "LaPoste",
            "image": "http://pms-demat.local/storage/7/conversions/laposte-thumb.jpg"
        },
        {
            "provider_id": 16,
            "name": "Lebara",
            "image": "http://pms-demat.local/storage/8/conversions/lebara-thumb.jpg"
        },
        {
            "provider_id": 17,
            "name": "Lyca",
            "image": "http://pms-demat.local/storage/9/conversions/lyca-thumb.jpg"
        },
        {
            "provider_id": 18,
            "name": "Mobiho",
            "image": "http://pms-demat.local/storage/10/conversions/mobiho-thumb.jpg"
        },
        {
            "provider_id": 19,
            "name": "Nrj",
            "image": "http://pms-demat.local/storage/19/conversions/nrj-thumb.jpg"
        },
        {
            "provider_id": 20,
            "name": "Orange",
            "image": "http://pms-demat.local/storage/11/conversions/orange-thumb.jpg"
        },
        {
            "provider_id": 21,
            "name": "SFR",
            "image": "http://pms-demat.local/storage/12/conversions/sfr-thumb.jpg"
        },
        {
            "provider_id": 22,
            "name": "Syma",
            "image": "http://pms-demat.local/storage/13/conversions/syma-thumb.jpg"
        },
        {
            "provider_id": 23,
            "name": "Vectone",
            "image": "http://pms-demat.local/storage/14/conversions/vectone-thumb.jpg"
        },
        {
            "provider_id": 24,
            "name": "Carte a code",
            "image": "http://pms-demat.local/storage/15/conversions/carte_a_code-thumb.jpg"
        },
        {
            "provider_id": 29,
            "name": "Neosurf",
            "image": "http://pms-demat.local/storage/16/conversions/neosurf-thumb.jpg"
        },
        {
            "provider_id": 30,
            "name": "Paysafecard",
            "image": "http://pms-demat.local/storage/17/conversions/paysafecard-thumb.jpg"
        },
        {
            "provider_id": 31,
            "name": "PrepaidCashService",
            "image": "http://pms-demat.local/storage/18/conversions/prepaidcashservice-thumb.jpg"
        },
        {
            "provider_id": 32,
            "name": "Aircel",
            "image": "http://pms-demat.local/storage/43/conversions/aircel-thumb.jpg"
        }
    ]
}
```

### Error
```json
{
    "error": "Unauthenticated!"
}
```

