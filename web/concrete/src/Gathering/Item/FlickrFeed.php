<?php
namespace Concrete\Core\Gathering\Item;
use Loader;
use Concrete\Core\Gathering\DataSource\Configuration\Configuration;

class FlickrFeed extends Item {

	public function loadDetails() {}
	public function canViewGatheringItem() {return true;}

	public static function getListByItem($mixed) {
		$ags = GatheringDataSource::getByHandle('flickr_feed');
		return GatheringItem::getListByKey($ags, $mixed->get_link());
	}
	
	public static function add(Configuration $configuration, $post) {
		$gathering = $configuration->getGatheringObject();
		try {
			// we wrap this in a try because it MIGHT fail if it's a duplicate
			$item = parent::add($gathering, $configuration->getGatheringDataSourceObject(), $post->getDateCreated()->format('Y-m-d H:i:s'), $post->getTitle(), md5(trim($post->getLink())));
		} catch(\Exception $e) {}

		if (is_object($item)) {
			$item->assignFeatureAssignments($post);
			$item->setAutomaticGatheringItemTemplate();
			return $item;
		}
	}

	public function assignFeatureAssignments($post) {

		$thumbnail = null;
		$enclosure = $post->getEnclosure();
		if (is_object($enclosure)) {
			if ($enclosure->medium == 'image' || strpos($enclosure->type,  'image') === 0) {
				$thumbnail = $enclosure->url;
			}
		}

		$this->addFeatureAssignment('title', $post->getTitle());
		$this->addFeatureAssignment('date_time', $post->getDateCreated()->format('Y-m-d H:i:s'));
		$this->addFeatureAssignment('link', $post->getLink());
		$description = strip_tags($post->getDescription());
		if ($description != '') {
			$this->addFeatureAssignment('description', $description);
		}
		$author = $post->getAuthor();
		if ($author) {
			$this->addFeatureAssignment('author', $author['name']);
		}
		if ($thumbnail) {
			$this->addFeatureAssignment('image', $thumbnail);
		}
	}

}
