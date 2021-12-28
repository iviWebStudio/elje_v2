import { getAllBlocks, switchUserToAdmin } from '@wordpress/e2e-test-utils';
import { insertBlockDontWaitForInsertClose } from '../../utils.js';
import { visitBlockPage } from '@elje/blocks-test-utils';

if ( process.env.ELJE_PLUGIN_PHASE < 3 )
	// eslint-disable-next-line jest/no-focused-tests
	test.only( 'skipping all other things', () => {} );

const block = {
	name: 'Post card',
	slug: 'elje/post-card',
	class: '.elje-block-post-card',
};

describe( `${ block.name } Block`, () => {
	beforeAll( async () => {
		await switchUserToAdmin();
		await visitBlockPage( `${ block.name } Block` );
	} );

	it( 'can be inserted more than once', async () => {
		await insertBlockDontWaitForInsertClose( block.name );
		expect( await getAllBlocks() ).toHaveLength( 2 );
	} );

	it( 'renders without crashing', async () => {
		await expect( page ).toRenderBlock( block );
	} );
} );
