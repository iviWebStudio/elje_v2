import {getSetting} from '@elje/settings';

export const sharedAttributeBlockTypes = [
	'elje/post-listings',
];

export default {
	/**
	 * Number of columns.
	 */
	columns: {
		type: 'number',
		default: getSetting('default_columns', 3),
	},

	/**
	 * Number of rows.
	 */
	rows: {
		type: 'number',
		default: getSetting('default_rows', 3),
	},

	/**
	 * How to align cart buttons.
	 */
	alignButtons: {
		type: 'boolean',
		default: false,
	},

	/**
	 * Post category, used to display only posts in the given categories.
	 */
	categories: {
		type: 'array',
		default: [],
	},

	/**
	 * Post category operator, used to restrict to posts in all or any selected categories.
	 */
	catOperator: {
		type: 'string',
		default: 'any',
	},

	/**
	 * Content visibility setting
	 */
	contentVisibility: {
		type: 'object',
		default: {
			title: true,
			thumbnail: true,
			content: true,
			button: true,
		},
	},

	/**
	 * Are we previewing?
	 */
	isPreview: {
		type: 'boolean',
		default: false,
	},
};
