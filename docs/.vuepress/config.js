module.exports = {
    title: '🚀 LIMS API',
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
                    '/permissions',
                    '/settings'
                ]
            },
            {
                title: 'Accounts',
                children: [
                    '/registration',
                    '/accept-invitation',
                    '/session',
                    '/passwords',
                    '/verification',
                    '/user',
                ]
            },
            {
                title: 'Organizations',
                children: [
                    ['/organization', 'Summary'],
                    '/invitations',
                    '/members'
                ]
            }
        ],
        displayAllHeaders: true, // Default: false
        lastUpdated: 'Last Updated', // string | boolean
    }
}
