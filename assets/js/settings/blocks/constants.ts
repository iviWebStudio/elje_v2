import {getSetting} from '@elje/settings';

export type WordCountType =
	| 'words'
	| 'characters_excluding_spaces'
	| 'characters_including_spaces';

interface EljeBlocksConfig {
	buildPhase: number;
	pluginUrl: string;
	postCount: number;
	defaultAvatar: string;
	restApiRoutes: Record<string, string[]>;
	wordCountType: WordCountType;
}

export const blocksConfig = getSetting('eljeBlocksConfig', {
	buildPhase: 1,
	pluginUrl: '',
	postCount: 0,
	defaultAvatar: '',
	restApiRoutes: {},
	wordCountType: 'words',
}) as EljeBlocksConfig;

export const ELJE_BLOCKS_IMAGE_URL = blocksConfig.pluginUrl + 'images/';
export const ELJE_BLOCKS_BUILD_URL = blocksConfig.pluginUrl + 'build/';
export const ELJE_BLOCKS_PHASE = blocksConfig.buildPhase;
export const LOGIN_URL = getSetting('wpLoginUrl', '/wp-login.php');
