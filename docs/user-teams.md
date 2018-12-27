# User Teams

### Index [GET /user/teams]

Retrieve all of the teams that the active user belongs to.

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "wy5dn36",
            "name": "Red Team",
            "slug": "red-team",
            "members_count": 2,
        },
        // ..
    ]
}
```
