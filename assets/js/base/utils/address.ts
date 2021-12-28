import {defaultAddressFields} from '@elje/settings';
import {isEmail} from '@wordpress/url';

/**
 * pluckAddress takes a full address object and returns relevant fields for calculating
 * shipping, so we can track when one of them change to update rates.
 *
 * @param {Object} address          An object containing all address information
 * @param {string} address.country  The country.
 * @param {string} address.state    The state.
 * @param {string} address.city     The city.
 * @param {string} address.postcode The postal code.
 *
 * @return {Object} pluckedAddress  An object containing shipping address that are needed to fetch an address.
 */
export const pluckAddress = ({
								 country = '',
								 state = '',
								 city = '',
								 postcode = '',
							 }) => ({
	country: country.trim(),
	state: state.trim(),
	city: city.trim(),
	postcode: postcode ? postcode.replace(' ', '').toUpperCase() : '',
});

/**
 * pluckEmail takes a full address object and returns only the email address, if set and valid. Otherwise returns an empty string.
 *
 * @param {Object} address       An object containing all address information
 * @param {string} address.email The email address.
 * @return {string} The email address.
 */
export const pluckEmail = ({
							   email = '',
						   }): string =>
	isEmail(email) ? email.trim() : '';

/**
 * Type-guard.
 */
const isValidAddressKey = (
	key: string,
	address: object
): key is keyof typeof address => {
	return key in address;
};

/**
 * Sets fields to an empty string in an address if they are hidden by the settings in countryLocale.
 *
 * @param {Object} address The address to empty fields from.
 * @return {Object} The address with hidden fields values removed.
 */
export const emptyHiddenAddressFields = (address: any) => {
	const fields = Object.keys(defaultAddressFields);
	const addressFields = [];
	const newAddress = Object.assign({}, address) as T;

	addressFields.forEach(({key = '', hidden = false}) => {
		if (hidden && isValidAddressKey(key, address)) {
			newAddress[key] = '';
		}
	});

	return newAddress;
};
