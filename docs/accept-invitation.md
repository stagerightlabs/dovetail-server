# Accepting Invitations

### Confirm Invitation Status [GET /invitations/{code}/confirm]

+ Headers

    + Accept: application/json

+ Parameters

    + `code`: The invitation's redemption code

+ Response 200 (application/json)

```json
{
    "data": {
        "email": "wisoky.assunta@example.net",
        "code": "YMKGEWXMZVQRPBZL",
        "invited-by": "Hopper Labs"
    }
}
```

+ Response 404 (application/json)

```json
{
    "message": "No query results for model [App\Invitation]"
}
```

### Redeem Invitation [POST /invitations/{code}/redeem]

+ Headers

    + Accept: application/json

+ Parameters

    + `code`: The invitation's redemption code

+ Form Data

    + `name`: The new user's name
    + `password`: The new user's password
    + `password_confirmation`: The new user's password again

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
        "name": ["The name field is required."]
    }
}
```
