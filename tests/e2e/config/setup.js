/* eslint-disable no-console */
import {setup as setupPuppeteer} from 'jest-environment-puppeteer';
import {
	createBlockPages,
	setupPageSettings,
	setupSettings,
} from '../fixtures/fixture-loaders';

module.exports = async (globalConfig) => {
	// we need to load puppeteer global setup here.
	await setupPuppeteer(globalConfig);

	try {
		// do setupSettings separately which hopefully gives a chance for plugin
		// to be configured before the others are executed.
		await setupSettings();
		const pages = await createBlockPages();

		/**
		 * Promise.all will return an array of all promises resolved values.
		 */
		const results = await Promise.all([
			setupPageSettings(),
		]);

		global.fixtureData = {
			pages,
		};
	} catch (e) {
		console.log(e);
	}
};
