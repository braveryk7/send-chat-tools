const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin')

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( process.cwd(), 'src', 'index.tsx' ),
	},
	resolve: {
		...defaultConfig.resolve,
		alias: {
			"src": path.resolve(__dirname, "./src"),
		},
		extensions: ['.ts', '.tsx', '.js']
	},
	module: {
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.tsx?$/,
				exclude: /node_modules/,
				use: [
					{
						loader: 'thread-loader',
						options: {
							workers: require('os').cpus().length - 1,
						}
					},
					{
						loader: 'esbuild-loader',
						options: {
							loader: 'tsx',
							target: 'es2015',
						}
					},
				],
			},
		],
	},
	plugins: [
		...defaultConfig.plugins,
		new ForkTsCheckerWebpackPlugin(),
	],
};
