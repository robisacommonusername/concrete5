<?php
namespace Concrete\Core\Gathering\DataSource;
use Concrete\Core\Gathering\DataSource\Configuration\RssFeedConfiguration;
use Concrete\Core\Gathering\Item\RssFeed;
use Loader;
use \Concrete\Core\Gathering\DataSource\Configuration\Configuration as GatheringDataSourceConfiguration;
use Concrete\Core\Gathering\Gathering;
class RssFeedDataSource extends DataSource {

	public function createConfigurationObject(Gathering $ag, $post) {
		$o = new RssFeedConfiguration();
		$o->setRssFeedURL($post['rssFeedURL']);
		return $o;
	}

	public function createGatheringItems(GatheringDataSourceConfiguration $configuration) {
		$fp = Loader::helper('feed');
		$posts = $fp->load($configuration->getRssFeedURL(), false);

		$gathering = $configuration->getGatheringObject();
		$lastupdated = 0;
		if ($gathering->getGatheringDateLastUpdated()) {
			$lastupdated = strtotime($gathering->getGatheringDateLastUpdated());
		}

		$items = array();
		foreach($posts as $p) {
			$posttime = $p->getDateCreated()->getTimestamp();
			//if ($posttime > $lastupdated) {
				$item = RssFeed::add($configuration, $p);
			//}

			if (is_object($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}
	
}
