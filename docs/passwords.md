# Password Resets

Request and redeem password resets.

### Request Reset [POST /password/email]

+ Headers

    + Accept: application/json

+ Form Data

    + `email`: The email of the account requesting the reset. Required.

+ Response 200 (application/json)

```json
{
    "message": "We have e-mailed your password reset link!"
}
```

+ Response 422 (application/json)

```json
{
    "errors": {
        "email": "We can't find a user with that e-mail address."
    }
}
```

### Reset Password [POST /password/reset]

A valid request will return an updated auth token.

+ Headers

    + Accept: application/json

+ Form Data

    + `token`: The password reset code (delivered via email)
    + `email`: The email of the account that received the code
    + `password`: The new password for this account
    + `password_confirmation`: The new password again

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
    "errors": {
        "email": "This password reset token is invalid."
    }
}
```

+ Response 422 (application/json)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "password": [
            "The password confirmation does not match."
        ]
    }
}
```
