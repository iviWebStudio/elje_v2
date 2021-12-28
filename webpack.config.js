/**
 * Internal dependencies
 */
const {NODE_ENV, FORCE_MAP, getAlias} = require('./bin/webpack-helpers.js');
const {
	getCoreConfig,
	getMainConfig,
	getFrontConfig,
	getStylingConfig,
} = require('./bin/webpack-configs.js');

// Only options shared between all configs should be defined here.
const sharedConfig = {
	mode: NODE_ENV,
	performance: {
		hints: false,
	},
	stats: {
		all: false,
		assets: true,
		builtAt: true,
		colors: true,
		errors: true,
		hash: true,
		timings: true,
	},
	watchOptions: {
		ignored: /node_modules/,
	},
	devtool: NODE_ENV==='development' || FORCE_MAP ? 'source-map':false,
};

// Core config for shared libraries.
const CoreConfig = {
	...sharedConfig,
	...getCoreConfig({alias: getAlias()}),
};

// Main Blocks config for registering Blocks and for the Editor.
const MainConfig = {
	...sharedConfig,
	...getMainConfig({
		alias: getAlias(),
	}),
};

// Frontend config for scripts used in the store itself.
const FrontendConfig = {
	...sharedConfig,
	...getFrontConfig({alias: getAlias()}),
};

/**
 * Config to generate the CSS files.
 */
const StylingConfig = {
	...sharedConfig,
	...getStylingConfig({alias: getAlias()}),
};

module.exports = [
	CoreConfig,
	MainConfig,
	FrontendConfig,
	StylingConfig,
];
