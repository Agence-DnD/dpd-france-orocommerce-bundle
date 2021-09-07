<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Exception;

use Oro\Bundle\ActivityBundle\Exception\Exception as BaseException;

/**
 * Class ExportException
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Exception
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class ExportException extends \Exception implements BaseException
{
}