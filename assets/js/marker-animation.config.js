const webpack = require('webpack');
const p = require('./package');

const banner = `${p.name} ${p.version} - ${p.description}\nCopyright (c) ${ new Date().getFullYear() } ${p.author}\nLicense: ${p.license}`;

const webpackConfig = {
    context: __dirname + '/src/marker-animation',
    entry: './index.js',
    output: {
        path: __dirname,
        filename: `${p.name}.min.js`
    },
    module: {
        rules: [{
            test: /\.js$/,
            exclude: /node_modules/,
            loader: 'babel-loader'
        }]
    },
    externals: {
        jquery: 'jQuery'
    },
    plugins: [
        new webpack.BannerPlugin(banner)
    ]
};

module.exports = webpackConfig;