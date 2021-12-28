module.exports = {
	...require('@wordpress/scripts/config/jest-e2e.config'),
	rootDir: '../../../',
	clearMocks: true,

	moduleFileExtensions: ['js', 'ts'],

	moduleNameMapper: {
		'@elje/blocks-test-utils': '<rootDir>/tests/utils',
	},

	reporters: [
		'default',
		[
			'jest-html-reporters',
			{
				publicPath: './reports/e2e',
				filename: 'index.html',
			},
		],
	],

	testEnvironment: '<rootDir>/tests/e2e/config/environment.js',
	testRunner: 'jest-circus/runner',
	roots: ['<rootDir>/tests/e2e/specs'],
	globalSetup: '<rootDir>/tests/e2e/config/setup.js',
	globalTeardown: '<rootDir>/tests/e2e/config/teardown.js',
	setupFiles: [],
	setupFilesAfterEnv: [
		'<rootDir>/tests/e2e/config/custom-matchers/index.js',
		'<rootDir>/tests/e2e/config/jest.setup.js',
		'expect-puppeteer',
	],

	transformIgnorePatterns: ['node_modules/(?!(elje)/)'],
};
