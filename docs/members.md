# Members

### Member Listing [GET /members]

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "wy5dn36",
            "name": "Grace Hopper",
            "email": "hopper@example.com",
            "rank": "Admin",
            "title": "Technologist Level III",
            "phone": "(705) 558-7119 x38444",
            "email_verified": true,
            "phone_verified": true,
            "blocked": false,
            "created_at": "2018-09-14T03:45:26+00:00",
        }
        // ...
    ]
}
```

+ Attributes

    + `id`: The member's database hashid
    + `name`: The member's name
    + `email`: The member's email address
    + `rank`: The member's account access level
    + `title`: The member's job title.  This can only be edited by administrators.
    + `phone`: The member's phone number.  Only visible to admins.
    + `email_verified`: The member's email verification status. Only visible to admins.
    + `phone_verified`: The member's phone verification status. Only visible to admins.
    + `blocked`: Indicates whether or not the user's account access has been blocked
    + `created_at`: The date the member's account was created.

### Deleted Member Listing [GET /members/deleted]

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "id": "wy5dn36",
            "name": "Grace Hopper",
            "email": "hopper@example.com",
            "rank": "Admin",
            "title": "Technologist Level III",
            "phone": "(705) 558-7119 x38444",
            "email_verified": true,
            "phone_verified": true,
            "blocked": false,
            "created_at": "2018-09-14T03:45:26+00:00",
            "deleted_at": "2018-09-21T03:45:26+00:00",
        }
        // ...
    ]
}
```

+ Attributes

    + `id`: The member's database hashid
    + `name`: The member's name
    + `email`: The member's email address
    + `rank`: The member's account access level
    + `title`: The member's job title.  This can only be edited by administrators.
    + `phone`: The member's phone number.  Only visible to admins.
    + `email_verified`: The member's email verification status. Only visible to admins.
    + `phone_verified`: The member's phone verification status. Only visible to admins.
    + `blocked`: Indicates whether or not the user's account access has been blocked
    + `created_at`: The date the member's account was created.
    + `deleted_at`: The date the member's account was deleted.

### Update Member [PUT /members/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The member's database hashid

+ Form Data

    + `email`: The member's new email address.  Changing this will revoke verified email status.
    + `phone`: The member's new phone number.  Changing this will revoke verified phone status.
    + `title`: The member's job title.

+ Response 200 (application/json)

```json
{
    "data": {
        "id": "wy5dn36",
        "name": "Grace Hopper",
        "email": "hopper@example.com",
        "rank": "Admin",
        "title": "Technologist Level III",
        "phone": "(705) 558-7119 x38444",
        "email_verified": true,
        "phone_verified": true,
        "blocked": false,
        "created_at": "2018-09-21T03:45:26+00:00",
    }
}
```

+ Response 403 (application/json)

```json
{
    "message": "This action is unauthorized."
}
```

### Block Member [DELETE /members/{hashid}/block]

Blocking a member removes their access without deleting their account.

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The member's database hashid

+ Response 204 (application/json)

```json

```

### Unblock Member [DELETE /members/{hashid}/unblock]

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The member's database hashid

+ Response 204 (application/json)

```json

```

### Delete Member [DELETE /members/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The member's database hashid

+ Response 204 (application/json)

```json

```

### Restore Member [DELETE /members/{hashid}/restore]

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The member's database hashid

+ Response 204 (application/json)

```json

```

### Fetch Member Permissions [GET /members/{hashid}/permissions]

View a members's [permission flags](/permissions.html). If none are set the default values are returned.

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The member's database hashid

+ Response 200 (application/json)

```json
{
    "data": {
        "notebooks.create": false,
        "notebooks.update": false,
        "notebooks.destroy": false,
  }
}
```

+ Response 403 (application/json)

```json
{
    "message": "This action is unauthorized."
}
```

### Update Member Permissions [PUT /members/{hashid}/permissions]

Update a user's [permission flags](/permissions.html).  You only need to submit the flags you want to have changed.

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The member's database hashid

+ Form Data

    + `permissions[]`: Key value pairs. Such as `permissons[notebook.create] = 1`

```json
{
    "data": {
        "notebooks.create": true,
        "notebooks.update": false,
        "notebooks.destroy": false,
  }
}
```

+ Response 403 (application/json)

```json
{
    "message": "This action is unauthorized."
}
```
