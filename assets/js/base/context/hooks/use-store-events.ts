import {doAction} from '@wordpress/hooks';
import {useCallback} from '@wordpress/element';

type StoreEvent = (
	eventName: string,
	eventParams?: Partial<Record<string, unknown>>
) => void;

/**
 * Abstraction on top of @wordpress/hooks for dispatching events via doAction for 3rd parties to hook into.
 */
export const useStoreEvents = (): {
	dispatchStoreEvent: StoreEvent;
	dispatchCheckoutEvent: StoreEvent;
} => {
	const dispatchStoreEvent = useCallback((eventName, eventParams = {}) => {
		try {
			doAction(
				`experimental__elje_blocks-${eventName}`,
				eventParams
			);
		} catch (e) {
			// eslint-disable-next-line no-console
			console.error(e);
		}
	}, []);

	const dispatchCheckoutEvent = useCallback(
		(eventName, eventParams = {}) => {
			try {
				doAction(
					`experimental__elje_blocks-checkout-${eventName}`,
					{
						...eventParams,
					}
				);
			} catch (e) {
				// eslint-disable-next-line no-console
				console.error(e);
			}
		},
		[]
	);

	return {
		dispatchStoreEvent,
		dispatchCheckoutEvent
	};
};
