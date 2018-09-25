# User Permissions

Access levels are used internally to determine actions that can be performed by user accounts.  Permissions are similar, except that administrators have the ability to manage the permissions assigned to each of their member accounts.  This way, two different users in the same access level may have different permissions if the administrator deems it appropriate.

| Permission Key     | Default | Description                                    |
| :----------------- |:--------|:-----------------------------------------------|
| `teams.create`     | false   | This user can create teams                     |
| `teams.update`     | false   | This user can update teams                     |
| `teams.membership` | false   | This user can manage their team's memberships  |
| `teams.delete`     | false   | This user can remove teams                     |
| `notebooks.create` | false   | This user can create notebooks                 |
| `notebooks.update` | false   | This user can update notebooks                 |
| `notebooks.delete` | false   | This user can remove notebooks                 |
| `notebooks.pages`  | false   | This user can edit notebook pages              |
