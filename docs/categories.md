# Categories

### List Available Categories [GET /categories]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "wy5dn36",
            "name": "Polymerase"
        },
        //..
    ]
}
```

### Store New Category [POST /categories]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Form Data

    + `name`: The category's name.  Required; must be unique within an organization.

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "wy5dn36",
            "name": "Polymerase"
        },
        //..
    ]
}
```

+ Response 422 (application/json)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": array:1 ["The name field is required."]
    }
}
```

### Show Category [GET /categories/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The category's database hashid

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "wy5dn36",
            "name": "Polymerase"
        },
        //..
    ]
}
```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Category]."
}
```

### Update Category [PUT /categories/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The category's database hashid

+ Form Data

    + `name`: The category's name. Required; must be unique within an organization.

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "wy5dn36",
        "name": "Polymerase"
    }
}
```

### Delete Category [DELETE /categories/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The category's database hashid

+ Response 204

```json

```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Category]."
}
```
