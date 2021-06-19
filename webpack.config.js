const path = require( 'path' );
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");

module.exports = {
    mode: "development",

    entry: './src/main.ts',

    output: {
        path: path.resolve( __dirname, 'dist' ),
        publicPath: '',
        filename: 'main.js',
    },

    module: {
        rules: [
            {
                test: /\.ts$/,
                use: 'ts-loader',
            },
            {
                test: /\.(scss|sass|css)$/i,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                    },
                    {
                        loader: "css-loader",
                        options: {
                            url: true,
                            sourceMap: true,
                        },
                    },
                    {
                        loader: "sass-loader",
                        options: {
                            implementation: require('sass'),
                            sassOptions: {
                                fiber: require('fibers'),
                            },
                            sourceMap: true,
                        },
                    },
                ],
            },
            {
                test: /\.(gif|png|jpe?g|svg)$/,
                type: "asset/inline",
            }
        ],
    },

    resolve: {
        extensions: [
            '.ts', '.js'
        ],
    },

    plugins: [
        new MiniCssExtractPlugin({
            filename: "css/style.css",
        }),
        new CleanWebpackPlugin({
            cleanStaleWebpackAssets: false,
        }),
    ],

    devtool: "source-map",

    watchOptions: {
        ignored: /node_modules/
    },

    target: ["web", "es5"],
}
