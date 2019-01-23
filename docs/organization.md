# Organization Summary

### View Summary [GET /organization]

Fetch the organization associated with the current user.

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "name": "Hopper Labs",
        "slug": "hopper-labs",
        "logo": {
            "hashid": "",
            "original": "",
            "standard": "",
            "thumbnail": "",
            "icon": ""
        },
        "settings": [
            {
                "key": "label.notebooks",
                "value": "Experiments"
            },
            {
                "key": "label.protocols",
                "value": "Protocols"
            },
            {
                "key": "label.plates",
                "value": "Plates"
            }
        ]
    }
}
```

### Read Organization Setting [GET /organization/setting/{key}]

Fetch the value of a [setting flag](/settings.html) for this organization.

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `key`: The setting to look up

+ Response 200 (application/json)

```json
{
    "data": {
        "key": "label.protocols",
        "value": "Protocols",
    }
}
```

### Update Organization Setting [PUT /organization/setting]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Form Data

    + `key`: The name of the setting being adjusted
    + `value`: The new value for the setting

+ Response 204 (application/json)

```json

```

+ Response 403 (application/json)

```json
{
    "message": "This action is unauthorized."
}
```
