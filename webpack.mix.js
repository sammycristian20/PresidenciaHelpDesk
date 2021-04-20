const mix = require('laravel-mix');
const webpack = require('webpack');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
	.js('resources/assets/js/admin.js', 'public/js')
	.js('resources/assets/js/agent.js', 'public/js')
  .js('resources/assets/js/navigation.js', 'public/js')
	.js('resources/assets/js/headerMenu.js', 'public/js')

  //make plugin bundles dynamic(should work without hardcoding)
  .js('app/Plugins/Ldap/views/js/ldap.js', 'public/js')
  .js('app/Plugins/Facebook/views/js/facebook.js', 'public/js')
  .js('app/Plugins/Calendar/views/js/calendar.js', 'public/js')
  .js('app/Plugins/Whatsapp/views/js/whatsapp.js', 'public/js')
  .js('app/Plugins/Chat/views/js/chat.js', 'public/js')
	.js('app/FaveoLog/views/js/faveoLog.js', 'public/js')
  .js('app/Bill/views/js/faveoBilling.js', 'public/js')
	.js('app/Plugins/ServiceDesk/views/js/serviceDesk.js', 'public/js')
	.js('app/FaveoReport/views/js/faveoReport.js', 'public/js')
  .js('app/Plugins/Telephony/views/js/telephony.js', 'public/js')
  .js('app/Plugins/Twitter/views/js/twitter.js', 'public/js')
    .js('app/Plugins/AzureActiveDirectory/views/js/azureActiveDirectory.js', 'public/js')
    .js('app/Plugins/AllianceCRM/views/js/allianceCRM.js', 'public/js')
    .js('app/Plugins/CRTWorkflow/views/js/crtWorkflow.js', 'public/js')
  .js('app/AutoAssign/views/js/autoAssign.js', 'public/js')
  .sass('resources/assets/sass/app.scss', 'public/css');

mix.copy('node_modules/tinymce/skins', 'public/js/skins');

mix.webpackConfig({
    resolve: {
        modules: [
          path.resolve(__dirname, 'resources/assets/js'),
          path.resolve(__dirname, 'resources/assets/store'),
          path.resolve(__dirname, 'node_modules'),
        ],
				alias : {
						faveoLog : path.resolve(__dirname, 'app/FaveoLog/views/js'),
						faveoReport : path.resolve(__dirname, 'app/FaveoReport/views/js'),
            faveoBilling : path.resolve(__dirname, 'app/Bill/views/js'),
            serviceDesk : path.resolve(__dirname, 'app/Plugins/ServiceDesk/views/js'),
            telephony : path.resolve(__dirname, 'app/Plugins/Telephony/views/js'),
            Calendar: path.resolve(__dirname,'app/Plugins/Calendar/views/js'),
            crtWorkflow: path.resolve(__dirname,'app/Plugins/CRTWorkflow/views/js'),
				}
    },
    node:{
      fs:'empty'
    },
    output: {
        publicPath: "/"
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

   plugins: [
   	 	new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
      new webpack.optimize.CommonsChunkPlugin({
        name: "common",
        filename: "js/common.js",
        minChunks: 2
      })
   ],
})
