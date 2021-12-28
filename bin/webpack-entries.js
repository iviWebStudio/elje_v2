/**
 * External dependencies
 */
const { omit } = require( 'lodash' );
const glob = require( 'glob' );

const blocks = {
	'post-card': {},
};

const getBlockEntries = ( relativePath ) => {
	const experimental =
		! parseInt( process.env.ELJE_BLOCKS_PHASE, 10 ) < 3;

	return Object.fromEntries(
		Object.entries( blocks )
		.filter(
			( [ , config ] ) =>
				! config.isExperimental ||
				config.isExperimental === experimental
		)
		.map( ( [ blockCode, config ] ) => {
			const filePaths = glob.sync(
				`./assets/js/blocks/${ config.customDir || blockCode }/` +
				relativePath
			);
			if ( filePaths.length > 0 ) {
				return [ blockCode, filePaths ];
			}
			return null;
		} )
		.filter( Boolean )
	);
};

const entries = {
	styling: {
		'general-style': glob.sync( './assets/**/*.scss', {
			ignore: [
				// Block styles are added below.
				'./assets/js/blocks/*/*.scss',
			],
		} ),
		...getBlockEntries( '**/*.scss' ),
	},
	core: {
		eljeBlocksRegistry: './assets/js/blocks-registry/index.js',
		eljeSettings: './assets/js/settings/shared/index.ts',
		eljeBlocksData: './assets/js/data/index.ts',
		eljeBlocksMiddleware: './assets/js/middleware/index.js',
		eljeBlocksSharedContext: './assets/js/shared/context/index.js',
		eljeBlocksSharedHocs: './assets/js/shared/hocs/index.js',
	},
	main: {
		// Shared blocks code
		'elje-blocks': './assets/js/index.js',
		...getBlockEntries( 'index.{t,j}s{,x}' ),
	},
	frontend: {
		...getBlockEntries( 'frontend.{t,j}s{,x}' ),
	},
};

const getEntryConfig = ( type = 'main', exclude = [] ) => {
	return omit( entries[ type ], exclude );
};

module.exports = {
	getEntryConfig,
};
