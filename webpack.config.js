var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .addEntry('js/app', './assets/js/app.js')

    .addStyleEntry('css/app', './assets/sass/app.scss')

    .createSharedEntry('vendor', [
        './node_modules/jquery/dist/jquery.min.js',
        './node_modules/semantic-ui-css/semantic.min.js',
        './vendor/sylius/ui-bundle/Resources/private/js/sylius-toggle.js',
        './vendor/sylius/ui-bundle/Resources/private/js/sylius-auto-complete.js',
        './vendor/sylius/ui-bundle/Resources/private/js/sylius-bulk-action-require-confirmation.js',
        './vendor/sylius/ui-bundle/Resources/private/js/sylius-require-confirmation.js',
        './vendor/sylius/ui-bundle/Resources/private/js/sylius-form-collection.js',
        './vendor/sylius/ui-bundle/Resources/private/js/sylius-prototype-handler.js',

        './node_modules/semantic-ui-css/semantic.min.css',
        './assets/sass/main.scss'

    ])


    .enableSassLoader((options) => {
    },{
        resolveUrlLoader: true
    })
    // .enableSassLoader()
    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    })
;

module.exports = Encore.getWebpackConfig();
