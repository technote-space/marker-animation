const webpack = require('webpack');
const p = require('./package');

const banner = `${p.name}-gutenberg ${p.version}\nCopyright (c) ${ new Date().getFullYear() } ${p.author}\nLicense: ${p.license}`;

const webpackConfig = {
    context: __dirname + '/src/gutenberg',
    entry: './index.js',
    output: {
        path: __dirname,
        filename: `${p.name}-gutenberg.min.js`
    },
    module: {
        rules: [{
            test: /\.js$/,
            exclude: /node_modules/,
            loader: 'babel-loader'
        }]
    },
    externals: {
        lodash: 'lodash'
    },
    plugins: [
        new webpack.BannerPlugin(banner),
        new webpack.DefinePlugin({
            'PLUGIN_NAME': JSON.stringify(`${p.name}`),
            'PARAMETER_NAME': JSON.stringify('marker_animation_params')
        })
    ]
};

module.exports = webpackConfig;