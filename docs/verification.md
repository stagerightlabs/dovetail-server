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

### Verify [GET /email/verify/{id}]

+ Headers

    + Accept: application/json

+ Parameters

    + `id`: The user's id

+ Response 200 (application/json)

```json
{
    "message": "Thank you for verifying your email address"
}
```

+ Response 401 (application/json)

```json
{
    "message": "Unauthenticated."
}
```