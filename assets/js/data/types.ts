export interface ResponseError {
	code: string;
	message: string;
	data: {
		status: number;
		[ key: string ]: unknown;
	};
}

export interface ApiResponse {
	body: Record< string, unknown >;
	headers: Headers;
	status: number;
}

export function assertResponseIsValid(
	response: unknown
): asserts response is ApiResponse {
	if (
		typeof response === 'object' &&
		response !== null &&
		response.hasOwnProperty( 'body' ) &&
		response.hasOwnProperty( 'headers' )
	) {
		return;
	}
	throw new Error( 'Response not valid' );
}
