<? defined('C5_EXECUTE') or die("Access Denied.");

if ($_REQUEST['gaiID']) {
  $nh = Loader::helper('validation/numbers');
  $form = Loader::helper('form');
  $gaiID = intval($_REQUEST['gaiID']);
  $nh = Loader::helper('validation/numbers');
  $item = \Concrete\Core\Gathering\Item\Item::getByID($gaiID);
  $type = \Concrete\Core\Gathering\Item\Template\Type::getByHandle('detail');
  if (is_object($item) && Loader::helper('validation/token')->validate('get_gathering_items', $_REQUEST['token'])) {
    $item->render($type);
    ?>
  <? } ?>
<? } ?>