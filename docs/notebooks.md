# Notebooks

### List Available Notebooks [GET /notebooks]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "wy5dn36",
            "name": "Experiment 24601",
            "category": "Experiments",
            "category_id": "wy5dn36"
        },
        //..
    ]
}
```

### Store New Notebook [POST /notebooks]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Form Data

    + `name`: The name of the new notebook.
    + `category_id`: Optional.  The hashid of the category to be applied to this notebook.

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "wy5dn36",
        "name": "Experiment 24601",
        "category": "Experiments",
        "category_id": "wy5dn36"
    },
}
```

+ Response 422 (application/json)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

### Show Notebook [GET /notebooks/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The category's database hashid

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "wy5dn36",
        "name": "Experiment 24601",
        "category": "Experiments",
        "category_id": "wy5dn36"
    }
}
```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Notebook]."
}
```

### Update Notebook [PUT /notebooks/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The notebook's database hashid

+ Form Data

    + `name`: The name of the new notebook.
    + `category_id`: Optional.  The hashid of the category to be applied to this notebook.

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "wy5dn36",
        "name": "Experiment 24601",
        "category": "Experiments",
        "category_id": "wy5dn36"
    }
}
```

### Delete Notebook [DELETE /notebooks/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The notebook's database hashid

+ Response 204

```json

```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Notebook]."
}
```
