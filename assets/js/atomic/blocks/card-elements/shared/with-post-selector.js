import {__} from '@wordpress/i18n';
import {useState} from '@wordpress/element';

import {
	Button,
	Placeholder,
} from '@wordpress/components';
import {BlockControls} from '@wordpress/block-editor';

import {usePostDataContext} from '@elje/shared-context';

import './editor.scss';

/**
 * This HOC shows a post selection interface if context is not present in the editor.
 *
 * @param {Object} selectorArgs Options for the selector.
 *
 */
const withPostSelector = (selectorArgs) => (OriginalComponent) => {
	return (props) => {
		const postDataContext = usePostDataContext();
		const {attributes, setAttributes} = props;
		const {postId} = attributes;
		const [isEditing, setIsEditing] = useState(!postId);

		if (postDataContext.hasContext) {
			return <OriginalComponent { ...props } />;
		}

		return (
			<>
				{ isEditing ? (
					<Placeholder
						icon={ selectorArgs.icon || '' }
						label={ selectorArgs.label || '' }
						className="elje-atomic-blocks-post"
					>
						{ !!selectorArgs.description && (
							<div>{ selectorArgs.description }</div>
						) }
						<div className="elje-atomic-blocks-post__selection">

							<Button
								isSecondary
								disabled={ !postId }
								onClick={ () => {
									setIsEditing(false);
								} }
							>
								{ __('Done', 'elje') }
							</Button>
						</div>
					</Placeholder>
				):(
					<>
						<BlockControls>

						</BlockControls>
						<OriginalComponent { ...props } />
					</>
				) }
			</>
		);
	};
};

export default withPostSelector;
