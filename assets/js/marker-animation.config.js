const SpeedMeasurePlugin            = require('speed-measure-webpack-plugin');
const DuplicatePackageCheckerPlugin = require('duplicate-package-checker-webpack-plugin');
const smp                           = new SpeedMeasurePlugin();
const webpack                       = require('webpack');
const pkg                           = require('./package');
const path                          = require('path');

const banner = `${pkg.name} ${pkg.version} - ${pkg.description}\nCopyright (c) ${new Date().getFullYear()} ${pkg.author}\nLicense: ${pkg.license}`;

const webpackConfig = {
  context: path.resolve(__dirname, 'src', 'marker-animation'),
  entry: './index.js',
  output: {
    path: __dirname,
    filename: 'marker-animation.min.js',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
      },
    ],
  },
  externals: {
    jquery: 'jQuery',
  },
  plugins: [
    new webpack.BannerPlugin(banner),
    new DuplicatePackageCheckerPlugin(),
  ],
};

module.exports = smp.wrap(webpackConfig);
