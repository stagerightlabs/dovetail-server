# Session Management

Request new auth tokens or destroy existing ones. Tokens are good for 1 year.

### Login [POST /login]

+ Headers

    + Accept: application/json

+ Form Data

    + `email`: The user's email address
    + `password`: The user's password

+ Response 200 (application/json)

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
        "email": [
            "These credentials do not match our records."
        ]
  }
}
```

### Logout [POST /logout]

+ Headers

    + Accept: application/json
    + Authorization:  Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
  "token revoked": 200
}
```

+ Response 401 (application/json)

```json
{
    "message": "Unauthenticated."
}
```
