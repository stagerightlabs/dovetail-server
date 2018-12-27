module.exports = {
    title: 'Dovetail API',
    description: 'Endpoint Documentation',
    head: [
        ['link', { rel: 'icon', href: '/favicon.png' }]
    ],
    themeConfig: {
        repo: 'https://gitlab.com/stage-right-labs/lims-api',
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Specs', link: '/to-do.html' },
        ],
        sidebar: [
            {
                title: 'Planning',
                children: [
                    ['/to-do', 'To Do']
                ]
            },
            {
                title: 'Definitions',
                children: [
                    '/models',
                    '/settings',
                    '/permissions',
                    '/env'
                ]
            },
            {
                title: 'Accounts',
                children: [
                    '/registration',
                    '/accept-invitation',
                    '/session',
                    '/passwords',
                ]
            },
            {
                title: 'User Profile',
                children: [
                    ['/user-profile', 'Manage'],
                    '/verification',
                    ['/user-teams', 'Teams']
                ]
            },
            {
                title: 'Organizations',
                children: [
                    ['/organization', 'Summary'],
                    '/invitations',
                    '/members',
                    '/categories',
                    '/teams'
                ]
            },
            {
                title: 'Notebooks',
                children: [
                    '/notebooks',
                    '/pages',
                    ['/page-comments', 'Comments'],
                    ['/page-attachments', 'Documents']
                ]
            }
        ],
        displayAllHeaders: true, // Default: false
        lastUpdated: 'Last Updated', // string | boolean
    }
}
