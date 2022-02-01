const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const postcssPlugins = require( '@wordpress/postcss-plugins-preset' );
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const { hasPostCSSConfig } = require( '@wordpress/scripts/utils' );

const isProduction = process.env.NODE_ENV === 'production';

const cssLoaders = [
	{
		loader: MiniCssExtractPlugin.loader,
	},
	{
		loader: require.resolve( 'css-loader' ),
		options: {
			sourceMap: ! isProduction,
			modules: {
				auto: true,
			},
		},
	},
	{
		loader: require.resolve( 'postcss-loader' ),
		options: {
			...( ! hasPostCSSConfig() && {
				postcssOptions: {
					ident: 'postcss',
					plugins: postcssPlugins,
				},
			} ),
		},
	},
];

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( process.cwd(), 'src', 'index.tsx' ),
	},
	resolve: {
		...defaultConfig.resolve,
		extensions: ['.ts', '.tsx', '.js']
	},
	module: {
		...defaultConfig.module,
		rules: [
			{
				test: /\.(js|jsx|ts|tsx)$/,
				exclude: /node_modules/,
				use: [
					{
						loader: require.resolve( 'babel-loader' ),
						options: {
							cacheDirectory: process.env.BABEL_CACHE_DIRECTORY || true,
						},
					},
				],
			},
			{
				test: /\.css$/,
				use: cssLoaders,
			},
			{
				test: /\.(sc|sa)ss$/,
				use: [
					...cssLoaders,
					{
						loader: require.resolve( 'sass-loader' ),
						options: {
							sourceMap: ! isProduction,
						},
					},
				],
			},
		],
	},
};
