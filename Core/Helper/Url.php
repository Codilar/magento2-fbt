<?php

/**
 * @package     eat
 * @author      Codilar Technologies
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        http://www.codilar.com/
 */

namespace Codilar\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;

class Url extends AbstractHelper
{
    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * Url constructor.
     * @param Context $context
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        Context $context,
        UrlInterface $urlInterface
    )
    {
        parent::__construct($context);
        $this->urlInterface = $urlInterface;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->urlInterface->getCurrentUrl();
    }

    /**
     * @param $path
     * @return string
     */
    public function getUrl($path)
    {
        return $this->urlInterface->getUrl($path);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlInterface->getBaseUrl();
    }
}