import {JobIcon} from '../icons';

const {__} = wp.i18n;
const {registerBlockType} = wp.blocks;
const{ServerSideRender}=wp.components;

registerBlockType('performancein/job-listing', {
	title: __('Job listing'),
	icon: JobIcon,
	category: 'performancein',
	keywords: [__('Jobs Listing'), __('job'), __('gutenberg'), __('performancein')],
	edit: props => {
		return(
			<ServerSideRender
				block="performancein/job-listing"
			/>
		)
	},
	save: props => {
		return null;
	},
});