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
        InstitutionManagementContract $institutionManagement,
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $institutionId = $request->integer('institutionId') ?: null;

        $institution = null;
        if (!is_null($institutionId)) {
            $institution = $this->institutionManagement->searchInstitutionById($institutionId);
        }

        if ($institution instanceof Institution) {
            $path = sprintf('%s%s?v=%s', self::IMAGE_PATH_FULL, $institution->logo()->value(), Str::random());
            $urlFile = url($path);
        }

        return [
            'institutionId' => $institutionId,
            'institution' => $institution,
            'image' => $urlFile ?? null
        ];
    }

    public function canOrchestrate(): string
    {
        return 'detail-institution';
    }
}
