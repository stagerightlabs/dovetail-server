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

+ Form Data

    + `notebook_id`: The hashid of the notebook this page will be assigned to
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
