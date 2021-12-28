import {withFilteredAttributes} from '@elje/shared-hocs';

import Block
	from './block';
import attributes
	from './attributes';

export default withFilteredAttributes(attributes)(Block);
