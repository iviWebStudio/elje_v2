import {
	buttonProps,
	cardBodyProps,
	cardHeaderProps,
} from './includes';

import {Button} from '@wordpress/components';

const PrimaryPostCard = ({attributes}) => {
	return (
		<div className="elje-block elje-card layout-primary">
			<div { ...cardHeaderProps(attributes) }>
				<h5 className="elje-card-title">{ attributes.headerText }</h5>
			</div>
			{ attributes.imageId &&
			<img
				className="elje-card-img"
				alt="Card Media"
				src={ attributes.imageId }
			/> }
			<div { ...cardBodyProps(attributes) }>
				<p className="elje-card-text">{ attributes.bodyText }</p>
				{ attributes.buttonLink && attributes.buttonText &&
				<Button { ...buttonProps(attributes) }>{ attributes.buttonText }</Button>
				}
			</div>
		</div>
	);
};

export default PrimaryPostCard;
