<?php

namespace PhpTwinfield\ApiConnectors;

use PhpTwinfield\Office;
use PhpTwinfield\Services\FinderService;

/**
 * A facade to make interaction with the the Twinfield service easier when trying to retrieve or send information about
 * offices.
 *
 * If you require more complex interactions or a heavier amount of control over the requests to/from then look inside
 * the methods or see the advanced guide detailing the required usages.
 *
 * @author Emile Bons <emile@emilebons.nl>
 */
class OfficeApiConnector extends BaseApiConnector
{
    /**
     * List all offices.
     *
     * @param string $pattern  The search pattern. May contain wildcards * and ?
     * @param int    $field    The search field determines which field or fields will be searched. The available fields
     *                         depends on the finder type. Passing a value outside the specified values will cause an
     *                         error.
     * @param int    $firstRow First row to return, useful for paging
     * @param int    $maxRows  Maximum number of rows to return, useful for paging
     * @param array  $options  The Finder options. Passing an unsupported name or value causes an error. It's possible
     *                         to add multiple options. An option name may be used once, specifying an option multiple
     *                         times will cause an error.
     *
     * @return Office[] The offices found.
     */
    public function listAll(
        string $pattern = '*',
        int $field = 0,
        int $firstRow = 1,
        int $maxRows = 100,
        array $options = []
    ): array {
        $response = $this->getFinderService()->searchFinder(FinderService::TYPE_OFFICES, $pattern, $field, $firstRow, $maxRows, $options);

        if ($response->data->TotalRows == 0) {
            return [];
        }

        $offices = [];
        foreach ($response->data->Items->ArrayOfString as $officeArray) {
            $office = new Office();
            $office->setCode($officeArray->string[0]);
            $office->setCountryCode($officeArray->string[2]);
            $office->setName($officeArray->string[1]);
            $offices[] = $office;
        }

        return $offices;
    }
}
