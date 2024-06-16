<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:20:09
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DetailInstitutionOrchestrator extends InstitutionOrchestrator
{
    private const IMAGE_PATH_FULL = '/images/full/';

    public function __construct(
        InstitutionManagementContract $institutionManagement
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function make(Request $request): array
    {
        $institutionId = $request->input('institutionId');
        if (! is_null($institutionId)) {

            /** @var Institution $institution */
            $institution = $this->institutionManagement->searchInstitutionById($institutionId);

            $urlFile = url(self::IMAGE_PATH_FULL.$institution->logo()->value().'?v='.Str::random(10));
        }

        return [
            'institutionId' => $institutionId,
            'institution' => $institution ?? null,
            'contactCard' => (isset($institution)) ? $institution->contactCard() : null,
            'image' => $urlFile ?? null
        ];
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'detail-institution';
    }
}
