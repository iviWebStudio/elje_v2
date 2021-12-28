import PropTypes
	from 'prop-types';
import {
	Fragment,
	useState,
} from '@wordpress/element';
import {
	__,
	sprintf,
} from '@wordpress/i18n';
import classnames
	from 'classnames';
import {PLACEHOLDER_IMG_SRC} from '@elje/settings';
import {
	useInnerBlockLayoutContext,
	usePostDataContext,
} from '@elje/shared-context';
import {withPostDataContext} from '@elje/shared-hocs';
import {useStoreEvents} from '@elje/base-context/hooks';

import './style.scss';

/**
 * Post card Image Block Component.
 *
 * @param {Object} props                  Incoming props.
 * @param {string} [props.className]      CSS Class name for the component.
 * @param {string} [props.imageSizing]    Size of image to use.
 * @param {boolean} [props.showPostLink]   Whether or not to display a link to the post page.
 * @return {*} The component.
 */
export const Block = ({
						  className,
						  imageSizing = 'full-size',
						  showPostLink = true,
					  }) => {
	const {parentClassName} = useInnerBlockLayoutContext();
	const {post} = usePostDataContext();
	const [imageLoaded, setImageLoaded] = useState(false);
	const {dispatchStoreEvent} = useStoreEvents();

	if (!post.id) {
		return (
			<div
				className={ classnames(
					className,
					'elje-block-components-post-image',
					'elje-block-components-post-image--placeholder',
					{
						[`${ parentClassName }__post-image`]: parentClassName,
					},
				) }
			>
				<ImagePlaceholder/>
			</div>
		);
	}
	const hasPostImages = !!post.thumbnail.id;
	const image = hasPostImages ? post.thumbnail:null;
	const ParentComponent = showPostLink ? 'a':Fragment;
	const anchorLabel = sprintf(
		/* translators: %s is referring to the post name */
		__('Link to %s', 'elje'),
		post.name,
	);
	const anchorProps = {
		href: post.permalink,
		rel: 'nofollow',
		...(!hasPostImages && {'aria-label': anchorLabel}),
		onClick: () => {
			dispatchStoreEvent('post-view-link', {
				post,
			});
		},
	};

	return (
		<div
			className={ classnames(
				className,
				'elje-block-components-post-image',
				{
					[`${ parentClassName }__post-image`]: parentClassName,
				},
			) }
		>
			<ParentComponent { ...(showPostLink && anchorProps) }>
				<Image
					fallbackAlt={ post.name }
					image={ image }
					onLoad={ () => setImageLoaded(true) }
					loaded={ imageLoaded }
					showFullSize={ imageSizing!=='cropped' }
				/>
			</ParentComponent>
		</div>
	);
};

const ImagePlaceholder = () => {
	return (
		<img src={ PLACEHOLDER_IMG_SRC } alt="" width={ 500 } height={ 500 }/>
	);
};

const Image = ({image, onLoad, loaded, showFullSize, fallbackAlt}) => {
	const {thumbnail, src, srcset, sizes, alt} = image || {};
	const imageProps = {
		alt: alt || fallbackAlt,
		onLoad,
		hidden: !loaded,
		src: thumbnail,
		...(showFullSize && {
			src,
			srcSet: srcset,
			sizes,
		}),
	};

	return (
		<>
			{ imageProps.src && (
				/* eslint-disable-next-line jsx-a11y/alt-text */
				<img data-testid="post-image" { ...imageProps } />
			) }
			{ !loaded &&
			<ImagePlaceholder/> }
		</>
	);
};

Block.propTypes = {
	className: PropTypes.string,
	fallbackAlt: PropTypes.string,
	showPostLink: PropTypes.bool,
	showSaleBadge: PropTypes.bool,
	saleBadgeAlign: PropTypes.string,
};

export default withPostDataContext(Block);
