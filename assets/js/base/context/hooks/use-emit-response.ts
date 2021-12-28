import {isObject} from '@elje/types';

export enum responseTypes {
	SUCCESS = 'success',
	FAIL = 'failure',
	ERROR = 'error',
}

export interface ResponseType extends Record<string, unknown> {
	type: responseTypes;
	retry?: boolean;
}

const isResponseOf = (
	response: unknown,
	type: string
): response is ResponseType => {
	return isObject(response) && 'type' in response && response.type === type;
};

export const isSuccessResponse = (
	response: unknown
): response is ResponseType => {
	return isResponseOf(response, responseTypes.SUCCESS);
};

export const isErrorResponse = (
	response: unknown
): response is ResponseType => {
	return isResponseOf(response, responseTypes.ERROR);
};

export const isFailResponse = (
	response: unknown
): response is ResponseType => {
	return isResponseOf(response, responseTypes.FAIL);
};

export const shouldRetry = (response: unknown): boolean => {
	return (!isObject(response) ||
		typeof response.retry === 'undefined' || response.retry);
};

/**
 * A custom hook exposing response utilities for emitters.
 */
export const useEmitResponse = () =>
	({
		responseTypes,
		shouldRetry,
		isSuccessResponse,
		isErrorResponse,
		isFailResponse,
	} as const);
