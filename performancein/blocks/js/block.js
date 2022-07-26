import './block/top50/block';
import './block/article-listing/block';
import './block/partner-listing/block';
import './block/users/block';
import './block/donwload-form/block';
import './block/job-listing/block';

(function() {
	const performanceinIcon = (
		<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20px" height="25px" viewBox="56.576 13.601 3.424 3.423" enable-background="new 56.576 13.601 3.424 3.423">
			<title>Asset 2</title>
			<path fill="#E94E27" d="M58.288,13.601c0.945,0,1.712,0.767,1.712,1.712s-0.767,1.711-1.712,1.711s-1.712-0.766-1.712-1.711
                    c0,0,0,0,0-0.001C56.576,14.367,57.344,13.601,58.288,13.601z"/>
		</svg>
	);
	wp.blocks.updateCategory('performancein', {icon: performanceinIcon});
})();
