<?php
namespace Concrete\Core\Gathering\DataSource;
use Concrete\Core\Gathering\DataSource\Configuration\FlickrFeedConfiguration;
use Concrete\Core\Gathering\Item\FlickrFeed;
use Loader;
use \Concrete\Core\Gathering\DataSource\Configuration\Configuration as GatheringDataSourceConfiguration;
use Concrete\Core\Gathering\Gathering;

class FlickrFeedDataSource extends DataSource {

	const FLICKR_FEED_URL = 'http://api.flickr.com/services/feeds/photos_public.gne';

	public function createConfigurationObject(Gathering $ag, $post) {
		$o = new FlickrFeedConfiguration();
		$o->setFlickrFeedTags($post['flickrFeedTags']);
		return $o;
	}

	public function createGatheringItems(GatheringDataSourceConfiguration $configuration) {
		$fp = Loader::helper('feed');
		$posts = $fp->load(self::FLICKR_FEED_URL . '?tags=' . $configuration->getFlickrFeedTags(), false);

		$gathering = $configuration->getGatheringObject();
		$lastupdated = 0;
		if ($gathering->getGatheringDateLastUpdated()) {
			$lastupdated = strtotime($gathering->getGatheringDateLastUpdated());
		}

		$items = array();
		foreach($posts as $p) {
			$item = FlickrFeed::add($configuration, $p);

			if (is_object($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}
	
}
