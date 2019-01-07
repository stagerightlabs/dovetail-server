# Pages

### Get all of a Notebook's Pages [GET /notebooks/{hashid}/pages]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The notebook's hashid

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "x737zq8",
            "notebook_id": "x737zq8",
            "content":  "Lorem ipsum...",
            "sort_order": 0
        },
        //..
    ]
}
```

### Store New Page [POST /notebooks/{hashid}/pages]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The hashid of the notebook this page will be assigned to

+ Form Data

    + `content`: The content of the page; markdown or plain text.

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "notebook_id": "x737zq8",
        "content":  "Lorem ipsum...",
        "sort_order": 0
    }
}
```

### Get single notebook page [GET /notebooks/{notebook}/pages/{page}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "notebook_id": "x737zq8",
        "content":  "Lorem ipsum...",
        "sort_order": 0
    }
}
```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Page]."
}
```

### Update Notebook Page [PUT /notebooks/{notebook}/pages/{page}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid

+ Form Data

    + `content`: The content of the page; markdown or plain text.

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "notebook_id": "x737zq8",
        "content":  "Lorem ipsum...",
        "sort_order": 0
    }
}
```

### Delete Notebook Page [DELETE /notebooks/{notebook}/pages/{page}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid

+ Response 204

```json

```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Page]."
}
```

### Update Sort Order [PUT /notebook/{hashid}/pages/sort-order]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The notebook's hashid

+ Form Data

    + `pages[]`: An array of page hashids arranged in the desired order.

+ Response 204

```json

```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Page]."
}
```

### Get all of a Notebook Page's activity logs [GET /notebooks/{notebook}/pages/{page}/activity]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "x737zq8",
            "user_id": "x737zq8",
            "user_name": "Grace Hopper",
            "description": "Created",
            "created_at": "2018-09-14T02:42:20+00:00",
            "since_created": "1 hour ago",
        },
        //..
    ]
}
```
