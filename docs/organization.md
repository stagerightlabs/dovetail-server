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
        "id": 1,
        "name": "Hopper Labs",
        "slug": "hopper-labs",
        "logo_url": null,
        "stripe_id": null,
        "card_brand": null,
        "card_last_four": null,
        "trial_ends_at": null,
        "configuration": null,
        "created_at": "2018-09-12 23:32:24",
        "updated_at": "2018-09-12 23:32:24",
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

### Update Organization Setting [POST /organization/setting]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Form Data

    + `settings[]`: Key value pairs. Such as `permissons[label.protocols] = "SOPs"`

+ Response 204 (application/json)

```json

```

+ Response 403 (application/json)

```json
{
    "message": "This action is unauthorized."
}
```
