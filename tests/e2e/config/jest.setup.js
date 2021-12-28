import {
	enablePageDialogAccept,
	isOfflineMode,
	setBrowserViewport,
} from '@wordpress/e2e-test-utils';

// Set the default test timeout.
jest.setTimeout(60000);

/**
 * Array of page event tuples of [ eventName, handler ].
 *
 * @type {Array}
 */
const pageEvents = [];
/**
 * Set of console logging types observed to protect against unexpected yet
 * handled (i.e. not catastrophic) errors or warnings. Each key corresponds
 * to the Puppeteer ConsoleMessage type, its value the corresponding function
 * on the console global object.
 *
 * @type {Object<string,string>}
 */
const OBSERVED_CONSOLE_MESSAGE_TYPES = {
	error: 'error',
};

async function setupBrowser() {
	await setBrowserViewport('large');
}

/**
 * Adds an event listener to the page to handle additions of page event
 * handlers, to assure that they are removed at test teardown.
 */
function capturePageEventsForTearDown() {
	page.on('newListener', (eventName, listener) => {
		pageEvents.push([eventName, listener]);
	});
}

/**
 * Removes all bound page event handlers.
 */
function removePageEvents() {
	pageEvents.forEach(([eventName, handler]) => {
		page.removeListener(eventName, handler);
	});
}

/**
 * Adds a page event handler to emit uncaught exception to process if one of
 * the observed console logging types is encountered.
 */
function observeConsoleLogging() {
	page.on('console', (message) => {
		const type = message.type();
		if (!OBSERVED_CONSOLE_MESSAGE_TYPES.hasOwnProperty(type)) {
			return;
		}
		const text = message.text();

		if (text.includes('net::ERR_UNKNOWN_URL_SCHEME')) {
			return;
		}

		if (
			text.includes('net::ERR_INTERNET_DISCONNECTED') &&
			isOfflineMode()
		) {
			return;
		}

		const logFunction = OBSERVED_CONSOLE_MESSAGE_TYPES[type];

		console[logFunction](text);
	});
}

beforeAll(async () => {
	capturePageEventsForTearDown();
	enablePageDialogAccept();
	observeConsoleLogging();
	await setupBrowser();
});

afterEach(async () => {
	await setupBrowser();
});

afterAll(() => {
	removePageEvents();
});
