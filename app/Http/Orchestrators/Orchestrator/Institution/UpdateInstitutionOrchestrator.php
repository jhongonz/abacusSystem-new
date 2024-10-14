<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:34:51
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use App\Traits\MultimediaTrait;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class UpdateInstitutionOrchestrator extends InstitutionOrchestrator
{
    use MultimediaTrait;

    public function __construct(
        InstitutionManagementContract $institutionManagement,
        ImageManagerInterface $imageManager,
    ) {
        parent::__construct($institutionManagement);
        $this->setImageManager($imageManager);
    }

    /**
     * @param Request $request
     * @return Institution
     */
    public function make(Request $request): Institution
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
            $filename = $this->saveImage($request->input('token'));
            $dataUpdate['logo'] = $filename;
        }

        return $this->institutionManagement->updateInstitution($request->input('institutionId'), $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-institution';
    }
}
