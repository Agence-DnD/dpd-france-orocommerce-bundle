<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Controller\Frontend;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\PudoProvider;
use Oro\Bundle\SecurityBundle\Annotation\CsrfProtection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RelayController
 *
 * @Route("/dpd_france")
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Controller\Frontend
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class RelayController extends AbstractController
{
    /**
     * Returns a list of the closest PUDOs for a given address
     *
     * @Route("/dpd_france/relays", name="dpd_france_relay_list", methods={"GET"})
     * @CsrfProtection()
     *
     * @param PudoProvider $pudoProvider
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(PudoProvider $pudoProvider, Request $request): JsonResponse
    {

        $checkoutId = $request->get('checkoutId');
        $city = $request->get('city');
        $address = $request->get('address');
        $postalCode = $request->get('postalCode');

        try {
            $pudoList = $pudoProvider->getPudoList($checkoutId, $city, $postalCode, $address);
            dump($pudoList);

            $response = [
                'relays' => $pudoList
            ];
            $status = Response::HTTP_OK;
        } catch (\ExceptionInterface $e) {
            $response = [
                'error' => $e->getMessage()
            ];
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse($response, $status);
    }
}
