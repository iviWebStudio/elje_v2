import {__} from '@wordpress/i18n';
import classnames
	from 'classnames';
import {
	useInnerBlockLayoutContext,
	usePostDataContext,
} from '@elje/shared-context';
import {isEmpty} from 'lodash';
import {withPostDataContext} from '@elje/shared-hocs';
import {HTMLAttributes} from 'react';

import './style.scss';
import {Attributes} from './types';

type Props =
	Attributes
	& HTMLAttributes<HTMLDivElement>;

/**
 * Post Category Block Component.
 *
 * @param {Object} props             Incoming props.
 * @param {string} [props.className] CSS Class name for the component.
 * @return {*} The component.
 */
const Block = ({className}: Props): JSX.Element | null => {
	const {parentClassName} = useInnerBlockLayoutContext();
	const {post} = usePostDataContext();

	if (isEmpty(post.categories)) {
		return null;
	}

	return (
		<div
			className={classnames(
				className,
				'elje-block-components-post-category-list',
				{
					[`${parentClassName}__post-category-list`]: parentClassName,
				}
			)}
		>
			{__('Categories:', 'elje')}{' '}
			<ul>
				{Object.values(post.categories).map(
					({name, link, slug}) => {
						return (
							<li key={`category-list-item-${slug}`}>
								<a href={link}>{name}</a>
							</li>
						);
					}
				)}
			</ul>
		</div>
	);
};

export default withPostDataContext(Block);
