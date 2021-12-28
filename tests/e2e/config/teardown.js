/* eslint-disable no-console */
import {teardown as teardownPuppeteer} from 'jest-environment-puppeteer';

import {deleteBlockPages} from '../fixtures/fixture-loaders';

module.exports = async (globalConfig) => {
	await teardownPuppeteer(globalConfig);
	const {
		pages,
	} = global.fixtureData;
	return Promise.all([
		deleteBlockPages(pages),
	]).catch(console.log);
};
