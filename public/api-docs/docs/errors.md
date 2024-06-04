# List of Error Codes

The TamaExpress API uses the following error codes:

We use `http_status_code` as error code.

| Error Code   |      Description      | 
|----------|:-------------:|
| 400 |  Bad Request – Your request Failed due to bad input |
| 401 |  Un Authorized Request – wrong API token |
| 403 |  Forbidden Request – Request cannot be processed |
| 404 |  Requested API not found  |
| 405 |  Method is not allowed - You tried to access API with an invalid request method. |
| 406 |  Not Acceptable – You requested a format that isn’t json |
| 410 |  Gone – The API that you requested has been removed |
| 429 |  Too Many Requests – You’re requesting too many API request! |
| 500 |  Internal Server Error – We had a problem with our server. Try again later. |
| 503 |  Service Unavailable – We’re offline for maintenance. Please try again later. |
