<?php

/**
 *
 * @see XenForo_ViewAdmin_Option_ListOptions
 */
class ThemeHouse_PostOnReport_Extend_XenForo_ViewAdmin_Option_ListOptions extends XFCP_ThemeHouse_PostOnReport_Extend_XenForo_ViewAdmin_Option_ListOptions
{

    public function renderHtml()
    {
        parent::renderHtml();



        foreach ($this->_params['renderedOptions'] as $displayOrder => $options) {
            if (isset($options['reportIntoForumId'])) {
                unset($this->_params['renderedOptions'][$displayOrder]['reportIntoForumId']);
            }
        }
    } /* END renderHtml */
}