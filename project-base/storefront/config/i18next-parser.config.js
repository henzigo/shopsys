module.exports = {
    createOldCatalogs: false,
    defaultNamespace: 'common',
    indentation: 4,
    keepRemoved: false,
    useKeysAsDefaultValue: (locale) => locale === 'en',
    lexers: {
        jsx: [
            {
                lexer: 'JsxLexer',
                attr: 'i18nKey', // Attribute for the keys
            },
        ],
    },
    lineEnding: 'lf',
    locales: ['en', 'cs', 'sk'],
    output: 'public/locales/$LOCALE/$NAMESPACE.json',
    namespaceSeparator: false,
    keySeparator: false,
    input: [
        '../components/**/*.{ts,tsx}',
        '../connectors/**/*.{ts,tsx}',
        '../gtm/**/*.{ts,tsx}',
        '../pages/**/*.{ts,tsx}',
        '../store/**/*.{ts,tsx}',
        '../utils/**/*.{ts,tsx}',
    ],
    sort: true,
    verbose: false,
};
