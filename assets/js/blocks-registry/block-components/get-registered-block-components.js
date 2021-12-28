/**
 * Internal dependencies
 */
import {registeredBlockComponents} from './registered-block-components-init';

/**
 * Get all Registered Block Components.
 *
 * Elje Blocks allows React Components to be used on the frontend of the store in place of
 * Blocks instead of just serving static content.
 *
 * This gets all registered Block Components so we know which Blocks map to which React Components.
 *
 * @param {string} context Current context (a named parent Block). If Block Components were only
 *                         registered under a certain context, those Components will be returned,
 *                         as well as any Components registered under all contexts.
 * @return {Object} List of React Components registered under the provided context.
 */
export function getRegisteredBlockComponents(context) {
	const parentInnerBlocks =
		typeof registeredBlockComponents[context]==='object' &&
		Object.keys(registeredBlockComponents[context]).length > 0
			? registeredBlockComponents[context]
			:{};

	return {
		...parentInnerBlocks,
		...registeredBlockComponents.any,
	};
}
