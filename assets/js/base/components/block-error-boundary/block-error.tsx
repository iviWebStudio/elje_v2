import {__} from '@wordpress/i18n';
import {ELJE_BLOCKS_IMAGE_URL} from '@elje/block-settings';

import type {BlockErrorProps} from './types';

const BlockError = ({
						imageUrl = `${ELJE_BLOCKS_IMAGE_URL}/block-error.svg`,
						header = __('Oops!', 'elje'),
						text = __(
							'There was an error loading the content.',
							'elje'
						),
						errorMessage,
						errorMessagePrefix = __('Error:', 'elje'),
						button,
					}: BlockErrorProps): JSX.Element => {
	return (
		<div className="elje-block-error elje-block-components-error">
			{imageUrl && (
				<img
					className="elje-block-error__image elje-block-components-error__image"
					src={imageUrl}
					alt=""
				/>
			)}
			<div className="elje-block-error__content elje-block-components-error__content">
				{header && (
					<p className="elje-block-error__header elje-block-components-error__header">
						{header}
					</p>
				)}
				{text && (
					<p className="elje-block-error__text elje-block-components-error__text">
						{text}
					</p>
				)}
				{errorMessage && (
					<p className="elje-block-error__message elje-block-components-error__message">
						{errorMessagePrefix ? errorMessagePrefix + ' ' : ''}
						{errorMessage}
					</p>
				)}
				{button && (
					<p className="elje-block-error__button elje-block-components-error__button">
						{button}
					</p>
				)}
			</div>
		</div>
	);
};

export default BlockError;
