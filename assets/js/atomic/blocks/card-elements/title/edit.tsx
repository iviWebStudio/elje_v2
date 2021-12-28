import {__} from '@wordpress/i18n';
import {
	Disabled,
	PanelBody,
	ToggleControl
} from '@wordpress/components';
import {compose} from '@wordpress/compose';
import {
	AlignmentToolbar,
	BlockControls,
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {isFeaturePluginBuild} from '@elje/block-settings';
import HeadingToolbar
	from '@elje/editor-components/heading-toolbar';

import Block
	from './block';
import withPostSelector
	from '../shared/with-post-selector';
import {
	BLOCK_ICON,
	BLOCK_TITLE
} from './constants';
import {Attributes} from './types';

interface Props {
	attributes: Attributes;
	setAttributes: (attributes: Record<string, unknown>) => void;
}

const TitleEdit = ({attributes, setAttributes}: Props): JSX.Element => {
	const blockProps = useBlockProps();
	const {headingLevel, showPostLink, align} = attributes;
	return (
		<div {...blockProps}>
			<BlockControls>
				<HeadingToolbar
					isCollapsed={true}
					minLevel={1}
					maxLevel={7}
					selectedLevel={headingLevel}
					onChange={(newLevel: number) =>
						setAttributes({headingLevel: newLevel})
					}
				/>
				{isFeaturePluginBuild() && (
					<AlignmentToolbar
						value={align}
						onChange={(newAlign) => {
							setAttributes({align: newAlign});
						}}
					/>
				)}
			</BlockControls>
			<InspectorControls>
				<PanelBody
					title={__('Content', 'elje')}
				>
					<ToggleControl
						label={__(
							'Link to Post Page',
							'elje'
						)}
						help={__(
							'Links the image to the single post listing.',
							'elje'
						)}
						checked={showPostLink}
						onChange={() =>
							setAttributes({
								showPostLink: !showPostLink,
							})
						}
					/>
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<Block {...attributes} />
			</Disabled>
		</div>
	);
};

const Title = isFeaturePluginBuild()
	? compose([
		withPostSelector({
			icon: BLOCK_ICON,
			label: BLOCK_TITLE,
			description: __(
				'Choose a post to display its title.',
				'elje'
			),
		}),
	])(TitleEdit)
	: TitleEdit;

export default Title;
