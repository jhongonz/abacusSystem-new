<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:28:36
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use App\Traits\MultimediaTrait;
use App\Traits\UtilsDateTimeTrait;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class CreateInstitutionOrchestrator extends InstitutionOrchestrator
{
    use MultimediaTrait;
    use UtilsDateTimeTrait;

    public function __construct(
        InstitutionManagementContract $institutionManagement,
        ImageManagerInterface $imageManager
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
        $dataInstitution = [
            'id' => $request->input('institutionId'),
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'shortname' => $request->input('shortname'),
            'observations' => $request->input('observations'),
            'createdAt' => $this->getCurrentTime(),
        ];

        $token = $request->input('token');
        if (! is_null($token)) {
            $filename = $this->saveImage($token);
            $dataInstitution['logo'] = $filename;
        }

        return $this->institutionManagement->createInstitution([Institution::TYPE => $dataInstitution]);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'create-institution';
    }
}
