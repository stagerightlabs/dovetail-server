# User Profile

### View Profile [GET /user]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": {
        "name": "Grace Hopper",
        "email": "grace@example.org",
        "email_verified_at": "2018-09-10T23:09:31+00:00",
        "phone": "907.748.2258 x7660",
        "phone_verified_at": "2018-09-10T23:09:31+00:00",
        "teams": [
            {
                "hashid": "wy5dn36",
                "name": "Red Team"
            },
            // ..
        ]
    }
}
```

### Update Profile [PUT /user]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Form Data

    + `name`: The user's name
    + `email`: The user's email. Changing this value will require re-verification.
    + `phone`: The user's phone number. Changing this value will require re-verification.

+ Response 200 (application/json)

```json
{
    "data": {
        "name": "Grace Hopper",
        "email": "grace@example.org",
        "email_verified_at": "2018-09-10T23:09:31+00:00",
        "phone": "907.748.2258 x7660",
        "phone_verified_at": "2018-09-10T23:09:31+00:00",
    }
}
```

### Read User Permission [GET /user/{permission}]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Parameters

    + `permission`: The [permission key](/permissions.html) being referenced

+ Response 200 (application/json)

```json
{
    "data": {
        "key": "notebooks.create",
        "allowed": true
    }
}
```

### User Admin Status [GET /user/admin]

Does the current user have administrator level access within their organization?

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": {
        "admin": true
    }
}
```

### User Read Only Status [GET /user/readonly]

Should the current user be restricted to "read only" access to their account?

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
    "data": {
        "readonly": true
    }
}
```
