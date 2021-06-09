const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

module.exports = {
	entry: './src/main.ts',
	output: {
		filename: 'main.js',
		path: path.resolve( __dirname, 'dist' ),
	},
	module: {
		rules: [
			{
				test: /\.ts$/,
				use: 'ts-loader',
			},
			{
				test: /\.scss$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					{
						loader: 'css-loader',
						options: {
							url: false,
							sourceMap: true,
						},
					},
					{
						loader: 'sass-loader',
						options: {
							implementation: require( 'sass' ),
							sassOptions: {
								fiber: require( 'fibers' ),
							},
							sourceMap: true,
						},
					},
				],
			},
		],
	},

	plugins: [
		new MiniCssExtractPlugin( {
			filename: 'css/style.css',
		} ),
	],
	devtool: 'source-map',
	watchOptions: {
		ignored: /node_modules/,
	},
	resolve: {
		extensions: [ '.ts', '.js' ],
	},
};
