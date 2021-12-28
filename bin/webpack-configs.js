/**
 * External dependencies
 */
const path = require('path');
const {kebabCase} = require('lodash');
const RemoveFilesPlugin = require('./remove-files-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const ProgressBarPlugin = require('progress-bar-webpack-plugin');
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');
const WebpackRTLPlugin = require('webpack-rtl-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CreateFileWebpack = require('create-file-webpack');
const CircularDependencyPlugin = require('circular-dependency-plugin');

/**
 * Internal dependencies
 */
const {getEntryConfig} = require('./webpack-entries');
const {
	NODE_ENV,
	FORCE_MAP,
	CHECK_CIRCULAR_DEPS,
	requestToExternal,
	requestToHandle,
	findModuleMatch,
	getProgressBarPluginConfig,
} = require('./webpack-helpers');

const isProduction = NODE_ENV==='production';

/**
 * Shared config for all script builds.
 */
const sharedPlugins = [
	CHECK_CIRCULAR_DEPS==='true'
		? new CircularDependencyPlugin({
			exclude: /node_modules/,
			cwd: process.cwd(),
			failOnError: 'warn',
		})
		:false,
	new DependencyExtractionWebpackPlugin({
		injectPolyfill: true,
		requestToExternal,
		requestToHandle,
	}),
].filter(Boolean);

/**
 * Build config for core packages.
 *
 * @param {Object} options Build options.
 */
const getCoreConfig = (options = {}) => {
	const {alias, resolvePlugins = []} = options;
	const resolve = alias
		? {
			alias,
			plugins: resolvePlugins,
		}
		:{
			plugins: resolvePlugins,
		};
	return {
		entry: getEntryConfig('core', options.exclude || []),
		output: {
			filename: (chunkData) => {
				return `${ kebabCase(chunkData.chunk.name) }.js`;
			},
			path: path.resolve(__dirname, '../build/'),
			library: ['elje', '[name]'],
			libraryTarget: 'this',
			jsonpFunction: 'webpackWcBlocksJsonp',
		},
		module: {
			rules: [
				{
					test: /\.(t|j)sx?$/,
					exclude: /node_modules/,
					use: {
						loader: 'babel-loader?cacheDirectory',
						options: {
							presets: ['@wordpress/babel-preset-default'],
						},
					},
				},
				{
					test: /\.s[c|a]ss$/,
					use: {
						loader: 'ignore-loader',
					},
				},
			],
		},
		plugins: [
			...sharedPlugins,
			new ProgressBarPlugin(
				getProgressBarPluginConfig('Core', options.fileSuffix),
			),
			new CreateFileWebpack({
				path: './',
				// file name
				fileName: 'blocks.ini',
				// content of the file
				content: `
elje_blocks_phase = ${ process.env.ELJE_BLOCKS_PHASE || 3 }
elje_blocks_env = ${ NODE_ENV }
`.trim(),
			}),
		],
		optimization: {
			splitChunks: {
				automaticNameDelimiter: '--',
			},
			minimizer: [
				new TerserPlugin({
					cache: true,
					parallel: true,
					sourceMap: !!FORCE_MAP || !isProduction,
					terserOptions: {
						output: {
							comments: /translators:/i,
						},
						compress: {
							passes: 2,
						},
						mangle: {
							reserved: ['__', '_n', '_nx', '_x'],
						},
					},
					extractComments: false,
				}),
			],
		},
		resolve: {
			...resolve,
			extensions: ['.js', '.ts', '.tsx'],
		},
	};
};

/**
 * Build config for Blocks in the editor context.
 *
 * @param {Object} options Build options.
 */
const getMainConfig = (options = {}) => {
	let {fileSuffix} = options;
	const {alias, resolvePlugins = []} = options;
	fileSuffix = fileSuffix ? `-${ fileSuffix }`:'';
	const resolve = alias
		? {
			alias,
			plugins: resolvePlugins,
		}
		:{
			plugins: resolvePlugins,
		};
	return {
		entry: getEntryConfig('main', options.exclude || []),
		output: {
			devtoolNamespace: 'elje',
			path: path.resolve(__dirname, '../build/'),
			chunkFilename: `[name]${ fileSuffix }.js?ver=[contenthash]`,
			filename: `[name]${ fileSuffix }.js`,
			library: ['elje', 'blocks', '[name]'],
			libraryTarget: 'this',
			jsonpFunction: 'webpackWcBlocksJsonp',
		},
		module: {
			rules: [
				{
					test: /\.(j|t)sx?$/,
					exclude: /node_modules/,
					use: {
						loader: 'babel-loader?cacheDirectory',
						options: {
							presets: ['@wordpress/babel-preset-default'],
							plugins: [
								isProduction
									? require.resolve(
									'babel-plugin-transform-react-remove-prop-types',
									)
									:false,
							].filter(Boolean),
						},
					},
				},
				{
					test: /\.s[c|a]ss$/,
					use: {
						loader: 'ignore-loader',
					},
				},
			],
		},
		optimization: {
			splitChunks: {
				minSize: 0,
				automaticNameDelimiter: '--',
				cacheGroups: {
					commons: {
						test: /[\\/]node_modules[\\/]/,
						name: 'elje-blocks-vendors',
						chunks: 'all',
						enforce: true,
					},
				},
			},
			minimizer: [
				new TerserPlugin({
					cache: true,
					parallel: true,
					sourceMap: !!FORCE_MAP || !isProduction,
					terserOptions: {
						output: {
							comments: /translators:/i,
						},
						compress: {
							passes: 2,
						},
						mangle: {
							reserved: ['__', '_n', '_nx', '_x'],
						},
					},
					extractComments: false,
				}),
			],
		},
		plugins: [
			...sharedPlugins,
			new ProgressBarPlugin(
				getProgressBarPluginConfig('Main', options.fileSuffix),
			),
		],
		resolve: {
			...resolve,
			extensions: ['.js', '.jsx', '.ts', '.tsx'],
		},
	};
};

/**
 * Build config for Blocks in the frontend context.
 *
 * @param {Object} options Build options.
 */
const getFrontConfig = (options = {}) => {
	let {fileSuffix} = options;
	const {alias, resolvePlugins = []} = options;
	fileSuffix = fileSuffix ? `-${ fileSuffix }`:'';
	const resolve = alias
		? {
			alias,
			plugins: resolvePlugins,
		}
		:{
			plugins: resolvePlugins,
		};
	return {
		entry: getEntryConfig('frontend', options.exclude || []),
		output: {
			devtoolNamespace: 'elje',
			path: path.resolve(__dirname, '../build/'),
			chunkFilename: `[name]-frontend${ fileSuffix }.js?ver=[contenthash]`,
			filename: `[name]-frontend${ fileSuffix }.js`,
			jsonpFunction: 'webpackWcBlocksJsonp',
		},
		module: {
			rules: [
				{
					test: /\.(j|t)sx?$/,
					exclude: /node_modules/,
					use: {
						loader: 'babel-loader?cacheDirectory',
						options: {
							presets: [
								[
									'@wordpress/babel-preset-default',
									{
										modules: false,
										targets: {
											browsers: [
												'extends @wordpress/browserslist-config',
											],
										},
									},
								],
							],
							plugins: [
								isProduction
									? require.resolve(
									'babel-plugin-transform-react-remove-prop-types',
									)
									:false,
							].filter(Boolean),
						},
					},
				},
				{
					test: /\.s[c|a]ss$/,
					use: {
						loader: 'ignore-loader',
					},
				},
			],
		},
		optimization: {
			splitChunks: {
				automaticNameDelimiter: '--',
			},
			minimizer: [
				new TerserPlugin({
					cache: true,
					parallel: true,
					sourceMap: !!FORCE_MAP || !isProduction,
					terserOptions: {
						output: {
							comments: /translators:/i,
						},
						compress: {
							passes: 2,
						},
						mangle: {
							reserved: ['__', '_n', '_nx', '_x'],
						},
					},
					extractComments: false,
				}),
			],
		},
		plugins: [
			...sharedPlugins,
			new ProgressBarPlugin(
				getProgressBarPluginConfig('Frontend', options.fileSuffix),
			),
		],
		resolve: {
			...resolve,
			extensions: ['.js', '.ts', '.tsx'],
		},
	};
};

/**
 * Build config for CSS Styles.
 *
 * @param {Object} options Build options.
 */
const getStylingConfig = (options = {}) => {
	let {fileSuffix} = options;
	const {alias, resolvePlugins = []} = options;
	fileSuffix = fileSuffix ? `-${ fileSuffix }`:'';
	const resolve = alias
		? {
			alias,
			plugins: resolvePlugins,
		}
		:{
			plugins: resolvePlugins,
		};
	return {
		entry: getEntryConfig('styling', options.exclude || []),
		output: {
			devtoolNamespace: 'elje',
			path: path.resolve(__dirname, '../build/'),
			filename: `[name]-style${ fileSuffix }.js`,
			library: ['elje', 'blocks', '[name]'],
			libraryTarget: 'this',
			jsonpFunction: 'webpackWcBlocksJsonp',
		},
		optimization: {
			splitChunks: {
				minSize: 0,
				automaticNameDelimiter: '--',
				cacheGroups: {
					editorStyle: {
						// Capture all `editor` stylesheets and editor-components stylesheets.
						test: (module = {}) =>
							module.constructor.name==='CssModule' &&
							(findModuleMatch(module, /editor\.scss$/) ||
								findModuleMatch(
									module,
									/[\\/]assets[\\/]js[\\/]editor-components[\\/]/,
								)),
						name: 'elje-blocks-editor-style',
						chunks: 'all',
						priority: 10,
					},
					vendorsStyle: {
						test: /\/node_modules\/.*?style\.s?css$/,
						name: 'elje-blocks-vendors-style',
						chunks: 'all',
						priority: 7,
					},
					blocksStyle: {
						// Capture all stylesheets with name `style` or name that starts with underscore (abstracts).
						test: /(style|_.*)\.scss$/,
						name: 'elje-blocks-style',
						chunks: 'all',
						priority: 5,
					},
				},
			},
		},
		module: {
			rules: [
				{
					test: /\/node_modules\/.*?style\.s?css$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {importLoaders: 1},
						},
						'postcss-loader',
						{
							loader: 'sass-loader',
							options: {
								sassOptions: {
									includePaths: ['node_modules'],
								},
								additionalData: (content) => {
									const styleImports = [
										'colors',
										'breakpoints',
										'variables',
										'mixins',
										'animations',
										'z-index',
									].map(
										(imported) =>
											`@import "~@wordpress/base-styles/${ imported }";`,
									).join(' ');
									return styleImports + content;
								},
							},
						},
					],
				},
				{
					test: /\.s?css$/,
					exclude: /node_modules/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {importLoaders: 1},
						},
						'postcss-loader',
						{
							loader: 'sass-loader',
							options: {
								implementation: require("sass"),
								sassOptions: {
									includePaths: ['assets/css/abstracts'],
								},
							},
						},
					],
				},
			],
		},
		plugins: [
			new ProgressBarPlugin(
				getProgressBarPluginConfig('Styles', options.fileSuffix),
			),
			new WebpackRTLPlugin({
				filename: `[name]${ fileSuffix }-rtl.css`,
				minify: {
					safe: true,
				},
			}),
			new MiniCssExtractPlugin({
				filename: `[name]${ fileSuffix }.css`,
			}),
			// Remove JS files generated by MiniCssExtractPlugin.
			new RemoveFilesPlugin(`./build/*style${ fileSuffix }.js`),
		],
		resolve: {
			...resolve,
			extensions: ['.js', '.ts', '.tsx'],
		},
	};
};

module.exports = {
	getCoreConfig,
	getFrontConfig,
	getMainConfig,
	getStylingConfig,
};
