/**
 * External dependencies
 */
import {__} from '@wordpress/i18n';
import {useCallback} from '@wordpress/element';
import {useDispatch} from '@wordpress/data';
import {
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	InnerBlockLayoutContextProvider,
	PostDataContextProvider,
} from '@elje/shared-context';
import {createBlocksFromTemplate} from '@elje/atomic-utils';
import {
	Button,
	PanelBody,
} from '@wordpress/components';
import {
	Icon,
	restore,
} from '@elje/icons';

/**
 * Internal dependencies
 */
import {
	ALLOWED_INNER_BLOCKS,
	BLOCK_NAME,
	DEFAULT_INNER_BLOCKS,
} from '../constants';

/**
 * Component to handle edit mode of the "Single Post Block".
 *
 * @param {Object} props Incoming props for the component.
 * @param {boolean} props.isLoading
 * @param {Object} props.post
 * @param {string} props.clientId
 */
const LayoutEditor = ({isLoading, post, clientId}) => {
	const baseClassName = 'elje-block-single-post elje-block-layout';
	const {replaceInnerBlocks} = useDispatch('core/block-editor');

	const resetInnerBlocks = useCallback(() => {
		replaceInnerBlocks(
			clientId,
			createBlocksFromTemplate(DEFAULT_INNER_BLOCKS),
			false,
		);
	}, [clientId, replaceInnerBlocks]);

	return (
		<InnerBlockLayoutContextProvider
			parentName={ BLOCK_NAME }
			parentClassName={ baseClassName }
		>
			<PostDataContextProvider
				post={ post }
				isLoading={ isLoading }
			>
				<InspectorControls>
					<PanelBody
						title={ __('Layout', 'elje') }
						initialOpen={ true }
					>
						<Button
							label={ __(
								'Reset layout to default',
								'elje',
							) }
							onClick={ resetInnerBlocks }
							isTertiary
							className="elje-block-single-post__reset-layout"
						>
							<Icon srcElement={ restore }/>{ ' ' }
							{ __(
								'Reset layout',
								'elje',
							) }
						</Button>
					</PanelBody>
				</InspectorControls>
				<div className={ baseClassName }>
					<InnerBlocks
						template={ DEFAULT_INNER_BLOCKS }
						allowedBlocks={ ALLOWED_INNER_BLOCKS }
						templateLock={ false }
						renderAppender={ false }
					/>
				</div>
			</PostDataContextProvider>
		</InnerBlockLayoutContextProvider>
	);
};

export default LayoutEditor;
