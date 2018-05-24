var CopyWebpackPlugin = require('copy-webpack-plugin');
const path            = require("path");
const vendorroot      = path.resolve("./vendor/");
const publicroot      = path.resolve("./public/");
module.exports        = {
    entry: publicroot + "/assets/js/index.js",
    output: {
        path: publicroot + "/assets/js",
        filename: "bundle.js"
    },
    plugins: [
        new CopyWebpackPlugin(
            [
            { from: vendorroot + '/twbs/bootstrap/dist/css/*.*', to: publicroot + '/assets/css/[name].[ext]' },
            { from: vendorroot + '/twbs/bootstrap/dist/js/*.*', to: publicroot + '/assets/js/[name].[ext]' }
            ], { copyUnmodified: true }
        ),
    ]
};