# User Permissions

Access levels are used internally to determine actions that can be performed by user accounts.  Permissions are similar, except that administrators have the ability to manage the permissions assigned to each of their member accounts.  This way, two different users in the same access level may have different permissions if the administrator deems it appropriate.

| Permission Key     | Default | Description                                    |
| :----------------- |:--------|:-----------------------------------------------|
| `notebooks.create` | false   | This user can create notebooks                 |
| `notebooks.update` | false   | This user can update notebooks                 |
| `notebooks.delete` | false   | This user can remove notebooks                 |
