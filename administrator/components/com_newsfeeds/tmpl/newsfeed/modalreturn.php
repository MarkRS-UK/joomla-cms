<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsfeeds
 *
 * @copyright   (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Component\Newsfeeds\Site\Helper\RouteHelper;

/** @var \Joomla\Component\Newsfeeds\Administrator\View\Newsfeed\HtmlView $this */

$icon     = 'icon-check';
$title    = $this->item ? $this->item->name : '';
$content  = $this->item ? $this->item->alias : '';
$data     = ['contentType' => 'com_newsfeeds.newsfeed'];

if ($this->item) {
    $data['id']    = $this->item->id;
    $data['title'] = $this->item->name;
    $data['catId'] = $this->item->catid;
    $data['uri']   = RouteHelper::getNewsfeedRoute($this->item->id, $this->item->catid, $this->item->language);
}

// Add Content select script
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('modal-content-select');

// The data for Content select script
$this->getDocument()->addScriptOptions('content-select-on-load', $data, false);

?>

<div class="px-4 py-5 my-5 text-center">
    <span class="fa-8x mb-4 <?php echo $icon; ?>" aria-hidden="true"></span>
    <h1 class="display-5 fw-bold"><?php echo $title; ?></h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">
            <?php echo $content; ?>
        </p>
    </div>
</div>
