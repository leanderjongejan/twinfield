<?php

namespace PhpTwinfield\ApiConnectors;

use PhpTwinfield\Request as Request;
use PhpTwinfield\Office;

/**
 * A facade to make interaction with the the Twinfield service easier when trying to retrieve or send information about
 * offices.
 *
 * If you require more complex interactions or a heavier amount of control over the requests to/from then look inside
 * the methods or see the advanced guide detailing the required usages.
 *
 * @author Emile Bons <emile@emilebons.nl>
 */
class BalanceSheetApiConnector extends ProcessXmlApiConnector
{
    /**
     * Requests all balance sheets from the List Dimension Type.
     *
     * @param Office $office
     * @param string $dimType
     * @return array A multidimensional array in the following form:
     *               [$balanceSheetId => ['name' => $name, 'shortName' => $shortName], ...]
     */
    public function listAll(Office $office, $dimType = 'BAS'): array
    {
        // Make a request to a list of all customers
        $requestBalanceSheets = new Request\Catalog\Dimension($office, $dimType);

        // Send the Request document and set the response to this instance.
        $response = $this->sendDocument($requestBalanceSheets);

        // Get the raw response document
        $responseDOM = $response->getResponseDocument();

        // Prepared empty balancesheet array
        $balanceSheets = [];

        // Store in an array by customer id
        foreach ($responseDOM->getElementsByTagName('dimension') as $balanceSheet) {

            $balanceSheetId = $balanceSheet->textContent;

            if (!is_numeric($balanceSheetId)) {
                continue;
            }

            $balanceSheets[$balanceSheet->textContent] = [
                'code' => $balanceSheet->textContent,
                'name' => $balanceSheet->getAttribute('name'),
                'shortName' => $balanceSheet->getAttribute('shortName'),
            ];

        }

        return $balanceSheets;
    }
}
