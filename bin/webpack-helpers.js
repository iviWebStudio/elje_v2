/* eslint-disable no-console */
/**
 * External dependencies
 */
const path = require( 'path' );
const chalk = require( 'chalk' );
const NODE_ENV = process.env.NODE_ENV || 'development';
const FORCE_MAP = process.env.FORCE_MAP || false;
const CHECK_CIRCULAR_DEPS = process.env.CHECK_CIRCULAR_DEPS || false;

// If a package is not available, or missing functionality, in an old but __supported__ version of WordPress, it should be listed here.
// Some packages are not available in legacy versions of WordPress, so we don't want to extract them.
const requiredPackagesInWPLegacy = [];

const eljeDepMap = {
	'@elje/blocks-registry': [ 'elje', 'eljeBlocksRegistry' ],
	'@elje/settings': [ 'elje', 'eljeSettings' ],
	'@elje/block-data': [ 'elje', 'eljeBlocksData' ],
	'@elje/shared-context': [ 'elje', 'eljeBlocksSharedContext' ],
	'@elje/shared-hocs': [ 'elje', 'eljeBlocksSharedHocs' ],
	'@elje/price-format': [ 'elje', 'priceFormat' ],
	'@elje/blocks-checkout': [ 'elje', 'blocksCheckout' ],
};

const eljeHandleMap = {
	'@elje/blocks-registry': 'elje-blocks-registry',
	'@elje/settings': 'elje-settings',
	'@elje/block-settings': 'elje-settings',
	'@elje/block-data': 'elje-blocks-data-store',
	'@elje/shared-context': 'elje-blocks-shared-context',
	'@elje/shared-hocs': 'elje-blocks-shared-hocs',
	'@elje/price-format': 'elje-price-format',
	'@elje/blocks-checkout': 'elje-blocks-checkout',
};

const getAlias = ( options = {} ) => {
	let { pathPart } = options;
	pathPart = pathPart ? `${ pathPart }/` : '';
	return {
		'@elje/atomic-blocks': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }atomic/blocks`
		),
		'@elje/atomic-utils': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }atomic/utils`
		),
		'@elje/base-components': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }base/components/`
		),
		'@elje/base-context': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }base/context/`
		),
		'@elje/base-hocs': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }base/hocs/`
		),
		'@elje/base-hooks': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }base/hooks/`
		),
		'@elje/base-utils': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }base/utils/`
		),
		'@elje/editor-components': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }editor-components/`
		),
		'@elje/block-hocs': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }hocs`
		),
		'@elje/blocks-registry': path.resolve(
			__dirname,
			'../assets/js/blocks-registry'
		),
		'@elje/block-settings': path.resolve(
			__dirname,
			'../assets/js/settings/blocks'
		),
		'@elje/icons': path.resolve( __dirname, `../assets/js/icons` ),
		'@elje/resource-previews': path.resolve(
			__dirname,
			`../assets/js/${ pathPart }previews/`
		),
		'@elje/types': path.resolve( __dirname, `../assets/js/types/` ),
	};
};

function findModuleMatch( module, match ) {
	if ( module.request && match.test( module.request ) ) {
		return true;
	} else if ( module.issuer ) {
		return findModuleMatch( module.issuer, match );
	}
	return false;
}

const requestToExternal = ( request ) => {
	if ( requiredPackagesInWPLegacy.includes( request ) ) {
		return false;
	}
	if ( eljeDepMap[ request ] ) {
		return eljeDepMap[ request ];
	}
};

const requestToHandle = ( request ) => {
	if ( requiredPackagesInWPLegacy.includes( request ) ) {
		return false;
	}
	if ( eljeHandleMap[ request ] ) {
		return eljeHandleMap[ request ];
	}
};

const getProgressBarPluginConfig = ( name, fileSuffix ) => {
	const isLegacy = fileSuffix && fileSuffix === 'legacy';
	const progressBarPrefix = isLegacy ? 'Legacy ' : '';
	return {
		format:
			chalk.blue( `Building ${ progressBarPrefix }${ name }` ) +
			' [:bar] ' +
			chalk.green( ':percent' ) +
			' :msg (:elapsed seconds)',
		summary: false,
		customSummary: ( time ) => {
			console.log(
				chalk.green.bold(
					`${ progressBarPrefix }${ name } assets build completed (${ time })`
				)
			);
		},
	};
};

module.exports = {
	NODE_ENV,
	FORCE_MAP,
	CHECK_CIRCULAR_DEPS,
	getAlias,
	findModuleMatch,
	requestToHandle,
	requestToExternal,
	getProgressBarPluginConfig,
};
