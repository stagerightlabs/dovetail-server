# Registration

### Register [POST /register]

Create a new organization and a default administrator for that organization.

+ Headers

    + Accept: application/json

+ Form Data

    + `name`: The new user's name.  Required.
    + `organization`: The new organization's name.  Required.
    + `email`: The new user's email address. Required.
    + `password`: The new user's password. Required.
    + `password_confirmation`: The new user's password again. Required.

+ Response 200 (application/json)

A valid request will return an auth token.

```json
{
    "token_type": "Bearer"
    "expires_in": 31536000
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
    "refresh_token": "def50200641f136a0811..."
}
```

+ Response 422 (application/json)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "organization": [
            "The organization field is required."
        ],
        "email": [
            "The email field is required."
        ]
    }
}
```
