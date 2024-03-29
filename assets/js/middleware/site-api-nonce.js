import apiFetch
	from '@wordpress/api-fetch';

// Stores the current nonce for the middleware.
let currentNonce = '';
let currentTimestamp = 0;

try {
	const storedNonceValue = window.localStorage.getItem('eljeApiNonce');
	const storedNonce = storedNonceValue ? JSON.parse(storedNonceValue):{};
	currentNonce = storedNonce?.nonce || '';
	currentTimestamp = storedNonce?.timestamp || 0;
} catch {
	// We can ignore an error from JSON parse.
}

/**
 * Returns whether or not this is a elje/store API request.
 *
 * @param {Object} options Fetch options.
 *
 * @return {boolean} Returns true if this is a store request.
 */
const isSiteApiRequest = (options) => {
	const url = options.url || options.path;
	if (!url || !options.method || options.method==='GET') {
		return false;
	}
	return /elje\/store\//.exec(url)!==null;
};

/**
 * Set the current nonce from a header object.
 *
 * @param {Object} headers Headers object.
 */
const setNonce = (headers) => {
	const nonce =
		typeof headers?.get==='function'
			? headers.get('X-ELJE-Store-API-Nonce')
			:headers['X-ELJE-Store-API-Nonce'];
	const timestamp =
		typeof headers?.get==='function'
			? headers.get('X-ELJE-Store-API-Nonce-Timestamp')
			:headers['X-ELJE-Store-API-Nonce-Timestamp'];

	if (nonce) {
		updateNonce(nonce, timestamp);
	}
};

/**
 * Updates the stored nonce within localStorage so it is persisted between page loads.
 *
 * @param {string} nonce Incoming nonce string.
 * @param {number} timestamp Timestamp from server of nonce.
 */
const updateNonce = (nonce, timestamp) => {
	// If the "new" nonce matches the current nonce, we don't need to update.
	if (nonce===currentNonce) {
		return;
	}

	// Only update the nonce if newer. It might be coming from cache.
	if (currentTimestamp && timestamp < currentTimestamp) {
		return;
	}

	currentNonce = nonce;
	currentTimestamp = timestamp || Date.now() / 1000; // Convert ms to seconds to match php time()

	// Update the persisted values.
	window.localStorage.setItem(
		'eljeApiNonce',
		JSON.stringify({
			nonce: currentNonce,
			timestamp: currentTimestamp,
		}),
	);
};

const appendNonceHeader = (request) => {
	const headers = request.headers || {};
	request.headers = {
		...headers,
		'X-ELJE-Store-API-Nonce': currentNonce,
	};
	return request;
};

/**
 * Nonce middleware which appends the current nonce to store API requests.
 *
 * @param {Object}   options Fetch options.
 * @param {Function} next    The next middleware or fetchHandler to call.
 * @return {*} The evaluated result of the remaining middleware chain.
 */
const storeNonceMiddleware = (options, next) => {
	if (isSiteApiRequest(options)) {
		options = appendNonceHeader(options);

		// Add nonce to sub-requests
		if (Array.isArray(options?.data?.requests)) {
			options.data.requests = options.data.requests.map(
				appendNonceHeader,
			);
		}
	}
	return next(options, next);
};

apiFetch.use(storeNonceMiddleware);
apiFetch.setNonce = setNonce;

updateNonce(
	// @ts-ignore eljeBlocksMiddlewareConfig is window global cache for the initial nonce initialized from hydration.
	eljeBlocksMiddlewareConfig.eljeApiNonce,
	// @ts-ignore eljeBlocksMiddlewareConfig is window global cache for the initial nonce initialized from hydration.
	eljeBlocksMiddlewareConfig.eljeApiNonceTimestamp,
);
