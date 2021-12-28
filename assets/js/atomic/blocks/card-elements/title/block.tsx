import classnames
	from 'classnames';
import {HTMLAttributes} from 'react';
import {
	useInnerBlockLayoutContext,
	usePostDataContext,
} from '@elje/shared-context';
import {
	getColorClassName,
	getFontSizeClass
} from '@wordpress/block-editor';
import {isFeaturePluginBuild} from '@elje/block-settings';
import {withPostDataContext} from '@elje/shared-hocs';
import PostName
	from '@elje/base-components/post-name';
import {useStoreEvents} from '@elje/base-context/hooks';

import './style.scss';
import {Attributes} from './types';

type Props =
	Attributes
	& HTMLAttributes<HTMLDivElement>;

interface TagNameProps extends HTMLAttributes<HTMLOrSVGElement> {
	headingLevel: number;
	elementType?: keyof JSX.IntrinsicElements;
}

const TagName = ({
					 children,
					 headingLevel,
					 elementType: ElementType = `h${headingLevel}` as keyof JSX.IntrinsicElements,
					 ...props
				 }: TagNameProps): JSX.Element => {
	return <ElementType {...props}>{children}</ElementType>;
};

/**
 * Post Title Block Component.
 *
 * @param {Object}  props                   Incoming props.
 * @param {string}  [props.className]       CSS Class name for the component.
 * @param {number}  [props.headingLevel]    Heading level (h1, h2 etc)
 * @param {boolean} [props.showPostLink] Whether or not to display a link to the post page.
 * @param {string}  [props.align]           Title alignment.
 * @param {string}  [props.textColor]       Title color name.
 * @param {string}  [props.fontSize]        Title font size name.
 * @param {string}  [props.style]           Title inline style.
 * will be used if this is not provided.
 * @return {*} The component.
 */
export const Block = ({
						  className,
						  headingLevel = 2,
						  showPostLink = true,
						  align,
						  textColor,
						  fontSize,
						  style,
					  }: Props): JSX.Element => {
	const {parentClassName} = useInnerBlockLayoutContext();
	const {post} = usePostDataContext();
	const {dispatchStoreEvent} = useStoreEvents();

	const colorClass = getColorClassName('color', textColor);
	const fontSizeClass = getFontSizeClass(fontSize);
	const titleClasses = classnames('wp-block-elje-post-title', {
		'has-text-color': textColor || style?.color?.text || style?.color,
		[`has-font-size`]:
		fontSize || style?.typography?.fontSize || style?.fontSize,
		[colorClass]: colorClass,
		[fontSizeClass]: fontSizeClass,
	});

	const titleStyle = {
		fontSize: style?.fontSize || style?.typography?.fontSize,
		color: style?.color?.text || style?.color,
	};

	if (!post.id) {
		return (
			<TagName
				headingLevel={headingLevel}
				className={classnames(
					className,
					'elje-block-components-post-title',
					{
						[`${parentClassName}__post-title`]: parentClassName,
						[`elje-block-components-post-title--align-${align}`]:
						align && isFeaturePluginBuild(),
						[titleClasses]: isFeaturePluginBuild(),
					}
				)}
			/>
		);
	}

	return (
		<TagName
			headingLevel={headingLevel}
			className={classnames(
				className,
				'elje-block-components-post-title',
				{
					[`${parentClassName}__post-title`]: parentClassName,
					[`elje-block-components-post-title--align-${align}`]:
					align && isFeaturePluginBuild(),
				}
			)}
		>
			<PostName
				className={classnames({
					[titleClasses]: isFeaturePluginBuild(),
				})}
				disabled={!showPostLink}
				name={post.name}
				permalink={post.permalink}
				rel={showPostLink ? 'nofollow' : ''}
				onClick={() => {
					dispatchStoreEvent('post-view-link', {
						post,
					});
				}}
				style={isFeaturePluginBuild() ? titleStyle : {}}
			/>
		</TagName>
	);
};

export default withPostDataContext(Block);
