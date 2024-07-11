<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Controller\Frontend;

use Dnd\Bundle\DpdFranceShippingBundle\Provider\PudoProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
#[\Symfony\Component\Routing\Attribute\Route(path: '/dpd_france')]
class RelayController extends AbstractController
{
    /**
     * Returns a list of nearby pudos
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/relays', name: 'dpd_france_relay_list', methods: ['GET', 'POST'])]
    public function listAction(Request $request, PudoProvider $pudoProvider): JsonResponse
    {
        $checkoutId = $request->get('checkoutId');
        $city = $request->get('city');
        $address = $request->get('address');
        $postalCode = $request->get('postalCode');
        try {
            if (empty($checkoutId) || (empty($city) || empty($postalCode))) {
                throw new \InvalidArgumentException(
                    'Missing mandatory parameter, expecting checkoutId, city & postalCode.'
                );
            }
            $response = [
                'relays' => $pudoProvider->getPudoList($checkoutId, $city, $postalCode, $address),
            ];
            $status = Response::HTTP_OK;
        } catch (\InvalidArgumentException $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
            $status = Response::HTTP_BAD_REQUEST;
        } catch (TransportExceptionInterface | \Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse($response, $status);
    }

    /**
     * Returns all them details about a pudo
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/relay/{pudoId}', name: 'dpd_france_relay_details', methods: ['GET'])]
    public function detailsAction(string $pudoId, PudoProvider $pudoProvider): JsonResponse
    {
        try {
            $response = [
                'details' => $pudoProvider->getPudoDetails($pudoId),
            ];
            $status = Response::HTTP_OK;
        } catch (\InvalidArgumentException $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
            $status = Response::HTTP_BAD_REQUEST;
        } catch (TransportExceptionInterface | \Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse($response, $status);
    }
}
