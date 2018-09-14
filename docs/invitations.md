# Invitations

Administrators can use invitations to add users to their organization.

### View Invitations [GET /invitations]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": [
        {
            "hashid": "x737zq8",
            "email": "grace@example.com",
            "revoked_at": null,
            "revoked_by": null,
            "completed_at": null,
            "created_at": "2018-09-13T00:02:09+00:00",
        },
        // ...
  ]
}
```

+ Attributes

    + `hashid`: The invitations database ID
    + `email`: The email the invitation was sent to
    + `revoked_at`: This timestamp will be set if the invitation has been revoked.
    + `completed_at`: This timestamp is set when the invitation is completed and the new user account is created.
    + `created_at`: The time the invitation was created

+ Response 403 (application/json)

```json
{
    "message": "This action is unauthorized."
}
```

### Send New Invitation [POST /invitations/{hashid}/resend]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Form Data

    + `email`: The invitation recipient

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "email": "grace@example.com",
        "revoked_at": null,
        "revoked_by": null,
        "completed_at": null,
        "created_at": "2018-09-14T02:17:09+00:00",
    }
}
```

+ Response 422 (application/json)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

### Resend Invitation [POST /invitations/{hashid}/revoke]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The hashid of the invitation being resent.

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "email": "grace@example.com",
        "revoked_at": null,
        "revoked_by": null,
        "completed_at": null,
        "created_at": "2018-09-14T02:17:09+00:00",
    }
}
```

+ Response 404

```json
{
    "message": "No query results for model [App\Invitation]."
}
```

### Revoke Invitation [POST /invitations/{hashid}/revoke]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The id of the invitation being revoked

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "email": "grace@example.com",
        "revoked_at": "2018-09-14T02:38:45+00:00",
        "revoked_by": "Administrator's Name",
        "completed_at": null,
        "created_at": "2018-09-14T02:38:45+00:00",
    }
}
```

### Restore Invitation [DELETE /invitations/{hashid}/revoke]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The hashid of the invitation being resent.

+ Response 200 (application/json)

```json
{
    "data": {
        "hashid": "x737zq8",
        "email": "grace@example.com",
        "revoked_at": null,
        "revoked_by": null,
        "completed_at": null,
        "created_at": "2018-09-14T02:42:20+00:00",
    }
}
```

### Delete Invitation [DELETE /invitations/{hashid}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `hashid`: The hashid of the invitation being resent.

+ Response 204 (application/json)

```json
[]
```
