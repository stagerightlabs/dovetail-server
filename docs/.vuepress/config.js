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
            { text: 'Guide', link: '/to-do.html' },
        ],
        sidebar: [
            {
                title: 'Planning',
                collapsable: false,
                children: [
                    ['/to-do', 'To Do']
                ]
            },
            {
                title: 'Accounts',
                children: [
                    '/registration',
                    '/session',
                    '/passwords'
                ]
            }
        ],
        displayAllHeaders: true, // Default: false
        lastUpdated: 'Last Updated', // string | boolean
    }
}
