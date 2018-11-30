# Email Verification

### Resend Verification Link [GET /email/resend]

+ Headers

    + Accept: application/json
    + Authorization: Bearer eyJ0eXAiOiJKV1Q...

+ Response 200 (application/json)

```json
{
  "message": "A fresh verification link has been sent to your email address."
}
```

+ Response 401 (application/json)

```json
{
    "message": "Unauthenticated."
}
```

### Verify [GET /email/verify/{code}]

+ Headers

    + Accept: application/json

+ Parameters

    + `code`: The user's email verification code

+ Response 200 (application/json)

```json
{
    "message": "Thank you for verifying your email address"
}
```

+ Response 422 (application/json)

```json
{
    "message": "There was a problem."
}
```

+ Response 401 (application/json)

```json
{
    "message": "Unauthenticated."
}
```
