<?php

/**
 *
 * @see XenForo_Model_Report
 */
class ThemeHouse_PostOnReport_Extend_XenForo_Model_Report extends XFCP_ThemeHouse_PostOnReport_Extend_XenForo_Model_Report
{

    /**
     *
     * @see XenForo_Model_Report::reportContent()
     */
    public function reportContent($contentType, array $content, $message, array $viewingUser = null)
    {
        $reportForumId = XenForo_Application::getOptions()->th_postOnReport_reportIntoForumId;

        if (XenForo_Application::$versionId < 1020000) {
            $this->standardizeViewingUserReference($viewingUser);

            if (!$viewingUser['user_id']) {
                return false;
            }

            $handler = $this->getReportHandler($contentType);
            if (!$handler) {
                return false;
            }

            list ($contentId, $contentUserId, $contentInfo) = $handler->getReportDetailsFromContent($content);
            if (!$contentId) {
                return false;
            }

            if ($reportForumId) {
                /**
                 * @var $forumModel XenForo_Model_Forum
                 */
                $forumModel = $this->getModelFromCache('XenForo_Model_Forum');
                $reportForum = $forumModel->getForumById($reportForumId);
                if ($reportForum) {
                    $report = array(
                        'content_type' => $contentType,
                        'content_id' => $contentId,
                        'content_user_id' => $contentUserId,
                        'content_info' => $contentInfo,
                        'first_report_date' => XenForo_Application::$time,
                        'report_state' => 'open',
                        'assigned_user_id' => 0,
                        'comment_count' => 0,
                        'report_count' => 0
                    );

                    $params = $this->_getContentForThread($report, $contentInfo, $handler);

                    $user = $this->getModelFromCache('XenForo_Model_User')->getUserById($contentUserId);
                    if ($user) {
                        $params['username'] = $user['username'];
                    }
                    $params['userLink'] = XenForo_Link::buildPublicLink('canonical:members', $user);
                    $params['reporter'] = $viewingUser['username'];
                    $params['reporterLink'] = XenForo_Link::buildPublicLink('canonical:members', $viewingUser);
                    $params['reportReason'] = $message;

                    $threadTitle = new XenForo_Phrase('th_reported_thread_title_postonreport', $params, false);

                    /**
                     * @var $threadDw XenForo_DataWriter_Discussion_Thread
                     */
                    $threadDw = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread',
                        XenForo_DataWriter::ERROR_SILENT);
                    $threadDw->setExtraData(XenForo_DataWriter_Discussion_Thread::DATA_FORUM, $reportForum);
                    $threadDw->bulkSet(
                        array(
                            'node_id' => $reportForum['node_id'],
                            'title' => $threadTitle->render(),
                            'user_id' => $viewingUser['user_id'],
                            'username' => $viewingUser['username']
                        ));
                    $threadDw->set('discussion_state',
                        $this->getModelFromCache('XenForo_Model_Post')
                            ->getPostInsertMessageState(array(), $reportForum));

                    $message = new XenForo_Phrase('th_reported_thread_message_postonreport', $params, false);

                    $postWriter = $threadDw->getFirstMessageDw();
                    $postWriter->set('message', $message->render());
                    $postWriter->setExtraData(XenForo_DataWriter_DiscussionMessage_Post::DATA_FORUM, $reportForum);

                    $threadDw->save();
                }
            }
            return parent::reportContent($contentType, $content, $message, $viewingUser);
        }

        XenForo_Application::getOptions()->set('reportIntoForumId', $reportForumId);

        if (parent::reportContent($contentType, $content, $message, $viewingUser)) {
            XenForo_Application::getOptions()->set('reportIntoForumId', 0);

            return parent::reportContent($contentType, $content, $message, $viewingUser);
        }
    } /* END reportContent */

    protected function _getContentForThread(array $report, array $contentInfo, XenForo_ReportHandler_Abstract $handler)
    {
        return array(
            'message' => isset($contentInfo['message']) ? $contentInfo['message'] : '',
            'extraDetails' => '',
            'link' => XenForo_Application::getOptions()->boardUrl . '/' .  $handler->getContentLink($report, $contentInfo),
            'title' => $handler->getContentTitle($report, $contentInfo),
            'username' => isset($contentInfo['username']) ? $contentInfo['username'] : ''
        );
    } /* END _getContentForThread */
}