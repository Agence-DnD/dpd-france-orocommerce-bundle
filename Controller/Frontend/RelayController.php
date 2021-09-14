<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Controller\Frontend;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\PudoProvider;
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
     * @Route("/relays", name="dpd_france_relay_list", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request, PudoProvider $pudoProvider): JsonResponse
    {
        /** @var string $checkoutId */
        $checkoutId = $request->get('checkoutId');
        /** @var string $city */
        $city = $request->get('city');
        /** @var string $address */
        $address = $request->get('address');
        /** @var string $postalCode */
        $postalCode = $request->get('postalCode');

        try {
            if (empty($checkoutId) || (empty($city) && empty($address) && empty($postalCode))) {
                throw new \InvalidArgumentException(
                    'Missing mandatory parameter, expecting checkoutId, city, address & postalCode.'
                );
            }

            /** @var mixed[] $response */
            $response = [
                'relays' => $pudoProvider->getPudoList($checkoutId, $city, $postalCode, $address),
            ];
            $status   = Response::HTTP_OK;
        } catch (\InvalidArgumentException $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
            $status   = Response::HTTP_BAD_REQUEST;
        } catch (\Throwable $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
            $status   = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse($response, $status);
    }
}
