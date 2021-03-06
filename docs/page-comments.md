# Page Comments

### Get page comments [GET /notebooks/{notebook}/pages/{page}/comments]

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
            "content": "Quidem totam dicta eum esse quam adipisci dolorum. Et mollitia officia et. Sit aut error laborum fugit.",
            "commentator": "Grace Hopper",
            "commentator_initials": "GH",
            "commentator_id": "x737zq8",
            "edited": false,
            "created_at": "2018-09-26T03:47:39+00:00",
            "since_created": "1 hour ago",
        },
        //..
    ]
}
```

### Store New Page Comment [POST /notebooks/{notebook}/pages/{page}/comments]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid

+ Form Data

    + `content`: The content of the comment

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "content": "Quidem totam dicta eum esse quam adipisci dolorum. Et mollitia officia et. Sit aut error laborum fugit.",
        "commentator": "Grace Hopper",
        "commentator_initials": "GH",
        "commentator_id": "x737zq8",
        "edited": false,
        "created_at": "2018-09-26T03:47:39+00:00",
        "since_created": "1 hour ago",
    }
}
```

### Get single page comment [GET /notebooks/{notebook}/pages/{page}/comments/{comment}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid
    + `comment`: The comment's hashid

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "content": "Quidem totam dicta eum esse quam adipisci dolorum. Et mollitia officia et. Sit aut error laborum fugit.",
        "commentator": "Grace Hopper",
        "commentator_initials": "GH",
        "commentator_id": "x737zq8",
        "edited": false,
        "created_at": "2018-09-26T03:47:39+00:00",
        "since_created": "1 hour ago",
    }
}
```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Comment]."
}
```

### Update Page Comment [PUT /notebooks/{notebook}/pages/{page}/comments/{comment}]

Users may only edit their own comments.

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid
    + `comment`: The comment's hashid

+ Form Data

    + `content`: The content of the comment; markdown or plain text.

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "content": "Quidem totam dicta eum esse quam adipisci dolorum. Et mollitia officia et. Sit aut error laborum fugit.",
        "commentator": "Grace Hopper",
        "commentator_initials": "GH",
        "commentator_id": "x737zq8",
        "edited": true,
        "created_at": "2018-09-26T03:47:39+00:00",
        "since_created": "1 hour ago",
    }
}
```

### Delete Page Comment [DELETE /notebooks/{notebook}/pages/{page}/comments/{comment}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `notebook`: The notebook's hashid
    + `page`: The page's hashid
    + `comment`: The comment's hashid

+ Response 204

```json

```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Comment]."
}
```
