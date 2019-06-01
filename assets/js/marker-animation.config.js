const webpack = require( 'webpack' );
const pkg = require( './package' );

const banner = `${ pkg.name } ${ pkg.version } - ${ pkg.description }\nCopyright (c) ${ new Date().getFullYear() } ${ pkg.author }\nLicense: ${ pkg.license }`;

const webpackConfig = {
	context: __dirname + '/src/marker-animation',
	entry: './index.js',
	output: {
		path: __dirname,
		filename: `${ pkg.name }.min.js`,
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
		new webpack.BannerPlugin( banner ),
	],
};

module.exports = webpackConfig;