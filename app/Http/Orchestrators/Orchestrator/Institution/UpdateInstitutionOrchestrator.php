<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:34:51
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use App\Traits\MultimediaTrait;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class UpdateInstitutionOrchestrator extends InstitutionOrchestrator
{
    use MultimediaTrait;

    public function __construct(
        InstitutionManagementContract $institutionManagement,
        protected ImageManagerInterface $imageManager,
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $dataUpdate = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'shortname' => $request->input('shortname'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
        ];

        if ($request->filled('token')) {
            $filename = $this->saveImage($request->string('token'));
            $dataUpdate['logo'] = $filename;
        }

        $institution = $this->institutionManagement->updateInstitution($request->integer('institutionId'), $dataUpdate);

        return ['institution' => $institution];
    }

    public function canOrchestrate(): string
    {
        return 'update-institution';
    }
}
