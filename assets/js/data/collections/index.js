import {registerStore} from '@wordpress/data';
import {controls as dataControls} from '@wordpress/data-controls';

import {STORE_KEY} from './constants';
import * as selectors
	from './selectors';
import * as actions
	from './actions';
import * as resolvers
	from './resolvers';
import reducer
	from './reducers';
import {controls} from '../shared-controls';

registerStore(STORE_KEY, {
	reducer,
	actions,
	controls: {...dataControls, ...controls},
	selectors,
	resolvers,
});

export const COLLECTIONS_STORE_KEY = STORE_KEY;
