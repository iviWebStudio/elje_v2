/**
 * @param {object} values
 * @param {string} type
 */
import {ELJE_ASSET_URL} from '@elje/settings';
import {__} from '@wordpress/i18n';

/**
 * @param {{ [x: string]: any; }} values
 */
const parseBoxValues = (values, type = 'padding') => {
	/** @type {{ [x: string]: any; }} */
	let map = {};
	Object.keys(values).map(position => {
		map[(type + position[0].toUpperCase() + position.substring(1))] = values[position];
	});
	return map;
};

const attributes = {
	width: {
		type: 'string',
		default: '300px',
	},
	height: {
		type: 'string',
		default: '360px',
	},
	activeLayout: {
		type: 'string',
		enum: ['primary', 'secondary'],
		default: 'primary',
	},
	headerText: {
		type: 'string',
		default: __('Title', 'elje'),
	},
	headerBgColor: {
		type: 'string',
		default: '#ffffff',
	},
	headerTextColor: {
		type: 'string',
		default: '#000000',
	},
	headerFontSize: {
		type: 'string',
		default: '12px',
	},
	headerPadding: {
		type: 'object',
		default: {
			top: '10px',
			right: '10px',
			bottom: '10px',
			left: '10px',
		},
	},
	headerMargin: {
		type: 'object',
		default: {
			top: '0px',
			right: '0px',
			bottom: '0px',
			left: '0px',
		},
	},
	bodyText: {
		type: 'string',
		default: __('Content', 'elje'),
	},
	bodyBgColor: {
		type: 'string',
		default: '#ffffff',
	},
	bodyTextColor: {
		type: 'string',
		default: '#000000',
	},
	bodyFontSize: {
		type: 'string',
		default: '12px',
	},
	bodyPadding: {
		type: 'object',
		default: {
			top: '10px',
			right: '10px',
			bottom: '10px',
			left: '10px',
		},
	},
	bodyMargin: {
		type: 'object',
		default: {
			top: '0px',
			right: '0px',
			bottom: '0px',
			left: '0px',
		},
	},
	buttonText: {
		type: 'string',
		default: __('Action button', 'elje'),
	},
	buttonLink: {
		type: 'string',
		default: '#',
	},
	buttonBgColor: {
		type: 'string',
		default: '#000000',
	},
	buttonTextColor: {
		type: 'string',
		default: '#FFFFFF',
	},
	buttonFontSize: {
		type: 'string',
		default: '12px',
	},
	buttonPadding: {
		type: 'object',
		default: {
			top: '10px',
			right: '10px',
			bottom: '10px',
			left: '10px',
		},
	},
	buttonMargin: {
		type: 'object',
		default: {
			top: '0px',
			right: '0px',
			bottom: '0px',
			left: '0px',
		},
	},
	imageId: {
		type: 'string',
		default: `${ ELJE_ASSET_URL }/images/post-card-default.jpg`,
	},
};

/**
 * @param {{ headerPadding: any; headerMargin: any; headerBgColor: any; headerTextColor: any; headerFontSize: any; }} attributes
 */
const cardHeaderProps = attributes => {
	const {paddingTop, paddingRight, paddingBottom, paddingLeft} = parseBoxValues(attributes.headerPadding);
	const {marginTop, marginRight, marginBottom, marginLeft} = parseBoxValues(attributes.headerMargin, 'margin');
	return {
		className: 'elje-card-header',
		style: {
			backgroundColor: attributes.headerBgColor,
			color: attributes.headerTextColor,
			fontSize: attributes.headerFontSize,
			paddingTop,
			paddingRight,
			paddingBottom,
			paddingLeft,
			marginTop,
			marginRight,
			marginBottom,
			marginLeft,
		},
	};
};

/**
 * @param {{ bodyPadding: any; bodyMargin: any; bodyBgColor: any; bodyTextColor: any; bodyFontSize: any; }} attributes
 */
const cardBodyProps = attributes => {
	const {paddingTop, paddingRight, paddingBottom, paddingLeft} = parseBoxValues(attributes.bodyPadding);
	const {marginTop, marginRight, marginBottom, marginLeft} = parseBoxValues(attributes.bodyMargin, 'margin');
	return {
		className: 'elje-card-body',
		style: {
			backgroundColor: attributes.bodyBgColor,
			color: attributes.bodyTextColor,
			fontSize: attributes.bodyFontSize,
			paddingTop,
			paddingRight,
			paddingBottom,
			paddingLeft,
			marginTop,
			marginRight,
			marginBottom,
			marginLeft,
		},
	};
};

/**
 * @param {{ buttonPadding: any; buttonMargin: any; buttonLink: any; buttonBgColor: any; buttonTextColor: any; buttonFontSize: any; }} attributes
 */
const buttonProps = attributes => {
	const {paddingTop, paddingRight, paddingBottom, paddingLeft} = parseBoxValues(attributes.buttonPadding);
	const {marginTop, marginRight, marginBottom, marginLeft} = parseBoxValues(attributes.buttonMargin, 'margin');
	return {
		variant: 'secondary',
		href: attributes.buttonLink,
		className: 'btn',
		style: {
			backgroundColor: attributes.buttonBgColor,
			color: attributes.buttonTextColor,
			fontSize: attributes.buttonFontSize,
			paddingTop,
			paddingRight,
			paddingBottom,
			paddingLeft,
			marginTop,
			marginRight,
			marginBottom,
			marginLeft,
		},
	};
};

export {
	attributes,
	cardHeaderProps,
	cardBodyProps,
	buttonProps,
};
