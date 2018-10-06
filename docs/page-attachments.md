# Page Attachments

Documents associated with Notebook Pages

### Get page attachments [GET /notebooks/{notebook}/pages/{page}/attachments]

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

            "created_at": "2018-09-26T03:47:39+00:00",
        },
        //..
    ]
}
```

### Store New Page Attachment [POST /notebooks/{notebook}/pages/{page}/attachment]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid

+ Form Data

    + `attachment`: The content of the uploaded file

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",

        "created_at": "2018-09-26T03:47:39+00:00",
    }
}
```

### Get single page attachment [GET /notebooks/{notebook}/pages/{page}/attachments/{attachment}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid
    + `attachment`: The attachment's hashid

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",

        "created_at": "2018-09-26T03:47:39+00:00",
    }
}
```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Comment]."
}
```

### Delete Attachment [DELETE /notebooks/{notebook}/pages/{page}/attachments/{attachment}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid
    + `attachment`: The attachment's hashid

+ Response 204

```json

```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Comment]."
}
```
