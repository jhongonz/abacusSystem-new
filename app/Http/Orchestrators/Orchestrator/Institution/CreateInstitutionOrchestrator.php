<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:28:36
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use App\Traits\MultimediaTrait;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class CreateInstitutionOrchestrator extends InstitutionOrchestrator
{
    use MultimediaTrait;

    public function __construct(
        InstitutionManagementContract $institutionManagement,
        protected ImageManagerInterface $imageManager,
    ) {
        parent::__construct($institutionManagement);
        $this->setImageManager($imageManager);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $dataInstitution = [
            'id' => $request->integer('institutionId'),
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'shortname' => $request->input('shortname'),
            'observations' => $request->input('observations'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'state' => ValueObjectStatus::STATE_NEW,
        ];

        if ($request->filled('token')) {
            $filename = $this->saveImage($request->string('token'));
            $dataInstitution['logo'] = $filename;
        }

        $institution = $this->institutionManagement->createInstitution([Institution::TYPE => $dataInstitution]);

        return ['institution' => $institution];
    }

    public function canOrchestrate(): string
    {
        return 'create-institution';
    }
}
