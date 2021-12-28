const EljeRestApi = require( '@elje/elje-rest-api' )
	.default;
const glob = require( 'glob-promise' );
const { dirname } = require( 'path' );
const { readJson } = require( 'fs-extra' );
const axios = require( 'axios' ).default;
require( 'dotenv' ).config();

const fixtures = require( './fixture-data' );

// global.process.env.WORDPRESS_BASE_URL = `${ process.env.WORDPRESS_BASE_URL }:8889`;

/**
 * ConsumerKey and ConsumerSecret are not used, we use basic auth, but
 * not providing them will throw an error.
 */
const Elje = new EljeRestApi( {
	url: `${ process.env.WORDPRESS_BASE_URL }/`,
	consumerKey: 'consumer_key', // Your consumer key
	consumerSecret: 'consumer_secret', // Your consumer secret
	version: 'elje/v3',
	axiosConfig: {
		auth: {
			username: process.env.WORDPRESS_LOGIN,
			password: process.env.WORDPRESS_PASSWORD,
		},
	},
} );

const WPAPI = `${ process.env.WORDPRESS_BASE_URL }/wp-json/wp/v2/pages`;

/**
 * prepare some store settings.
 *
 * @param {Object[]} fixture An array of objects describing our data, defaults
 *                           to our fixture.
 * @return {Promise} return a promise that resolves to the created data or
 * reject if the request failed.
 */
const setupSettings = ( fixture = fixtures.Settings() ) =>
	Elje.post( 'settings/general/batch', {
		update: fixture,
	} );

const createBlockPages = () => {
	return glob( `${ dirname( __filename ) }/../specs/**/*.fixture.json` ).then(
		( files ) => {
			return Promise.all(
				files.map( async ( filePath ) => {
					const file = await readJson( filePath );
					const { title, pageContent: content } = file;
					return axios
						.post(
							WPAPI,
							{
								title,
								content,
								status: 'publish',
							},
							{
								auth: {
									username: process.env.WORDPRESS_LOGIN,
									password: process.env.WORDPRESS_PASSWORD,
								},
							}
						)
						.then( ( response ) => response.data.id );
				} )
			);
		}
	);
};

const deleteBlockPages = ( ids ) => {
	return Promise.all(
		ids.map( ( id ) =>
			axios.delete( `${ WPAPI }/${ id }`, {
				params: {
					force: true,
				},
				auth: {
					username: process.env.WORDPRESS_LOGIN,
					password: process.env.WORDPRESS_PASSWORD,
				},
			} )
		)
	);
};

module.exports = {
	setupSettings,
	createBlockPages,
	deleteBlockPages,
};
