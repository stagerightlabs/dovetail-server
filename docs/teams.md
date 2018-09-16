# Teams

### View All Teams [GET /teams]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "wy5dn36",
            "name": "Red Team"
        },
        // ..
    ]
}
```

### Create New Team [POST /teams]

Users must have permission to perform this action.

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Form Data

    + `name`: The team's name

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "wy5dn36",
        "name": "Red Team"
    }
}
```

+ Response 403 (application/json)

```json
{
    "message": "This action is unauthorized."
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

### View Team [GET /teams/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The team's database hashid

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "wy5dn36",
        "name": "Red Team",
        "members": [
            {
                "hashid": "wy5dn36",
                "name": "Grace Hopper",
                "email": "hopper@example.com",
                "rank": "Admin",
                "title": "Technologist Level III",
            },
            // ..
        ]
    }
}
```

### Update Team [PUT /teams/{hashid}]

Users must have permission to perform this action.

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The team's database hashid

+ Form Data

    + `name`: The team's new name

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "wy5dn36",
        "name": "Red Team"
    }
}
```

### Delete Team [DELETE /teams/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The team's database hashid

+ Response 200 (application/json)

```json

```

### Add Team Member [POST /teams/{hashid}/members]

Users must have membership granting permission to add members to teams.

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The team's database hashid

+ Form Data

    + `member`:  The new member's hashid

+ Response 201 (application/json)

```json

```

### Remove Team Member [DELETE /teams/{team}/members/{member}]

Users must have membership granting permissions to remove members from teams.

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `team`: The team's database hashid
    + `member`: The hashid of the user to be removed

+ Response 204 (application/json)

```json

```
