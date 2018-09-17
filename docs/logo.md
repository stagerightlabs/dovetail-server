# Logos

### Store Logo [POST /logos]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Form Data

    + `logo`: The file being uploaded.  Required.
    + `owner_type`: The type of model this logo will be associated with: "user", "organization"
    + `owner_hashid`: The hashid of the model this logo will be associated with.

+ Response 200 (application/json)

```json
{
    "hashid": "wy5dn36",
    "original": "https://picsum.photos/600",
    "large": "https://picsum.photos/160",
    "small": "https://picsum.photos/50",
    "filename": "filename.png"
}
```

### Delete Logo [DELETE /logo/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The hashid of the logo being removed

+ Response 204 (application/json)

```json

```
