/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Placeholder, Button, PanelBody } from '@wordpress/components';
import { withPost } from '@elje/block-hocs';
import BlockErrorBoundary from '@elje/base-components/block-error-boundary';
import EditPostLink from '@elje/editor-components/edit-post-link';
import { postCardBlockPreview } from '@elje/resource-previews';
import { InspectorControls } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './editor.scss';
import ApiError from './api-error';
import SharedPostControl from './shared-post-control';
import EditorBlockControls from './editor-block-controls';
import LayoutEditor from './layout-editor';
import { BLOCK_TITLE, BLOCK_ICON, BLOCK_DESCRIPTION } from '../constants';

/**
 * Component to handle edit mode of the "Single Post Block".
 *
 * @param {Object} props Incoming props for the component.
 * @param {string} props.className
 * @param {Object} props.attributes Incoming block attributes.
 * @param {function(any):any} props.setAttributes Setter for block attributes.
 * @param {string} props.error
 * @param {function(any):any} props.getPost
 * @param {Object} props.post
 * @param {boolean} props.isLoading
 * @param {string} props.clientId
 */
const Editor = ( {
	className,
	attributes,
	setAttributes,
	error,
	getPost,
	post,
	isLoading,
	clientId,
} ) => {

	return (
		<div className={ className }>
index
		</div>
	);
};

export default  Editor;
