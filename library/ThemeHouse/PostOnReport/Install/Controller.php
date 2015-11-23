<?php

class ThemeHouse_PostOnReport_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/post-on-report-by-th.714/';

    protected function _preInstall()
    {
        $addOn = $this->getModelFromCache('XenForo_Model_AddOn')->getAddOnById('postOnReport');

        if ($addOn) {
            $this->_db->update('xf_option', array(
                'option_id' => 'th_postOnReport_reportIntoForumId',
                'addon_id' => 'ThemeHouse_PostOnReport'
            ), 'option_id = ' . $this->_db->quote('newThreadOnReportCategory'));

            $dw = XenForo_DataWriter::create('XenForo_DataWriter_AddOn');
            $dw->setExistingData($addOn);
            $dw->delete();
        }
    } /* END _preInstall */
}