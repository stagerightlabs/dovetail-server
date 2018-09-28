# Domain Models

### Organizations

Organizations consist of a group of users who work together under the same institutional name. Generally speaking, all of the users in an Organization have access to the same Notebooks and Documents, though a user's ability to interact with those resources will depend on their permission settings.

When someone registers for an account on the main registration page they are creating a new Organization.  They also then become the default administrator for that Organization.  Certain [application settings](settings.html) set at the Organization level will automatically be shared with all Organization members.

### Invitations

Invitations are the means by which administrators can add members to their organization.  Sending an invitation to a new user allows them to register a new account that belongs to their new parent Organization.  By default, users who accept invitations are given standard member level permissions, but they can be upgraded to administrator-level permissions by another administrator.

Invitations can be revoked if they are sent erroneously, or resent if the invited user looses their acceptance link.

### Members

Members are a group of users that belong to the same Organization.  Administrators can view and manage all member profiles as well as changing user permissions or disabling accounts.

### Teams

Teams are subsets of users within the same Organization.  They can be created for any purpose and contain any number of members.  Teams can manage their own Notebooks and Documents.  The primary benefit of team membership is notification scope: when actions are taken within a Team's notebook only those team members will be notified instead of all users in the Organization.

Note:

- Users must have permission to create, edit or delete teams.
- Users must have permission to manage team memberships before they can add or remove members.
- Users cannot add or remove themselves from teams.

### Categories

Categories are custom labels that can be used to scope sets of organizational knowledge.  Primarily this entails grouping sets of Notebooks under the same genre of research, but the use of categories may be extended in the future.

Categories are managed by administrators but are visible to all organization members.

### Notebooks

Notebooks are sets of notes that revolve around the same experiment or set of experiments.  Users can create their own Notebooks or they can be shared by a Team or the entire Organization.

### Pages

A Page is one section of notes for a Notebook. They can contain as little or as much information as desired.  When viewing a Notebook, pages are displayed in the order of creation.

### Comments

Users who have access to a Notebook may add comments to any Page in a Notebook, provided that they have the proper access permissions and comments have been enabled for that notebook.   When comments are created every member who has access to that Notebook will be notified.

### Documents

Documents are file attachments associated with notebook pages.  Currently only images and PDF files are allowed.
