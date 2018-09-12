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
