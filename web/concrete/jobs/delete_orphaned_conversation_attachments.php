<?
namespace Concrete\Job;
use FileSet;
use Config;
use Log;
use \Job as AbstractJob;
class DeleteOrphanedConversationAttachments extends AbstractJob {

    public function getJobName() {
        return t("Delete Orphaned Conversation Attachments");
    }

    public function getJobDescription() {
        return t("Removes files that were uploaded as file attachments for messages that were never published.");
    }

    public function run() {
        $filesToDelete = FileSet::getFilesBySetName(Config::get('concrete.conversations.attachments_pending_file_set'));
        if(count($filesToDelete) > 0) {
            foreach($filesToDelete as $orphan) {
                $timeCreated = $orphan->getDateAdded()->format('Y-m-d H:i:s');
                if(strtotime($timeCreated) < strtotime('-6 hours')) {
                    $orphan->delete();
                }
            }
        }
        return t('Orphaned Conversation Attachments Deleted');
    }
}

?>