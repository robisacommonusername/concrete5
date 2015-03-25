<?php
namespace Concrete\Core\Gathering\Item;
use Loader;
use Concrete\Core\Gathering\DataSource\Configuration\Configuration;

class RssFeed extends Item {

	public function loadDetails() {}
	public function canViewGatheringItem() {return true;}

	public static function getListByItem($mixed) {
		$ags = GatheringDataSource::getByHandle('rss_feed');
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
		/*
		$thumbnail = null;
		$enclosures = $post->get_enclosures();
		if (is_array($enclosures)) {
			foreach($enclosures as $e) {
				if ($e->get_medium() == 'image' || strpos($e->get_type(), 'image') === 0) {
					$thumbnail = $e->get_link();
					break;
				}
			}
		}
		*/


		$this->addFeatureAssignment('title', $post->getTitle());
		$this->addFeatureAssignment('date_time', $post->getDateCreated()->format('Y-m-d H:i:s'));
		$this->addFeatureAssignment('link', $post->getLink());
		$description = strip_tags($post->getDescription());
		if ($description != '') {
			$this->addFeatureAssignment('description', $description);
		}
		/*
		if ($thumbnail) {
			$this->addFeatureAssignment('image', $thumbnail);
		}
		*/
	}

}
