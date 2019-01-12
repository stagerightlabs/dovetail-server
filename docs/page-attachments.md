# Page Attachments

Documents associated with Notebook Pages

### Get page attachments [GET /notebooks/{notebook}/pages/{page}/documents]

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
            "original": "http://link/to/original/file",
            "large": "http:://link/to/large/thumbnail",
            "small": "http:://link/to/small/thumbnail",
            "mimetime": "image/jpeg",
            "filename": "original_filename.jpg"
        },
        //..
    ]
}
```

### Store New Page Attachment [POST /notebooks/{notebook}/pages/{page}/documents]

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
        "original": "http://link/to/original/file",
        "large": "http:://link/to/large/thumbnail",
        "small": "http:://link/to/small/thumbnail",
        "mimetime": "image/jpeg",
        "filename": "original_filename.jpg"
    }
}
```

### Get single page attachment [GET /notebooks/{notebook}/pages/{page}/documents/{attachment}]

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
        "original": "http://link/to/original/file",
        "large": "http:://link/to/large/thumbnail",
        "small": "http:://link/to/small/thumbnail",
        "mimetime": "image/jpeg",
        "filename": "original_filename.jpg"
    }
}
```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Comment]."
}
```

### Delete Attachment [DELETE /notebooks/{notebook}/pages/{page}/documents/{attachment}]

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
