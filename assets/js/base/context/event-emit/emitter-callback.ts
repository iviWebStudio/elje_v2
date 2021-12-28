import {actions} from './reducer';
import type {
	ActionCallbackType,
	ActionType
} from './types';

export const emitterCallback = (
	type: string,
	observerDispatch: React.Dispatch<ActionType>
) => (callback: ActionCallbackType, priority = 10): (() => void) => {
	const action = actions.addEventCallback(type, callback, priority);
	observerDispatch(action);
	return () => {
		observerDispatch(actions.removeEventCallback(type, action.id));
	};
};
